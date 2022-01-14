<?php

use App\Lib\UserInfo;
use App\Lib\SpiralDataBase;
use App\Lib\SpiralSendMail;
use App\Lib\SpiralDBFilter;

use ApiErrorCode\FactoryApiErrorCode;


class Model
{
    private static $instances = [];
    public static $guarded = [];
    public static $fillable = [];
    public static $select = [];
    public static $attributes = [];
    public static $primary_key = null;
    public static $page = 1;
    public $sort = [];
    
    public function __construct()
    {
        global $SPIRAL;
        $spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
        $spiralApiRequest = new SpiralApiRequest();
        $this->spiralDataBase = new SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
        if(isset($this::$mail_field_title) && $this::$mail_field_title != null)
        {
            $this->spiralSendMail = new SpiralSendMail($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
            $this->spiralDBFilter = new SpiralDBFilter($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
        }
        foreach($this::$guarded as $column)
        {
            $this->{$column} = null;
        }
        foreach($this::$fillable as $column)
        {
            $this->{$column} = null;
        }
    }
    
    private function getDataToInstance(array $data)
    {
        $result = [];
        foreach($data as $key => $val)
        {
            $instance = new static;
            foreach($val as $column => $value)
            {
                $instance->{$column} = $value;
            }
            $result[$key] = $instance;
        }
        return $result;
    }

    public static function getNewInstance()
    {
        $instance = new static;
        self::$instances[static::class] = $instance;
        return self::$instances[static::class];
    }
    
    public static function getInstance()
    {
        if (!isset(self::$instances[static::class])) {
            $instance = new static;
            self::$instances[static::class] = $instance;
        }
        return self::$instances[static::class];
    }

    public function requestUrldecode(array $array): array
    {
		$result = array();
		foreach($array as $key => $value){
			if(is_array($value)){
				$result[$key] = $this->requestUrldecode($value);
			} else {
				$result[$key] = urldecode($value);
			}
		}
		return $result;
	}

    public static function destroy($ids)
    {
        $instance = self::getInstance();
        if(is_array($ids))
        {
            foreach($ids as $id)
            {
                $instance::orWhere('id',$id);
            }
        } 
        else 
        {
            $instance::where('id',$ids);
        }
        return $instance->delete();
    }

    public static function find(int $id)
    {   
        $instance = self::getInstance();
        $instance->spiralDataBase->addSearchCondition('id', $id);
        return $instance;
    }

    public static function where(string $field , string $val , string $op = '=')
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->addSearchCondition($field , $val , $op);
        $instance->search[] = [$field , $val , $op , 'and'];
        return $instance;
    }

    public static function orWhere(string $field , string $val , string $op = '=')
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->addSearchCondition($field , $val , $op , 'or');
        $instance->search[] = [$field , $val , $op , 'or'];
        return $instance;
    }
    
    public static function whereDeleted()
    {
        $instance = self::getInstance();
        
        if($instance::DELETED_AT !== '')
        {
            $instance->orWhere($instance::DELETED_AT,'1')->orWhere($instance::DELETED_AT,'0')->orWhere($instance::DELETED_AT,'0','ISNULL');
            $instance->search[] = [$instance::DELETED_AT , '1' , '!=' , 'and']; 
        }
        return $instance;
    }

    public static function get()
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->setDataBase($instance::$spiral_db_name);
        
        foreach($instance->sort as $sort)
        {
            $instance->spiralDataBase->addSortField($sort['name'],$sort['order']);
        }
        
        $delete_field_flg = false;
        if($instance::DELETED_AT !== '')
        {
            foreach($instance->search as $field)
            {
                if($field[0] === $instance::DELETED_AT)
                {
                    $delete_field_flg = true;
                }
            }
        }
        
        if(!$delete_field_flg)
        {
            $instance->orWhere($instance::DELETED_AT , false , 'ISNULL');
            $instance->orWhere($instance::DELETED_AT , 0 , '=');
        }
        
