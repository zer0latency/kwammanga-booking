<?php

/*
 * Copyright (C) 2015 dkey
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

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