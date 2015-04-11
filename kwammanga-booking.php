<?php

/*
Plugin Name: Kwammanga Booking
Plugin URI: https://github.com/zer0latency/kwammanga-booking
Description: Система бронирования номеров. Страница с формой должна иметь название kwmmb-booking. Реквизиты должны находиться в рубрике "реквизиты".
Author: Daniil Kolesnik
Version: 1.0.2
Author URI: https://github.com/zer0latency
GitHub Plugin URI: zer0latency/kwammanga-booking
GitHub Branch:     master
*/

/**
 * Root plugin directory
 */
define('KWMMB_DIR', dirname(__FILE__));
define('KWMMB_URL', plugin_dir_url(__FILE__));

require KWMMB_DIR.'/includes/bootstrap.php';

register_activation_hook( __FILE__, 'KwmmbDb::update_db' );

add_action( 'admin_menu', 'kwmmb_add_admin_menu' );

add_action( 'init', 'kwmmb_register_form_shortcode' );