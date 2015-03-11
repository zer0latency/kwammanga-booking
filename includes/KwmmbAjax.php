<?php

/**
 * KwmmbAjax class
 */
class KwmmbAjax
{
    //--------------------------------------------------------------------------
    //                                 items
    public static function items_get() {
        $items = BookingItem::get_all();
        foreach ($items as $item) {
            $arr_items[] = $item->as_array();
        }
        wp_send_json( $arr_items );
        wp_die(); // All ajax handlers die when finished
    }

    public static function item_create() {
        check_ajax_referer( 'kwmmb_admin_nonce' );
        $item = BookingItem::create_from_obj((object) $_POST);
        wp_send_json( $item->persist() );
        wp_die(); // All ajax handlers die when finished
    }

    public static function item_set() {
        check_ajax_referer( 'kwmmb_admin_nonce' );
        $item = BookingItem::create_from_obj((object) $_POST);
        wp_send_json( $item->persist() );
        wp_die(); // All ajax handlers die when finished
    }

    public static function item_remove() {
        check_ajax_referer('kwmmb_admin_nonce');
        $id = (int) $_POST['item_id'];
        $item = BookingItem::get_by_id($id);
        wp_send_json( $item->remove() );
        wp_die(); // All ajax handlers die when finished
    }
    //                               items
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                              bookings
    public static function booking_get() {
        $str_id = $_POST['str_id'];
        kwmmb_log("KwmmbAjax: Searching for $str_id...");
        $booking = Booking::get_by_str_id($str_id);
        wp_send_json( $booking->as_array() );
        wp_die();
    }
    //                              bookings
    //--------------------------------------------------------------------------
}
