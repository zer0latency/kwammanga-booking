<?php

/*
Plugin Name: Kwammanga Booking
Plugin URI: https://github.com/zer0latency/kwammanga-booking
Description: Under active development.
Author: Daniil Kolesnik
Version: 0.1
Author URI: https://github.com/zer0latency
*/

/**
 * Root plugin directory
 */
define('KWMMB_DIR', dirname(__FILE__));
define('KWMMB_URL', plugin_dir_url(__FILE__));

require KWMMB_DIR.'/includes/bootstrap.php';

register_activation_hook( KwmmbAssetic::get('php', 'hooks/activation'), 'kwmmb_activate' );

register_deactivation_hook( KwmmbAssetic::get('php', 'hooks/deactivation'), 'kwmmb_deactivate' );

add_action( 'admin_menu', 'kwmmb_add_admin_menu' );

add_action( 'init', 'kwmmb_register_form_shortcode' );