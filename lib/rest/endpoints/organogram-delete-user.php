<?php

require_once __DIR__ . "/../base-api-endpoint.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixUser.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/constants.php";

class BI_Rest_Organogram_Delete_User extends RestRouteBase {
    public $route = "/organogram/user/delete/(?P<id>\d+)";

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
            $bitrix_user = new BitrixUser();
            $id = $req["id"];

            $bitrix_user_id = intval(get_post_meta($id, "c_bitrix_user_id", true));
            $b_user = $bitrix_user->get_user($bitrix_user_id);

            $profiles_name_match = strtolower($b_user["NAME"] ?? "") == strtolower(get_the_title($id));

            if (!$profiles_name_match || is_null($b_user)) {
                $email = get_post_meta($id, "c_email", true);
                $b_users = $bitrix_user->get_users_by_email($email);

                if ($b_users) {

                    $bitrix_user_id = intval($b_users[0]["ID"]);
                    update_post_meta($id, "c_bitrix_user_id", $bitrix_user_id);
    
                } else {
                    throw new Error("Bitrix User dont exists", BI_ERROR_CODES["BITRIX_USER_NOT_FOUND"]);
               
                }

            } else {

                $bitrix_user_id = intval($b_user["ID"]);
                update_post_meta($id, "c_bitrix_user_id", $b_user["ID"]);

            }
    
            $result = $bitrix_user->deactivate_user($bitrix_user_id);

            return rest_ensure_response([
                "data" => ["result" => $result]
            ]);
        } catch (\Throwable $th) {
            return $this->handle_exception($th);
        }
    }

}