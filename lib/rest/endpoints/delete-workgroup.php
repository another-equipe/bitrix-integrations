<?php

require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixWorkGroup.php";

class BI_Rest_Delete_Workgroup extends RestRouteBase {
    public $route = "/workgroups/siga/delete";

    public function __construct(){
        $this->define_args();
    }

    private function define_args(){
        $this->args = [
            "methods" => "POST",
            "callback" => [$this, "callback"]
        ];
    }

    public function callback($req){
        $id = $req["id"];
        $bitrix_id = intval(get_post_meta($id, "c_bitrix_user_id", true));

        $BitrixWorkgroupModel = new BitrixWorkGroup();

        if (!$bitrix_id) {
            return "Bitrix user not found";
            throw new Exception("Bitrix user not found");
        }
        
        $workgroup = $BitrixWorkgroupModel->get_workgroups_by("OWNER_ID", $bitrix_id)[0];
        
        if (!$workgroup) {
            return "Workgroup not found";
            throw new Exception("Workgroup not found");
        }
        
        $deleted = $BitrixWorkgroupModel->delete_workgroup(intval($workgroup["ID"]));
        
        if (!$deleted) {
            return "Error deleting workgroup " . $workgroup["ID"];
            throw new Exception("Error deleting workgroup " . $workgroup["ID"]);
        }

        return rest_ensure_response([
            "workgroup" => $workgroup,
        ]);
    }

}