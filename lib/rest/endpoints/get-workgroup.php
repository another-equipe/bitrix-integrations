<?php

require_once WP_PLUGIN_DIR . "/bitrix-integrations/constants.php";

class BI_Rest_Get_Workgroup extends RestRouteBase {
    public $route = "/workgroups/siga/(?P<id>\d+)";

    public function __construct(){
        $this->define_args();
    }

    private function define_args(){
        $this->args = [
            "methods" => "GET",
            "callback" => [$this, "callback"]
        ];
    }

    public function callback($req){
        try {
            $id = intval($req["id"]);
            $bitrix_id = intval(get_post_meta($id, "c_bitrix_user_id", true));
    
            $BitrixWorkgroupModel = new BitrixWorkGroup();
    
            if (!$bitrix_id) {
                throw new Exception("Bitrix user not found", BI_ERROR_CODES["BITRIX_USER_NOT_FOUND"]);
            }
    
            $workgroups = $BitrixWorkgroupModel->get_workgroups_by("OWNER_ID", $bitrix_id);
    
            $workgroups = array_filter($workgroups, function($group) {
                return str_contains($group["NAME"], "[SIGA]");
            });

            return rest_ensure_response($workgroups);
        }catch (\Throwable $th) {
            return $this->handle_exception($th);
        }
        
    }

}