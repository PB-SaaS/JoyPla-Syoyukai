<?php 

namespace App\Lib;

use App\Model\TenantMaster;

class Auth extends TenantMaster
{

    public function __construct(){
        global $SPIRAL;
        
		foreach(parent::$fillable as $field)
		{
		    $this->{$field} = $SPIRAL->getContextByFieldTitle($field);
		}
		$this->id = $SPIRAL->getContextByFieldTitle('id');
	}
	
	public function save(){
		$parent = new parent();
		
		foreach(parent::$fillable as $field)
		{
		    $parent->{$field} = $this->{$field};
		}
	    $parent->id = $this->id;
	    return $parent->save();
	}
}