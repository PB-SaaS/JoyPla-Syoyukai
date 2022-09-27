<?php

namespace framework\SpiralConnecter;

use App\Model\Lot;
use Collator;
use Collection;
use framework\Exception\NotFoundException;
use HttpRequestParameter;
use LogicException;

class SpiralManager {

    private $connection;
    private ?HttpRequestParameter $request = null;
    private array $searchCondition = [];
    private array $fields = [];
    private array $defaultFields = [];
    private ?OrderBy $orderBy = null;
    private string $dataformat = '';
    private string $labelsTarget = '';
    private string $selectName = '';
    private string $dbAsName = '';
    private array $leftJoin = [];
    private array $joinDbs = [];
    private ?Collection $mstData = null;
    
    public function __construct(?SpiralConnecterInterface $connector = null)
    {
        if(is_null($connector))
        {
            $this->connection = SpiralDB::getConnection();
        } else {
            $this->connection = $connector;
        } 
        $this->request = new HttpRequestParameter();
        $this->page(1);
    }

    public function fields($fields)
    {
        $this->defaultFields = $fields;
        return $this;
    }

    public function setTitle($title)
    {
        $this->request->set('db_title' , $title);
        return $this;
    }

    public function getTitle()
    {
        return $this->request->get('db_title');
    }

    public function setDbAsName($dbAsName)
    {
        $this->dbAsName = $dbAsName;
    }

    public function getDbAsName()
    {
        return $this->dbAsName;
    }

    public function where(string $field , string $value , string $operator = "=")
    {
        $this->searchCondition[] = new SearchCondition($field , $value , $operator);

        $this->request->set('search_condition' , array_map(function(SearchCondition $searchCondition){
            return $searchCondition->getRequestParam();
        },$this->searchCondition));

        return $this;
    }

    public function orWhere(string $field ,string $value ,string $operator = "=")
    {
        
        $this->searchCondition[] = new SearchCondition($field , $value , $operator , false);

        $this->request->set('search_condition' , array_map(function(SearchCondition $searchCondition){
            return $searchCondition->getRequestParam();
        },$this->searchCondition));

        return $this;
    }

    public function whereIn(string $field , array $values )
    {
        foreach($values as $value)
        {
            $this->orWhere($field , $value , '=');
        }
        return $this;
    }

    public function whereNotIn(string $field , array $values )
    {
        foreach($values as $value)
        {
            $this->where($field , $value , '!=');
        }
        return $this;
    }

    public function whereNull(string $field)
    {
        $this->where($field , '0' , 'ISNULL' );
        return $this;
    }

    public function whereNotNull(string $field)
    {
        $this->where($field , '0' , 'ISNOTNULL' );
        return $this;
    }

    public function orderBy(string $field , string $ascOrDesc)
    {
        $this->orderBy = new OrderBy($field , $ascOrDesc);
        $this->request->set('sort' , $this->orderBy->getRequestParam());
        return $this;
    }

    public function value( $value )
    {
        if(is_array($value))
        {
            $this->fields = array_merge($this->fields , $value);
        }
        
        if(is_string($value))
        {
            $this->fields[] = $value;
        }

        $this->request->set('select_columns' , $this->fields);

        return $this;
    }

    private function combine($keys , $values)
    {
        foreach($values as &$val)
        {
            $val = array_combine($keys, $val);
        }
        return $values;
    }

    public function on( string $joinKey , string $joinOperator , string $leftKey )
    {
        $this->leftJoin['joinKey'] = $joinKey;
        $this->leftJoin['leftKey'] = $leftKey;
        $this->leftJoin['joinOperator'] = $joinOperator;

        if(!is_null($this->mstData))
        {
            foreach($this->mstData as $m)
            {
                $this->orWhere($joinKey , $m->{$leftKey} , $joinOperator);
            }
        }
        return $this;
    }

    public function leftJoin(string $joinTitle , $joinKey , ?string $joinOperator = "" , ?string $leftKey = "" , ?array $fields = [])
    {
        SpiralDB::setConnecter($this->connection);
        if(class_exists($joinTitle))
        {
            $db = ( new $joinTitle()) ->init();
            if(! empty($joinTitle::$dbAsName) )
            {
                $db->setDbAsName($joinTitle::$dbAsName);
            }
        }
        else 
        {
            $db = SpiralDB::title($joinTitle);
        }
        $this->joinDbs[] = $db;
        if(is_callable($joinKey))
        {
            $db->leftJoin = [ 'title' => $this->request->get('db_title') , 'callable' => $joinKey , 'joinOperator' => $joinOperator , 'leftKey' => $leftKey , 'fields' => $fields , 'joinKey' => null];
            return $this;
        }
        $db->leftJoin = [ 'title' => $this->request->get('db_title') , 'joinKey' => $joinKey , 'joinOperator' => $joinOperator , 'leftKey' => $leftKey , 'fields' => $fields];
        return $this;
    }

    public function setMstData(Collection $col)
    {
        $this->mstData = $col;
    }

