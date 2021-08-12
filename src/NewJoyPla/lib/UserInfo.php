<?php 

namespace App\Lib;

class UserInfo{

    private $spiral ;

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
}