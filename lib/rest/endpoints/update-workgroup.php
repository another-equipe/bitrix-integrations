<?php

require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixWorkGroup.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/SigaCandidate.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/constants.php";


class BI_Rest_Update_Workgroup extends RestRouteBase {
    public $route = "/workgroups/siga/update";

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
        try {
            global $wpdb;

            $id = $req["id"];
            $members = $req["members"];
            $name = get_the_title($id);
            $bitrix_id = intval(get_post_meta($id, "c_bitrix_user_id", true));

            $BitrixWorkgroupModel = new BitrixWorkGroup();
            $SigaCandidateModel = new SigaCandidate();

            // verificar se existe um grupo de trabalho
            $workgroups = $BitrixWorkgroupModel->get_workgroups_by("OWNER_ID", $bitrix_id);

            $workgroup = array_filter($workgroups, function($workgroup) {
                return str_contains($workgroup["NAME"], "[SIGA]"); 
            })[0];

            if (!$workgroup) {
                throw new Exception("Workgroup for saver $name don't exists", BI_ERROR_CODES["WORKGROUP_DONT_EXISTS"]);
            }
            
            if (!$members) {
                $members = $SigaCandidateModel->get_imediate_childs($id);
            }

            // adicionar membros
            $sql = "SELECT meta_value AS bitrix_id FROM wp_postmeta WHERE meta_key = 'c_bitrix_user_id' AND post_id IN (" . join(",", $members) . ")";
            $b_u_ids = array_filter(
                array_column($wpdb->get_results($sql), "bitrix_id"),
                function($b_id) { return boolval($b_id); }
            );

            $users_added = $BitrixWorkgroupModel->add_users(intval($workgroup["ID"]), $b_u_ids);

            return rest_ensure_response([
                "workgroupId" => $workgroup["ID"],
                "owner" => $id,
                "b_owner" => $bitrix_id,
                "members" => $members,
                "members_bitrix_users" => $b_u_ids,
                "users_added" => $users_added,
            ]);
        } catch (\Throwable $th) {
            return $this->handle_exception($th);
        }
        
    }

}