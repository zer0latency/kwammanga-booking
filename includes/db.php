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
define('KWMMB_DB_VERSION', 1);

function kwmmb_install() {
    global $wpdb;

    $tables = array(
        KWMMB_BASES_TABLE_NAME,
        KWMMB_BOOKINGS_TABLE_NAME
    );

    foreach ($tables as $table) {
        kwmmb_log("Requesting version for table $table");
        $table_version = kwmmb_current_table_version($wpdb->dbname, $table);
        if ($table_version === null) {
            kwmmb_log("$table does not exists.");
            $from_version = 0;
        } else {
            kwmmb_log("$table has version $table_version");
            $from_version = $table_version + 1;
        }

        for ($i=$from_version; $i<KWMMB_DB_VERSION+1; $i++) {
            kwmmb_log("Executing $i query for table $table");
            $wpdb->query( kwmmb_get_sql($table, $i, array('prefix' => $wpdb->prefix)) );
        }
    }
}

function kwmmb_get_sql($name, $version, $params) {
    return kwmmb_render("db/{$name}_{$version}.sql", $params);
}

function kwmmb_current_table_version ($db_name, $table_name) {
    global $wpdb;

    $table_name = $wpdb->escape($table_name);
    $db_name    = $wpdb->escape($db_name);

    $query = "SELECT table_comment
    FROM INFORMATION_SCHEMA.TABLES
    WHERE table_schema=%s
        AND table_name=%s;";

    $query_result = $wpdb->get_var( $wpdb->prepare($query, $db_name, $wpdb->prefix.$table_name) );

    if ( $query_result === '0') {
        return null;
    } else {
        return str_replace('Version: ', '', $query_result);
    }
}

function kwmmb_items_get_all() {
    global $wpdb;

    return $wpdb->get_results("SELECT * FROM `".$wpdb->prefix.KWMMB_BASES_TABLE_NAME."`");
}

function kwmmb_items_set($id, $params) {
    global $wpdb;

    return $wpdb->update($wpdb->prefix.KWMMB_BASES_TABLE_NAME, $params, array('id' => $id));
}

function kwmmb_items_create($params) {
    global $wpdb;
    $accept_fields = array('name','description','price','price_full','roominess','latitude','longitude');
    $accepted_params = array();

    foreach ($params as $key => $value) {
        if (in_array($key, $accept_fields)) {
            $accepted_params[$wpdb->escape($key)] = $wpdb->escape($value);
        }
    }

    return $wpdb->insert($wpdb->prefix.KWMMB_BASES_TABLE_NAME, $accepted_params);
}

kwmmb_install();