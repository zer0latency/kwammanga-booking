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

define('KWMMB_BASES_TABLE_NAME', 'kwmmb-booking_items');
define('KWMMB_BOOKINGS_TABLE_NAME', 'kwmmb-bookings');
define('KWMMB_DB_VERSION', 0);

function kwmmb_install() {
    global $wpdb;

    $bases_table = $wpdb->prefix.KWMMB_BASES_TABLE_NAME;
    $bookings_table = $wpdb->prefix.KWMMB_BOOKINGS_TABLE_NAME;
}

function kwmmb_get_sql($name, $params) {
    kwmmb_render("db/$name", $params);
}