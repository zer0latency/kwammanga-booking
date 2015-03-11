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

function register_kwmmb_settings() {
    register_setting('kwmmb', 'smsaero_user');
    register_setting('kwmmb', 'smsaero_password');
    register_setting('kwmmb', 'smsaero_sender');
    register_setting('kwmmb', 'map_latitude');
    register_setting('kwmmb', 'map_longitude');
    register_setting('kwmmb', 'price_org');
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
add_action( 'wp_ajax_kwmmb_item_create', 'KwmmbAjax::item_create' );

/**
 * Get All booking items in JSON
 */
add_action( 'wp_ajax_kwmmb_items_get', 'KwmmbAjax::items_get' );

/**
 * Ajax processing booking item update
 */
add_action( 'wp_ajax_kwmmb_item_set', 'KwmmbAjax::item_set' );

/**
 * Ajax processing booking item removal
 */
add_action( 'wp_ajax_kwmmb_item_remove', 'KwmmbAjax::item_remove' );

/**
 * Ajax processing booking item removal
 */
add_action( 'wp_ajax_kwmmb_booking_get', 'KwmmbAjax::booking_get' );

/**
 * Doesn't know why I really need this
 */
if ( is_admin() ) {
    add_action('admin_init', 'register_kwmmb_settings');
}