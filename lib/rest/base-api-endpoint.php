<?php

require_once WP_PLUGIN_DIR . "/bitrix-integrations/constants.php";

class RestRouteBase {
    public $namespace = BI_REST_API_NAMESPACE;
    public $route = "/";
    public $args = array();
    public $override = true;

    public function register(){

        register_rest_route(
            $this->namespace,
            "/" . BI_REST_API_SECRET . $this->route,
            $this->args,
            $this->override
        );
    }

    public function handle_exception(\Throwable $th){
        $code = $th->getCode();

        return [
            "error_code" => $code,
            "error" => BI_REST_API_DEBUG ? $th->getMessage() : "internal_server_error"
        ];
    }
}