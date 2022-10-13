<?php

require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixWorkGroup.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/SigaCandidate.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixUser.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixDeal.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/controllers/bitrixUser.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/constants.php";

class BI_Rest_Create_Workgroup extends RestRouteBase {
    public $route = "/workgroups/siga/create";

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
    
            $id = intval($req["id"]);

            if (!get_post_status($id)) {
                throw new Error("Candidate with ID [$id] don't exists", BI_ERROR_CODES["CANDIDATE_NOT_EXISTS"]);
            }

            $members = $req["members"];
            $name = ucwords(strtolower(get_the_title($id)));
            $bitrix_id = intval(get_post_meta($id, "c_bitrix_user_id", true));
    
            $BitrixWorkgroupModel = new BitrixWorkGroup();
            $SigaCandidateModel = new SigaCandidate();
            $BitrixUserModel = new BitrixUser();
            $BitrixUserController = new bitrixUserController();
    
            $b_user = $BitrixUserModel->get_user($bitrix_id);

            if (is_null($b_user)) {
    
                $email = get_post_meta($id, "c_email", true);
                $b_users = $BitrixUserModel->get_users_by_email($email);
    
                if ($b_users) {
                    $bitrix_id = intval($b_users[0]["ID"]);
                    update_post_meta($id, "c_bitrix_user_id", $bitrix_id);
    
                } else {
    
                    $bitrix_id = $BitrixUserController->create_user_and_deal($id);
    
                    if ($bitrix_id) {
                        update_post_meta($id, "c_bitrix_user_id", $bitrix_id);
                    } else {
                        throw new Error("Bitrix User creation fails after don't match", BI_ERROR_CODES["BITRIX_USER_1NOT_FOUND"]);
                    }
    
                }
    
            }
    
            // verificar se existe um grupo de trabalho
            $workgroups = $BitrixWorkgroupModel->get_workgroups_by("OWNER_ID", $bitrix_id);
            $workgroup = array_filter($workgroups, function($workgroup) {
                return str_contains($workgroup["NAME"], "[SIGA]"); 
            });
    
            if ($workgroup) {
                throw new Exception("Workgroup for saver $name already exists", BI_ERROR_CODES["WORKGROUP_ALREADY_EXISTS"]);
            }
    
            // criar grupo de trabalho
            $group_id = $BitrixWorkgroupModel->create_workgroup([
                "NAME" => "[SIGA] " . $name
            ]);

            if (!$group_id) {
                throw new Exception("Error creating workgroup", BI_ERROR_CODES["WORKGROUP_CREATION_FAILS"]);
            }
    
            // colocar como owner
            $owner_setted = $BitrixWorkgroupModel->set_owner($group_id, $bitrix_id);
    
            if (!$owner_setted) {
                throw new Exception("Error to set $name to owner of group $group_id", BI_ERROR_CODES["SET_WORKGROUP_OWNER"]);
            }

            // remover-me do grupo
            $auto_subscribed_user = 12305;
            $BitrixWorkgroupModel->remove_users($group_id, [$auto_subscribed_user]);
    
            if (!$members) {
                $members = $SigaCandidateModel->get_imediate_childs($id);
            }
    
            // adicionar membros
            $sql = "SELECT meta_value AS bitrix_id FROM wp_postmeta WHERE meta_key = 'c_bitrix_user_id' AND post_id IN (" . join(",", $members) . ")";
            $b_u_ids = array_values(array_filter(
                array_column($wpdb->get_results($sql), "bitrix_id"),
                function($b_id) { return boolval($b_id); }
            ));
    
            $users_added = $BitrixWorkgroupModel->add_users($group_id, $b_u_ids);
            
            return rest_ensure_response([
                "workgroupId" => $group_id,
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