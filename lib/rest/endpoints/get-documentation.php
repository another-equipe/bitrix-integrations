<?php

require_once __DIR__ . "/../base-api-endpoint.php";

class BI_Rest_Get_Documentation extends RestRouteBase {
    public $route = "/";

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
        return rest_ensure_response("https://another-equipe.atlassian.net/wiki/spaces/BIP/");   
    }

}