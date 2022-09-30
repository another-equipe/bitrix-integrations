<?php

require_once __DIR__ . "/../base-api-endpoint.php";

class BI_Rest_Get_Workgroup extends RestRouteBase {
    public $route = "/workgroup/(?P<id>\d+)";

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
        return rest_ensure_response([$req["id"]]);
    }

}