        $column = array_merge($instance::$fillable,$instance::$guarded);
        $instance->spiralDataBase->addSelectFieldsToArray($column);
        $result = $instance->spiralDataBase->doSelectLoop();
        if($result['label']){
            $result['label'] = $instance->spiralDataBase->labelToNameArray($result['label'],$column);
        }
        if($result['count'] > 0){
            $result['data'] = $instance->spiralDataBase->arrayToNameArray($result['data'],$column);
            $result['data'] = $instance->getDataToInstance($result['data']);
        }
        
        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        
        return new Collection($result);
    }
    
    public static function sort(string $fieldTitle , string $order = 'asc')
    {
        $instance = self::getInstance();
        $column = array_merge($instance::$fillable,$instance::$guarded);
        if(array_search($fieldTitle, $column , true) === false){ $fieldTitle = 'id'; $order = 'asc';}
        if($order == 'asc' || $order == 'desc'){
            $instance->sort[] = array( 'name' => $fieldTitle , 'order' => $order );
        }
        return $instance;
    }

    public static function groupby(string $fieldTitle)
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->setGroupByField($fieldTitle);
        return $instance;
    }
    
    public static function sortLink(string $fieldTitle)
    {
        $instance = self::getInstance();
        $linkparam = [];
        $checked = false;
        foreach($instance->sort as $sort)
        {
            $order = 'asc';
            if($sort['name'] == $fieldTitle)
            {
                $checked = true;
                if($sort['order'] == 'asc'){
                    $order = 'desc';
                }
                $linkparam[] = [$fieldTitle => "desc"];
            }
        }
        if(!$checked)
        {
            $linkparam[] = [$fieldTitle => "desc"];
        }
        return http_build_query($linkparam);
    }
    
    public static function paginate(int $lines_per_page)
    {
        $instance = self::getInstance();
        foreach($instance->sort as $sort)
        {
            $instance->spiralDataBase->addSortField($sort['name'],$sort['order']);
        }
        $instance->spiralDataBase->setDataBase($instance::$spiral_db_name);
        $column = array_merge($instance::$fillable,$instance::$guarded);
        $instance->spiralDataBase->addSelectFieldsToArray($column);
        $instance->spiralDataBase->setLinesPerPage($lines_per_page);
        $result = $instance->spiralDataBase->doSelect();
        if($result['label']){
            $result['label'] = $instance->spiralDataBase->labelToNameArray($result['label'],$column);
        }
        if($result['count'] > 0){
            $result['data'] = $instance->spiralDataBase->arrayToNameArray($result['data'],$column);
            $result['data'] = $instance->getDataToInstance($result['data']);
        }
        
        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        return new Collection($result);
    }
    
    public static function page(int $page)
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->setPage($page);
        $instance->page = $page;
        return $instance;
    }
    
    public static function current_page()
    {
        $instance = self::getInstance();
        return $instance->page;
    }
    
    public static function count()
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->setDataBase($instance::$spiral_db_name);
        $column = array_merge($instance::$fillable,$instance::$guarded);
        $instance->spiralDataBase->setLinesPerPage(1);
        $result = $instance->spiralDataBase->doSelect();
        
        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        return (int)$result['count'];
    }

    public static function update(array $update_data)
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->setDataBase($instance::$spiral_db_name);
        $update_data = $instance->makeUpdateArray($update_data);
        $result = $instance->spiralDataBase->doUpdate($update_data);
        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        return new Collection($result);
    }

    private function makeUpdateArray(array $update_data)
    {
        $update_array = [];
        $update_array[] = ['name'=>static::UPDATED_AT , 'value'=>'now'];

        foreach($update_data as $field => $val)
        {
            $update_array[] = ['name' => $field , 'value' => (string)$val];
        }

        return $update_array;
    }

    public static function bulkUpdate(String $key ,array $update_data)
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->setDataBase($instance::$spiral_db_name);
        $update_data = $instance->makeBulkUpdateArray($update_data);
        $result = $instance->spiralDataBase->doBulkUpdate($key , $update_data['fillable'], $update_data['data']);
        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        return new Collection($result);
    }

    public static function delete()
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->setDataBase($instance::$spiral_db_name);
        $result = $instance->spiralDataBase->doDelete();
        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        return new Collection($result);
    }

    private function makeBulkUpdateArray(array $update_data)
    {
        $update_array = [];
        $update_fillable = [];

        foreach($update_data as $index => $data)
        {
            $datas = [];
            foreach($this::$fillable as $column)
            {
                if(static::UPDATED_AT == $column){
                    if(! in_array($column, $update_fillable)) {
                        $update_fillable[] = (string)$column;
                    }
                    $datas[] = 'now' ;
                }
                else if(array_key_exists($column, $data))
                {
                    if(! in_array($column, $update_fillable)) {
                        $update_fillable[] = (string)$column;
                    }
                    $datas[] = (string)$data[$column];
                }
            }
            $update_array[] = $datas;
        }
        $result = [
            'data' => $update_array,
            'fillable' => $update_fillable,
        ];
        return $result;
    }

    public static function create(array $create_data)
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->setDataBase($instance::$spiral_db_name);
        $create_data = $instance->makeCreateArray($create_data);
        $result = $instance->spiralDataBase->doInsert($create_data);

        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        return new Collection($result);
    }

    private function makeCreateArray(array $create_data)
    {
        $create_array = [];
        $create_array[] = ['name'=>static::CREATED_AT , 'value'=>'now'];

        foreach($this::$attributes as $field => $val)
        {
            $create_array[] = ['name' => $field , 'value' => (string)$val];
        }

        foreach($create_data as $field => $val)
        {
            $create_array[] = ['name' => $field , 'value' => (string)$val];
        }

        return $create_array;
    }


    public static function upsert( $key , array $upsert_data)
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->setDataBase($instance::$spiral_db_name);
        $upsert_data = $instance->makeUpsertArray($upsert_data);
        $result = $instance->spiralDataBase->doBulkUpsert($key , $instance::$fillable, $upsert_data);
        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        return new Collection($result);
    }

    private function makeUpsertArray(array $upsert_data)
    {
        $create_array = [];
        foreach($upsert_data as $index => $data)
        {
            $create_array[$index] = [];
            foreach($this::$fillable as $column)
            {
                $def = '';
                if(static::UPDATED_AT == $column){
                    $def = 'now';
                }
                $create_array[$index][] = (isset($data[$column]))? (string)$data[$column] : ((isset($this::$attributes[$column]))? (string)$attributes[$column]: (string)$def) ;
            }
        }
        return $create_array;
    }

    
    public static function insert(array $insert_data)
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->setDataBase($instance::$spiral_db_name);
        $insert_data = $instance->makeInsertArray($insert_data);
        $result = $instance->spiralDataBase->doBulkInsert($instance::$fillable,$insert_data);
        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        return new Collection($result);
    }

    private function makeInsertArray(array $insert_data)
    {
        $create_array = [];
        foreach($insert_data as $index => $data)
        {
            $create_array[$index] = [];
            foreach($this::$fillable as $column)
            {
                $def = '';
                if(static::CREATED_AT == $column){
                    $def = 'now';
                }
                $create_array[$index][] = (isset($data[$column]))? (string)$data[$column] : ((isset($this::$attributes[$column]))? (string)$attributes[$column]: (string)$def) ;
            }
        }
        return $create_array;
    }


    public function save()
    {
        $this->spiralDataBase->setDataBase($this::$spiral_db_name);
        $this->spiralDataBase->addSearchCondition($this::$primary_key,$this->{$this::$primary_key});
        $save_data = $this->makeSaveArray();
        $result = $this->spiralDataBase->doUpsert($this::$primary_key,$save_data);
        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        return new Collection($result);
    }

    private function makeSaveArray()
    {
        $save_array = [];

        foreach($this::$fillable as $field)
        {
            if(static::CREATED_AT == $field) {continue;}
            if($this->{$field} === null) {continue;}
            if(static::UPDATED_AT == $field)
            {
                $save_array[] = ['name'=>$field , 'value'=>'now'];
            } 
            else 
            {
                $save_array[] = ['name' => $field , 'value' => (string)$this->{$field}];
            }
        }

        return $save_array;
    }

    /****
     * Exprerss 2 Mail
     */
    public static function body(String $view)
    {
        $instance = self::getInstance();
        $instance->spiralSendMail->addBodyText($view);
        return $instance;
    }

    public static function subject(String $subject)
    {
        $instance = self::getInstance();
        $instance->spiralSendMail->addSubject($subject);
        return $instance;
    }

    public static function from(String $mail , String $name = null)
    {
        $instance = self::getInstance();
        $instance->spiralSendMail->addFromAddress($mail);
        $instance->spiralSendMail->addFromName($name);
        return $instance;
    }

    public static function replyTo(String $mail)
    {
        $instance = self::getInstance();
        $instance->spiralSendMail->addReplyTo($mail);
        return $instance;
    }

    public static function selectRule(string $rule_name)
    {
        $instance = self::getInstance();
        $instance->spiralSendMail->addSelectName($rule_name);
        return $instance;
    }

    public static function send()
    {
        $instance = self::getInstance();
        $instance->spiralSendMail->setDataBase($instance::$spiral_db_name);
        $instance->spiralSendMail->addMailFieldTitle($instance::$mail_field_title);
        $instance->spiralSendMail->addReserveDate('now');
        $result = $instance->spiralSendMail->regist();
        
        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        return new Collection($result);
    }

    /**
     * Filter
     */
    public static function selectName(String $name)
    {
        $instance = self::getInstance();
        $instance->spiralDBFilter->addSelectName($name);
        return $instance;
    }

    public static function rule(array $rule)
    {
        $instance = self::getInstance();
        $instance->spiralDBFilter->addFields($rule);
        return $instance;
    }

    public static function filterCreate()
    {
        $instance = self::getInstance();
        $instance->spiralDBFilter->setDataBase($instance::$spiral_db_name);
        $result = $instance->spiralDBFilter->create();
        
        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        return new Collection($result);
    }



    public function logging(string $message, string $file_name = 'pdo.log')
    {
        /*
        $Logger = new Logger('logger');
        $Logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/' . $file_name, Logger::INFO));
        $Logger->addInfo($message);
        */
    }
}