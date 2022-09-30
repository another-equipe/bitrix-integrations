<?php

require_once __DIR__ . "/../base-api-endpoint.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/classes/BitrixUser.php";

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
    
            $bitrix_user_id = intval(get_post_meta($id,"c_bitrix_user_id", true));

            if (!$bitrix_user_id) {
                throw new Error("Bitrix User not found", 1);
            }
    
            $result = $bitrix_user->deactivate_user($bitrix_user_id);

            return rest_ensure_response([
                "data" => $result
            ]);
        } catch (\Throwable $th) {
            return $this->handle_exception($th);
        }
    }

}