    public function getLeftJoin(Collection $collection)
    {
        $col = $collection->toArray();
        if(count($col) == 0)
        {
            return $collection;
        }

        foreach($this->joinDbs as $joinDb)
        {
            $joinDb->setMstData($collection);
            $j = $joinDb->leftJoin;
            if($j['title'] !== $this->request->get('db_title'))
            {
                throw new LogicException('title is Not Equal');
            }
            if(isset($j['callable']))
            {
                $j['callable']($joinDb);
            }
            else 
            {
                foreach( $col as $c )
                {
                    $joinDb->orWhere( $j['joinKey'] , $c[$j['leftKey']] , '=' );
                }

                $joinDb->value($j['fields']);
            }

            $j = $joinDb->leftJoin;

            $res = $joinDb->get();
            $res = $res->toArray();

            foreach( $col as &$c )
            {
                foreach( $res as $r)
                {
                    if( $c[$j['leftKey']] == $r[$j['joinKey']] )
                    {
                        $titleKey = $joinDb->getTitle();
                        if(! empty($joinDb->getDbAsName()))
                        {
                            $titleKey = $joinDb->getDbAsName();
                        }
                        $c[$titleKey] = $r;
                    }
                }
            }
        }

        
        return new Collection($col);
    }

    public function page(int $page)
    {
        if($page < 1)
        {
            throw new LogicException('page must be greater than or equal to 1', 501);
        }
        $this->request->set('page' , $page);
        return $this;
    }

    public function find( int $id )
    {
        $this->where('id',$id,'=');
        $res = $this->get();
        if(count($res->toArray()) === 0 )
        {
            return null;
        }
        return (new Collection($res))->first();
    }

    public function findOrFail( int $id )
    {
        $res = $this->find($id);
        if(is_null($res))
        {
            throw new NotFoundException('Not Found' , 404);
        }

        return $res;
    }

    public function paginate(int $limit = 1000)
    {
        if($limit > 1000 || $limit < 1)
        {
            throw new LogicException('limit must be greater than or equal to 1 and less than or equal to 1000 ', 501);
        }

        if(is_null($this->request->get('select_columns')))
        {
            if( count($this->defaultFields) > 0)
            {
                $this->value($this->defaultFields);
            }
            else
            {
                $this->value('id');
            }
        }

        $xSpiralApiHeader = new XSpiralApiHeaderObject('database','select');

        $this->request->set('lines_per_page' , $limit);

        $request = $this->connection->request($xSpiralApiHeader , $this->request);

        $res = $this->combine($this->request->get('select_columns') , $request['data'] );

        $res = new Collection($res);
        $res = $this->getLeftJoin($res);

        return new Paginator( 
            ( new Collection($res) ) , 
            (int)$this->request->get('page'),
            1,
            ceil($request['count'] / $limit ),
            $limit,
            $request['count']);
    }

    public function getMulti(array $fields = [])
    {
        
        $count = 1; // 1件以上ある想定
        $res = [];
        if(is_null($this->request->get('select_columns')))
        {
            if( count($this->defaultFields) > 0)
            {
                $this->value($this->defaultFields);
            }
            else
            {
                $this->value('id');
            }
        }

        $xSpiralApiHeader = new XSpiralApiHeaderObject('database','select');
        $this->value($fields);

        $result = $this->paginate(1);

        $count = $result->getTotal();

        $requests = [];

        $this->request->set('lines_per_page' , 1000);

        for($page = 1; $page <= ceil($count / 1000 ); $page++) {
            $request = $this->request->remake();
            $request->set('page' , $page);
            $requests[] = $request;
        }
        $res = $this->connection->bulkRequest($xSpiralApiHeader , $requests);

        $res = $this->combine($this->request->get('select_columns') , $res );
        return new Collection($res);
    }

    public function get( array $fields = [] )
    {
        $count = 1; // 1件以上ある想定
        $res = [];
        if(is_null($this->request->get('select_columns')))
        {
            if( count($this->defaultFields) > 0)
            {
                $this->value($this->defaultFields);
            }
            else
            {
                $this->value('id');
            }
        }

        $xSpiralApiHeader = new XSpiralApiHeaderObject('database','select');
        $this->value($fields);

        $this->request->set('lines_per_page' , 1000);
        $this->request->set('page' , 1);

        for($page = 1; $page <= ceil($count / 1000 ); $page++) {
            $this->request->set('page' , $page);
            $response = $this->connection->request($xSpiralApiHeader , $this->request);
            $count = (int)$response['count'];
            $res = array_merge($res, $response['data']);
        }

        $res = $this->combine($this->request->get('select_columns') , $res );
        $res = new Collection($res);
        $res = $this->getLeftJoin($res);
        return $res;
    }


