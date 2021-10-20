<?php 

namespace App\Lib;

class UserInfo{

    private $spiral ;
    public $permissionName = [
        1 => '管理者',
        2 => '担当者',
        3 => '承認者'
    ];

    public function __construct(\Spiral $SPIRAL){
		$this->spiral = $SPIRAL;
	}

    public function getSPIRAL(){
        return $this->spiral;
    }

    public function getHospitalId(){
        return $this->spiral->getContextByFieldTitle("hospitalId");
    }

    public function getDivisionId(){
        return $this->spiral->getContextByFieldTitle("divisionId");
    }
    
    public function getLoginId(){
        return $this->spiral->getContextByFieldTitle("loginId");
    }

    public function getName(){
        return $this->spiral->getContextByFieldTitle("name");
    }

    public function getMailAddress(){
        return $this->spiral->getContextByFieldTitle("mailAddress");
    }
    
    public function getTenantId(){
        return $this->spiral->getContextByFieldTitle("tenantId");
    }

    public function getDistributorId(){
        return $this->spiral->getContextByFieldTitle("distributorId");
    }
    
    public function getUserPermission(){
        return $this->spiral->getContextByFieldTitle("userPermission");
    }
    public function getUserPermissionName(){
        return $this->permissionName[$this->getUserPermission()];
    }

    public function isHospitalUser(){
        if($this->getDistributorId() == null)
        {
            return true;
        }
        return false;
    }

    public function isDistributorUser(){
        return !$this->isHospitalUser();
    }
    
    public function isAdmin(){
        if($this->getUserPermission() == '1')
        {
            return true;
        }
        return false;
    }

    public function isUser(){
        if($this->getUserPermission() == '2')
        {
            return true;
        }
        return false;
    }
    
    
    public function isApprover(){
        if($this->getUserPermission() == '3')
        {
            return true;
        }
        return false;
    }
}