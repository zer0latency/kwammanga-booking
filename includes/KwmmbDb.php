<?php

/**
 * kwmmbDb class
 */
class KwmmbDb
{
    //--------------------------------------------------------------------------
    //                             Properties
    public static $version = 0;
    public static $tables  = array(
        'kwmmb_booking_items',
        'kwmmb_bookings',
        'kwmmb_code',
    );
    //                             Properties
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                        Getters and Setters
    //
    //                        Getters and Setters
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                            Constructor
    //
    //                            Constructor
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                           Public Methods

    /**
     *
     * @param string $table Table name
     * @return string Prefixed table name
     */
    public static function get_table_name($table)
    {
        if (in_array($table, self::$tables)) {
            return self::get_prefix().$table;
        }
        return null;
    }

    public static function update_db()
    {
        foreach (self::$tables as $table) {
            kwmmb_log('Updating table '.$table);
            self::update_table($table, self::$version);
        }
    }
    //                           Public Methods
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                          Protected Methods
    protected static function get_table_version($table)
    {
        global $wpdb;

        $table_name = self::get_table_name( $wpdb->escape($table) );
        $db_name    = $wpdb->dbname;

        if (!$table_name) {
            return null;
        }

        $query = "SELECT table_comment FROM INFORMATION_SCHEMA.TABLES
                  WHERE table_schema=%s AND table_name=%s;";

        $query_result = $wpdb->get_var( $wpdb->prepare($query, $db_name, $table_name) );

        if (!$query_result or $query_result === '0') {
            return null;
        }

        return (int) str_replace('Version: ', '', $query_result);
    }

    /**
     *
     * @global wpdb $wpdb
     * @param string $table
     * @param string $version
     */
    protected static function update_table($table, $version)
    {
        global $wpdb;

        $prefixed_table_name = self::get_table_name($table);
        $current_version = (self::get_table_version($table) === null) ? -1 : self::get_table_version($table);

        for ($i = $current_version+1; $i <= $version; $i++) {
            kwmmb_log("Executing $i query for $table");
            $sql = self::get_sql($table, $i, array('prefix' => $wpdb->prefix));
            if ($wpdb->query( $sql )) {
                $wpdb->query("alter table `$prefixed_table_name` comment 'Version: $i'");
            }
        }
    }

    protected static function get_sql($table, $version, $params)
    {
        return KwmmbAssetic::render("db/{$table}_{$version}.sql", $params);
    }

    /**
     * Get Wordpress tables prefix
     * @global wpdb $wpdb
     * @return string
     */
    protected static function get_prefix()
    {
        global $wpdb;
        return $wpdb->prefix;
    }
    //                          Protected Methods
    //--------------------------------------------------------------------------
}
