<?php

require_once __DIR__ . "/../base-api-endpoint.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/controllers/organogram.php";
require_once WP_PLUGIN_DIR . "/bitrix-integrations/constants.php";

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
            $id = intval($req["id"]);
            $status = get_post_meta($id,"c_status",true);

            if (!is_numeric($id) || !get_post_status($id)){
                throw new Exception("Candidato relacionado ao ID $id não existe", BI_ERROR_CODES["CANDIDATE_NOT_EXISTS"]);
            }

            if ($status == "distratado") {
                require_once WP_PLUGIN_DIR . "/bitrix-integrations/lib/rest/endpoints/organogram-delete-user.php";
                
                return (new BI_Rest_Organogram_Delete_User())->callback($req);
            }

            if ($status != "contratado") {
                throw new Exception("Apenas contratados são sincronizados no organograma", BI_ERROR_CODES["REQUEST_TO_NON_HIRED"]);
            }

            $organogram_controller = new OrganogramController();

            $result = $organogram_controller->update_user_in_tree($id);

            return  ([
                "data" => $result
            ]);
        } catch (\Throwable $th) {
            return $this->handle_exception($th);
        }
    }

}
