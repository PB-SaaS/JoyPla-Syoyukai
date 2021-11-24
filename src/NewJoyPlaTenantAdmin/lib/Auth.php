<?php 

namespace App\Lib;

use App\Model\TenantMaster;
use Exception;

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
	
	public function Gate(string $page)
	{
		if( $this->authority === "1" )
		{
			if(isset(GateSetting['all'][$page]))
			{
				return GateSetting['all'][$page];
			}
			else
			{
				return true;
			}
		}
		else if( $this->authority === "2" )
		{
			if(isset(GateSetting['custom1'][$page]))
			{
				return GateSetting['custom1'][$page];
			}
			else
			{
				return false;
			}
		}
		return false;
	}
	
	public function browseAuthority(string $page)
	{
		if(! $this->Gate($page))
		{
            throw new Exception("閲覧権限がありません",999);
		}
	}
}