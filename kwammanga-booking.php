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

register_activation_hook(dirname(__file__).'/hooks/activation.php', 'kwmmb_activate');

register_deactivation_hook(dirname(__file__).'/hooks/deactivation.php', 'kwmmb_deactivate');

function kwmmb_add_admin_menu() {
    //add an item to the menu
    add_menu_page (
        'Бронирование',
        'Бронирование',
        'manage_options',
        'kwmmb-settings',
        plugin_dir_url( __FILE__ ).'admin/settings.html.php',
        plugin_dir_url( __FILE__ ).'assets/images/icon.png',
        '23.56'
    );
}

add_action( 'admin_menu', 'kwmmb_add_admin_menu' );