    public function create(array $create)
    {
        $xSpiralApiHeader = new XSpiralApiHeaderObject('database','insert');

        $data = [];
        foreach($create as $key => $v)
        {
            $data[] = [ 'name' => $key , 'value' => $v ];
        }

        $this->request->set('data', $data);
        $res = $this->connection->request($xSpiralApiHeader , $this->request);

        $create['id'] = $res['id'];

        return new Collection($create);
    }

    public function insert($insert)
    {
        $xSpiralApiHeader = new XSpiralApiHeaderObject('database','bulk_insert');

        $fields = [];
        foreach($insert[0] as $key => $v)
        {
            $fields[] = $key;
        }
        
        $data = [];
        foreach($insert as $i)
        {
            $data[] = array_values($i);
        }

        $this->request->set('columns', $fields);

		foreach(array_chunk($data , 1000) as $d)
		{
            $this->request->set('data', $d);
            $this->connection->request($xSpiralApiHeader , $this->request);
        }

        return true;
    }

    public function update($update)
    {
        $xSpiralApiHeader = new XSpiralApiHeaderObject('database','update');

        $data = [];
        foreach($update as $key => $v)
        {
            $data[] = [ 'name' => $key , 'value' => $v ];
        }

        $this->request->set('data', $data);
        $res = $this->connection->request($xSpiralApiHeader , $this->request);

        return (int)$res['count'];
    }

    public function upsert($upsert)
    {

        $xSpiralApiHeader = new XSpiralApiHeaderObject('database','upsert');

        $data = [];
        foreach($upsert as $key => $v)
        {
            $data[] = [ 'name' => $key , 'value' => $v ];
        }

        $this->request->set('data', $data);
        $res = $this->connection->request($xSpiralApiHeader , $this->request);

        return (int)$res['count'];
    }

    public function delete()
    {
        $xSpiralApiHeader = new XSpiralApiHeaderObject('database','delete');
        $res = $this->connection->request($xSpiralApiHeader , $this->request);
        return (int)$res['count'];
    }

    public function destroy($id)
    {
        $xSpiralApiHeader = new XSpiralApiHeaderObject('database','delete');
        $this->where('id',$id,'=');
        $this->connection->request($xSpiralApiHeader , $this->request);
    }

    public function updateBulk($key , $update)
    {
        $xSpiralApiHeader = new XSpiralApiHeaderObject('database','bulk_update');

        $fields = [];
        foreach($update[0] as $key => $v)
        {
            $fields[] = $key;
        }
        
        $data = [];
        foreach($update as $i)
        {
            $data[] = array_values($i);
        }

        $this->request->set('columns', $fields);
        $this->request->set('key', $key);

        $count = 0;

		foreach(array_chunk($data , 1000) as $d)
		{
            $this->request->set('data', $d);
            $res = $this->connection->request($xSpiralApiHeader , $this->request);
            $count = $count + count($res['results']);
        }

        return $count;
    }

    public function upsertBulk($key , $upsert)
    {
        $xSpiralApiHeader = new XSpiralApiHeaderObject('database','bulk_upsert');

        $fields = [];
        foreach($upsert[0] as $key => $v)
        {
            $fields[] = $key;
        }
        
        $data = [];
        foreach($upsert as $i)
        {
            $data[] = array_values($i);
        }

        $this->request->set('columns', $fields);
        $this->request->set('key', $key);

        $count = 0;

		foreach(array_chunk($data , 1000) as $d)
		{
            $this->request->set('data', $d);
            $res = $this->connection->request($xSpiralApiHeader , $this->request);
            $count = $count + count($res['results']);
        }

        return $count;
    }

    public function schema()
    {
        $xSpiralApiHeader = new XSpiralApiHeaderObject('database','get');
        $response = $this->connection->request($xSpiralApiHeader , $this->request);
        return new Collection($response['schema']);
    }

    public function registedRecordCount()
    {
        $db = SpiralDB::title($this->request->get('db_title'))->paginate(1);
        return $db->getTotal();
    }

    public function reInstance()
    {
        return (new self($this->connection))->setTitle($this->request->get('db_title'));
    }
}

class SearchCondition {

    public string $field;
    public string $value;
    public string $operator;
    public bool $isAnd;

    public function __construct( string $field , string $value , string $operator = '=' , bool $isAnd = true)
    {
        $this->field = $field;
        $this->value = $value;
        $this->operator = $operator;
        $this->isAnd = $isAnd;
    }

    public function getRequestParam()
    {
        return [ 'name' => $this->field , 'value' => $this->value  , 'operator' => $this->operator, 'logical_connection' => ( $this->isAnd )? 'and' : 'or' ];
    }
}

class OrderBy {
    
    public string $field;
    public string $ascOrDesc;

    public function __construct( string $field , string $ascOrDesc)
    {
        $this->field = $field;

        if($ascOrDesc !== 'asc' && $ascOrDesc !== 'desc')
        {
            throw new LogicException('Please specify asc or desc');
        }
        $this->ascOrDesc = $ascOrDesc;
    }

    public function getRequestParam()
    {
        return [ 'name' => $this->field , 'order' => $this->ascOrDesc ];
    }
}