<?php

/**
 * Add a menuentry form kwammanga-booking plugin
 */
function kwmmb_add_admin_menu() {
    //add an item to the menu
    add_menu_page (
        'Бронирование',
        'Бронирование',
        'manage_options',
        'kwmmb-settings',
        'kwmmb_get_admin_page',
        KwmmbAssetic::get('image', 'icon'),
        '23.56'
    );
}

/**
 * Render a admin page
 */
function kwmmb_get_admin_page() {
    echo KwmmbAssetic::render(
        'assets/views/settings.html',
        array('items' => BookingItem::get_all())
    );
}

/**
 * Ajax processing booking item creation
 */
function kwmmb_ajax_item_create() {
    check_ajax_referer( 'kwmmb_admin_nonce' );

    $item = BookingItem::create_from_obj((object) $_POST);

    wp_send_json( $item->persist() );
    wp_die(); // All ajax handlers die when finished
}
add_action( 'wp_ajax_kwmmb_item_create', 'kwmmb_ajax_item_create' );


/**
 * Ajax processing booking item update
 */
function kwmmb_ajax_item_set() {
    check_ajax_referer( 'kwmmb_admin_nonce' );
    $id = (int) $_POST['id'];
    wp_send_json( kwmmb_item_set($id, $_POST) );
    wp_die(); // All ajax handlers die when finished
}
add_action( 'wp_ajax_kwmmb_item_set', 'kwmmb_ajax_item_set' );

/**
 * Get All booking items in JSON
 */
function kwmmb_ajax_items_get() {
    wp_send_json( BookingItem::get_all() );
    wp_die(); // All ajax handlers die when finished
}
add_action( 'wp_ajax_kwmmb_items_get', 'kwmmb_ajax_items_get' );

/**
 * Ajax processing booking item removal
 */
function kwmmb_ajax_item_remove() {
    check_ajax_referer('kwmmb_admin_nonce');
    $id = (int) $_POST['item_id'];
    wp_send_json( kwmmb_item_remove($id) );
    wp_die(); // All ajax handlers die when finished
}
add_action( 'wp_ajax_kwmmb_item_remove', 'kwmmb_ajax_item_remove' );