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


function kwmmb_add_admin_menu() {
    //add an item to the menu
    add_menu_page (
        'Бронирование',
        'Бронирование',
        'manage_options',
        'kwmmb-settings',
        'kwmmb_get_admin_page',
        kwmmb_asset('image', 'icon'),
        '23.56'
    );
}

function kwmmb_get_admin_page() {
    echo kwmmb_render(
        'admin/settings.html',
        array('items' => kwmmb_items_get_all())
    );
}

// Ajax item_create action
add_action( 'wp_ajax_kwmmb_item_create', 'kwmmb_ajax_item_create' );
function kwmmb_ajax_item_create() {
    check_ajax_referer( 'kwmmb_admin_nonce' );

    wp_send_json( kwmmb_items_create($_POST) );
    wp_die(); // All ajax handlers die when finished
}

// Ajax get_all_items action
add_action( 'wp_ajax_kwmmb_items_get', 'kwmmb_ajax_items_get' );
function kwmmb_ajax_items_get() {
    wp_send_json( kwmmb_items_get_all() );
    wp_die(); // All ajax handlers die when finished
}