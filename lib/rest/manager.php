<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if (!class_exists( 'BI_REST_API' ) ) {

	class BI_REST_API {

        public function register_routes(){
            require("endpoints/get-documentation.php");
            require("endpoints/get-workgroup.php");
            require("endpoints/organogram-delete-user.php");
            require("endpoints/organogram-update-user.php");

            (new BI_Rest_Get_Documentation())->register();            
            (new BI_Rest_Get_Workgroup())->register();
            (new BI_Rest_Organogram_Delete_User())->register();
            (new BI_Rest_Organogram_Update_User())->register();
        }
        
    }

}