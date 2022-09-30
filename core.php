<?php

require_once "lib/rest/manager.php";

function bi_activate_plugin(){

    if (!post_type_exists(BI_SLUG_CANDIDATES)){
    	throw new Exception("Post Type de candidatos não existe");
    }

}