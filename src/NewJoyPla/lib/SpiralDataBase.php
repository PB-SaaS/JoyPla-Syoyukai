<?php

namespace App\Lib;

class SpiralDataBase {

    protected $database;
    protected $selectColumns = array();
    protected $searchCondition = array();
    protected $selectName;
    protected $sort = array();
	protected $groupBy = array();
	protected $page = 1;
	protected $linesPerPage = 10;
	protected $apiSpiral ;

	public function __construct(\Spiral $SPIRAL,\PbSpiralApiCommunicator $PbSpiralApiCommunicator ,\SpiralApiRequest $SpiralApiRequest){
		$this->spiral = $SPIRAL;
		$this->setApiSpiral($PbSpiralApiCommunicator,$SpiralApiRequest);
	}
	
	private function setApiSpiral(\PbSpiralApiCommunicator $PbSpiralApiCommunicator ,\SpiralApiRequest $SpiralApiRequest){
		//$this->spiral->setApiTokenTitle(APITITLE); //APIタイトル
		$this->apiSpiral = new \App\Lib\ApiSpiral($this->spiral);
		$this->apiSpiral->setApiCommunicator($PbSpiralApiCommunicator);
		$this->apiSpiral->setSpiralApiRequest($SpiralApiRequest);
	}
	
	public function addSelectFields(string ...$fields){
        $this->selectColumns = $fields;
	}
	
	public function addSelectFieldsToArray(array $fields){
        $this->selectColumns = $fields;
    }

    public function addSearchCondition(string $fieldTitle, string $value, string $operator = '=', string $connection  = 'and'){
        $this->searchCondition[] = array( 'name' => $fieldTitle , 'value' => $value , 'operator' => $operator , 'logical_connection' => $connection );
    }

    public function addSortField(string $fieldTitle , string $order ){
        $this->sort[] = array( 'name' => $fieldTitle , 'order' => $order );
	}
	
	public function addSelectNameCondition(string $selectName){
		$this->selectName = $selectName;
	}

    public function setGroupByFields(string ...$fields){
		$this->groupBy = $fields;
	}

	public function setDataBase(string $databaseTitle){
		$this->database = $databaseTitle;
	}

	public function setPage(int $page){
		$this->page = $page;
	}
	public function setLinesPerPage(int $linesPerPage){
		$this->linesPerPage = $linesPerPage;
	}

	public function doSelect(bool $flag = true){
		$apiHeader = array("database","select");
		$parameters = array();
		$parameters['db_title'] = $this->database;
		$parameters['select_columns'] = $this->selectColumns;
		$parameters['search_condition'] = $this->searchCondition;
		$parameters['sort'] = $this->sort;
		$parameters['group_by'] = $this->groupBy;
		$parameters['lines_per_page'] = $this->linesPerPage;
		$parameters['page'] = $this->page;
		if($flag){
			$this->clearData();
		}
		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	public function doInsert(array $insertData){
		$apiHeader = array("database","insert");
		$parameters['db_title'] = $this->database;
		$parameters['data'] = $this->obj2arr($insertData);
		$this->clearData();

		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	
	public function doBulkInsert(array $columns ,array $insertData){
		$apiHeader = array("database","bulk_insert");
		$parameters['db_title'] = $this->database;
		$parameters['columns'] = $columns;
		$parameters['data'] = $this->obj2arr($insertData);
		$this->clearData();

		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}
	
	public function doUpdate(array $updateData){
		$apiHeader = array("database","update");
		$parameters['db_title'] = $this->database;
		$parameters['select_name'] = $this->selectName;
		$parameters['search_condition'] = $this->searchCondition;
		$parameters['data'] = $this->obj2arr($updateData);
		$this->clearData();

		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	public function doDelete(){
		$apiHeader = array("database","delete");
		$parameters['db_title'] = $this->database;
		$parameters['select_name'] = $this->selectName;
		$parameters['search_condition'] = $this->searchCondition;
		$this->clearData();

		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	public function doBulkUpdate(string $keyTitle ,array $columns ,array $updateData){
		$apiHeader = array("database","bulk_update");
		$parameters['db_title'] = $this->database;
		$parameters['key'] = $keyTitle;
		$parameters['columns'] = $columns;
		$parameters['data'] = $this->obj2arr($updateData);
		$this->clearData();

		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	public function doUpsert(string $keyTitle ,array $upsertData){
		$apiHeader = array("database","upsert");
		$parameters['db_title'] = $this->database;
		$parameters['key'] = $keyTitle;
		$parameters['data'] = $this->obj2arr($upsertData);
		$this->clearData();

		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	public function doBulkUpsert(string $keyTitle ,array $columns ,array $bulkData){
		$apiHeader = array("database","bulk_upsert");
		$parameters['db_title'] = $this->database;
		$parameters['key'] = $keyTitle;
		$parameters['columns'] = $columns;
		$parameters['data'] = $this->obj2arr($bulkData);
		$this->clearData();
		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	public function doSelectLoop(){
		$count = 1; // 1件以上ある想定
		$this->setLinesPerPage(1000);
		$loopdata = array();
		$resdata = array();
		for($page = 1; $page <= ceil($count / $this->linesPerPage ); $page++) {
			$this->setPage($page);
			$flag = false;
			$response = $this->doSelect($flag);
			if($response['code'] != "0"){
				return $response;
			}
			$count = $response['count'];
			$resdata = array_merge($resdata, $response['data']);
		}
		$this->clearData();
		return array( 'data' => $resdata , 'count' => $response['count'] , 'code' => '0' ,'message' => $response['message']);
	}

	private function clearData(){
		$this->database = "";
		$this->selectColumns = array();
		$this->searchCondition = array();
		$this->selectName = "";
		$this->sort = array();
		$this->groupBy = array();
		$this->page = 1;
		$this->linesPerPage = 10;
	}

	
	public function arrayToNameArray(array $selectData , array $selectColumns){
		$result = array();
		foreach($selectData as $rnum => $record){
			$result[$rnum] = array();
			foreach($record as $key => $data){
				$result[$rnum][$selectColumns[$key]] = $data;
			}
		}
		return $result;
	}

	private function obj2arr($obj){
		if ( ! is_object($obj) && ! is_array($obj)  ) return htmlspecialchars($obj, ENT_QUOTES, "UTF-8");
	
		$arr = (array) $obj;
	
		foreach ( $arr as &$a )
		{
			$a = $this->obj2arr($a);
		}
		return $arr;
	}
}