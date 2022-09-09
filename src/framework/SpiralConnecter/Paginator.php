<?php
namespace framework\SpiralConnecter;

use Collection;
use stdClass;

class Paginator extends stdClass{

    private int $currentPage = 1;
    private int $from = 1;
    private int $lastPage  = 0;
    private int $limit = 0 ;
    private int $total = 0;

    public function __construct(
        Collection $data,
        int $currentPage,
        int $from,
        int $lastPage,
        int $limit,
        int $total
    )
    {
        $this->data = $data;
        foreach($data as $key => $val)
        {
            $this->{$key} = $val;
        }
        $this->currentPage = $currentPage;
        $this->from = $from;
        $this->lastPage = $lastPage;
        $this->limit = $limit;
        $this->total = $total;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getLastPage()
    {
        return $this->lastPage;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getTotal()
    {
        return $this->total;
    }
}