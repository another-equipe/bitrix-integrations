<?php

/**
 * Bitrix Integrations
 *
 * @since             2.0.0
 * @wordpress-plugin
 * Plugin Name:       Bitrix Integrations
 * Plugin URI:        
 * Description:       Plugin que faz a integração entre o SIGA e o Bitrix
 * Version:           2.0.0
 * Author:            Another Equipe
 * Author URI:        https://another-equipe.savecash.tech/
 * Text Domain:       bitrix-integrations
 */

// If this file was called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require "constants.php";
require "core.php";

register_activation_hook(__FILE__, 'bi_activate_plugin');

add_action("rest_api_init", [new BI_REST_API(), "register_routes"]);