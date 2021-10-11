<?php
class Collection
{

    private $array = [];
    
    public function __construct($ary)
    {
        $this->setVariable($ary);
    }

    private function setVariable($ary)
    {
        $tmp = [];
        foreach($ary as $key => $val)
        {
            if(is_array($val))
            {
                $this->{$key} = new Collection($val);
                $tmp[$key] = new Collection($val);
            } 
            else 
            {
                $this->{$key} = $val;
                $tmp[$key] = $val;
            }
        }
        $this->array = $tmp;
    }
    
    public function all()
    {
        return $this->array;
    }

    public function toArray()
    {
        $tmp = [];
        foreach($this->array as $key => $val)
        {
            if($val instanceof Collection)
            {
                $tmp[$key] = $val->all();
            }
            else
            {
                $tmp[$key] = $val;
            }
        }
        return $tmp;
    }

    public function count()
    {
        return count($this->array);
    }

    public function sum()
    {
        $sum_tmp = 0;
        foreach($this->array as $key => $val){
            if(!is_numeric($val)){ throw new Exception('not numeric'); }
            $sum_tmp += $val;
        }
        return $sum_tmp;
    }

    public function avg()
    {
        return $this->sum() / $this->count();
    }

    public function max()
    {
        return max($this->array);
    }

    public function min()
    {
        return min($this->array);
    }

    public function where($key , $val)
    {
        $tmp = [];
        foreach($this->array as $ary_key => $ary){
            if(isset($ary[$key]) && $ary[$key] === $val)
            {
                $tmp[$ary_key] = $ary;
            }
        }
        return new Collection($tmp);
    }

    public function whereIn($key , $val_in)
    {
        $tmp = [];
        foreach($this->array as $ary_key => $ary){
            if(isset($ary[$key]) && in_array($ary[$key], $val_in))
            {
                $tmp[$ary_key] = $ary;
            }
        }
        return new Collection($tmp);
    }

    public function whereNotIn($key , $val_in)
    {
        $tmp = [];
        foreach($this->array as $ary_key => $ary){
            if(isset($ary[$key]) && !in_array($ary[$key], $val_in))
            {
                $tmp[$ary_key] = $ary;
            }
        }
        return new Collection($tmp);
    }

    public function filter(callable $filter)
    {
        $tmp = [];
        foreach($this->array as $key => $val){
            $test = function (callable $filter, $val, $key): bool {
                return $filter($val, $key);
            };
            if($test($filter, $val, $key))
            {
                $tmp[$key] = $val;
            }
        }
        return new Collection($tmp);
    }

    public function reject(callable $filter)
    {
        $tmp = [];
        foreach($this->array as $key => $val){
            $test = function (callable $filter, $val, $key): bool {
                return $filter($val, $key);
            };
            if(!$test($filter, $val, $key))
            {
                $tmp[$key] = $val;
            }
        }
        return new Collection($tmp);
    }

    public function first()
    {
        $first = array_key_first($this->all());
        return $this->get($first);
    }

    public function last()
    {
        $last = array_key_last($this->all());
        return $this->get($last);
    }

    public function get($index)
    {
        if(array_key_exists($index , $this->all()))
        {
            return $this->all()[$index];
        }
        return null;
    }
}

/**
 * helper
 * Todo いつか別ファイルにする
 */

function collect(array $ary){
    return new Collection($ary);
}