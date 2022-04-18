<?php
namespace App\Controller {

    use View;
    use Controller;
    
    class InventoryMRController extends Controller
    {
        
        public function __construct()
        {
        }
    
        public function index(): View
        {
    
            
    
            return $this->view('',[],false);
        }
    }
}
