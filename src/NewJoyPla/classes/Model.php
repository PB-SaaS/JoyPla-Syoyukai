<?php

use App\Lib\UserInfo;
use App\Lib\SpiralDataBase;

use ApiErrorCode\FactoryApiErrorCode;

class Model
{
    private static $instances = [];
    public static $guarded = [];
    public static $fillable = [];
    public static $attributes = [];
    public static $primary_key = null;
    
    public function __construct()
    {
        global $SPIRAL;
        $spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
        $spiralApiRequest = new SpiralApiRequest();
        $this->spiralDataBase = new SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
        
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
        return $instance;
    }

    public static function orWhere(string $field , string $val , string $op = '=')
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->addSearchCondition($field , $val , $op , 'or');
        return $instance;
    }

    public static function get()
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->setDataBase($instance::$spiral_db_name);
        $instance->spiralDataBase->addSelectFieldsToArray($instance::$fillable);
        $result = $instance->spiralDataBase->doSelectLoop();
        if($result['count'] > 0){
            $result['data'] = $instance->spiralDataBase->arrayToNameArray($result['data'],$instance::$fillable);
            $result['data'] = $instance->getDataToInstance($result['data']);
        }
        
        if($result['code'] != 0)
        {
            throw new Exception(FactoryApiErrorCode::factory((int)$result['code'])->getMessage(),FactoryApiErrorCode::factory((int)$result['code'])->getCode());
        }
        
        return new Collection($result);
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
            $update_array[] = ['name' => $field , 'value' => $val];
        }

        return $update_array;
    }

    public static function bulkUpdate(String $key ,array $update_data)
    {
        $instance = self::getInstance();
        $instance->spiralDataBase->setDataBase($instance::$spiral_db_name);
        $update_data = $instance->makeBulkUpdateArray($update_data);
        $result = $instance->spiralDataBase->doBulkUpdate($key , $update_data['fillable'], $update_data['data']);
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
        $update_fillable = $this::$fillable;

        foreach($update_data as $index => $data)
        {
            foreach($this::$fillable as $column)
            {
                $def = '';
                if(static::CREATED_AT == $column){
                    $update_fillable = array_diff($update_fillable, array(static::CREATED_AT));
                    $update_fillable = array_values($update_fillable);
                    continue;
                }
                if(static::UPDATED_AT == $column){
                    $def = 'now';
                }
                $update_array[$index][] = (isset($data[$column]))? $data[$column] : ((isset($this::$attributes[$column]))? $attributes[$column]: $def) ;
            }
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
            $create_array[] = ['name' => $field , 'value' => $val];
        }

        foreach($create_data as $field => $val)
        {
            $create_array[] = ['name' => $field , 'value' => $val];
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
                $create_array[$index][] = (isset($data[$column]))? $data[$column] : ((isset($this::$attributes[$column]))? $attributes[$column]: $def) ;
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
                $save_array[] = ['name' => $field , 'value' => $this->{$field}];
            }
        }

        return $save_array;
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