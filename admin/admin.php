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
 * Ajax processing bookings creation
 */
add_action( 'wp_ajax_kwmmb_pub', 'KwmmbRest::pubRouter' );
add_action( 'wp_ajax_nopriv_kwmmb_pub', 'KwmmbRest::pubRouter' );

/**
 * Doesn't know why I really need this
 */
if ( is_admin() ) {
    /**
     * Ajax processing booking item creation
     */
    add_action( 'wp_ajax_kwmmb_rest', 'KwmmbRest::router' );
    add_action('admin_init', 'register_kwmmb_settings');
}