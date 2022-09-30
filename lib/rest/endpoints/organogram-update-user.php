<?php

require_once __DIR__ . "/../base-api-endpoint.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/controllers/organogram.php";

class BI_Rest_Organogram_Update_User extends RestRouteBase {
    public $route = "/organogram/user/update/(?P<id>\d+)";

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
            $id = $req["id"];

            if (!is_numeric($id) || !get_post_status($id)){
                throw new Exception("Candidato relacionado ao ID $id não existe", 1);
            }

            $organogram_controller = new OrganogramController();

            $result = $organogram_controller->update_user_in_tree($id);

            return rest_ensure_response([
                "data" => $result
            ]);
        } catch (\Throwable $th) {
            return $this->handle_exception($th);
        }
    }

}
