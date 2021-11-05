<?php 

namespace App\Lib;

use App\Model\TenantMaster;

class Auth extends TenantMaster
{

    private $spiral ;

    public function __construct(){
        global $SPIRAL;
        
		foreach($this->fillable as $field)
		{
		    $this->{$field} = $SPIRAL->getContextByFieldTitle($field);
		}
	}
	
}