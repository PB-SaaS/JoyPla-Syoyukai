<?php

namespace App\Lib;

class SpiralDataBase {

    protected $database;
    protected $selectColumns = array();
    protected $searchCondition = array();
    protected $selectName = '';
    protected $sort = array();
	protected $groupBy = array();
	protected $page = 1;
	protected $linesPerPage = 1000;
	protected $apiSpiral ;
	protected $dataformat = [] ;

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

	public function dataformat(array $dataformat)
	{
		$this->dataformat = $dataformat;
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

    public function setGroupByField(string $field){
		$this->groupBy[] = $field;
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
		$parameters['dataformat'] = $this->dataformat;
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
		$parameters['data'] = $insertData;
		$this->clearData();

		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	
	public function doBulkInsert(array $columns ,array $insertData){
		$apiHeader = array("database","bulk_insert");
		$parameters['db_title'] = $this->database;
		$parameters['columns'] = $columns;
		foreach(array_chunk($insertData , 1000) as $ary)
		{
			$parameters['data'] = $ary;
			$result = $this->apiSpiral->requestAPI($apiHeader, $parameters);
			if($result['code'] != "0")
			{
				return $result;
			}
		}
		$this->clearData();

		return $result;
	}
	
	public function doUpdate(array $updateData){
		$apiHeader = array("database","update");
		$parameters['db_title'] = $this->database;
		$parameters['select_name'] = $this->selectName;
		$parameters['search_condition'] = $this->searchCondition;
		$parameters['data'] = $updateData;
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
		foreach(array_chunk($updateData , 1000) as $ary)
		{
			$parameters['data'] = $ary;
			$result = $this->apiSpiral->requestAPI($apiHeader, $parameters);
			if($result['code'] != "0")
			{
				return $result;
			}
		}
		$this->clearData();

		return $result;
	}

	public function doUpsert(string $keyTitle ,array $upsertData){
		$apiHeader = array("database","upsert");
		$parameters['db_title'] = $this->database;
		$parameters['key'] = $keyTitle;
		$parameters['data'] = $upsertData;
		$this->clearData();

		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	public function doBulkUpsert(string $keyTitle ,array $columns ,array $bulkData){
		$apiHeader = array("database","bulk_upsert");
		$parameters['db_title'] = $this->database;
		$parameters['key'] = $keyTitle;
		$parameters['columns'] = $columns;
		foreach(array_chunk($bulkData , 1000) as $ary)
		{
			$parameters['data'] = $ary;
			$result = $this->apiSpiral->requestAPI($apiHeader, $parameters);
			if($result['code'] != "0")
			{
				return $result;
			}
		}
		$this->clearData();

		return $result;
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
		return array( 'data' => $resdata , 'count' => $response['count'] , 'code' => '0' ,'message' => $response['message'] , 'label' => $response['label']);
	}

	private function clearData(){
		$this->database = "";
		$this->selectColumns = array();
		$this->searchCondition = array();
		$this->selectName = "";
		$this->sort = array();
		$this->groupBy = array();
		$this->page = 1;
		$this->linesPerPage = 1000;
	}

	public function labelToNameArray(array $label , array $selectColumns){
		$result = array();
		foreach($selectColumns as $key => $data){
			if($data == 'id'){ continue; }
			$result[$data] = $label[$key];
		}
		return $result;
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
}