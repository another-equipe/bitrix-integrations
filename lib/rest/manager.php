<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if (!class_exists( 'BI_REST_API' ) ) {

	class BI_REST_API {

        public function register_routes(){

            $this->import_and_register("endpoints/get-documentation.php", "BI_Rest_Get_Documentation");
            $this->import_and_register("endpoints/organogram-delete-user.php", "BI_Rest_Organogram_Delete_User");
            $this->import_and_register("endpoints/organogram-update-user.php", "BI_Rest_Organogram_Update_User");
            $this->import_and_register("endpoints/get-workgroup.php", "BI_Rest_Get_Workgroup");
            $this->import_and_register("endpoints/create-workgroup.php", "BI_Rest_Create_Workgroup");
            $this->import_and_register("endpoints/update-workgroup.php", "BI_Rest_Update_Workgroup");
            $this->import_and_register("endpoints/delete-workgroup.php", "BI_Rest_Delete_Workgroup");

        }

        private function import_and_register(string $path, string $class_name) {
            require($path);
            
            if (class_exists($class_name)){
                $reflection_class = new ReflectionClass($class_name);
                $instance = $reflection_class->newInstance();

                if (method_exists($instance, "register")){
                    $instance->register();
                }

            }

        }
        
    }

}