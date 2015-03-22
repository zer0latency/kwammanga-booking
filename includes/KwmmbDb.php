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
        'kwmmb_rooms',
        'kwmmb_bookings',
        'kwmmb_code',
    );
    public static $table_options = array(
        'kwmmb_bookings' => array(
            'default_slug' => 'str_id'
        )
    );
    public static $cache = array();
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
     * Get the prefixed and whitelisted name of table
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

    /**
     * Perform an update of DB tables
     */
    public static function update_db()
    {
        foreach (self::$tables as $table) {
            kwmmb_log('Updating table '.$table);
            self::update_table($table, self::$version);
        }
    }

    /**
     * Select builder for params array
     * @param  string $table_name Name of a table
     * @param  array  $where      Array with column => value rows
     * @return array              Array of objects returned by $wpdb->get_results(...)
     * @throws Exception
     */
    public static function select($table_name, $where = array())
    {
        global $wpdb;
        $real_table_name = self::get_table_name($table_name);

        if (!$real_table_name) {
            throw new Exception( "Unknown table name." );
        }

        $where = self::build_where($real_table_name, $where);
        kwmmb_log( print_r($where, true) );

        $results = $wpdb->get_results($wpdb->prepare(
                "select * from $real_table_name {$where['string']}",
                $where['params']
        ));

        if (count($results) > 1) {
            return $results;
        } else {
            return array_pop($results);
        }
    }

    /**
     * Insert or update table row
     * @global wpdb $wpdb
     * @param  string $table_name Name of a table
     * @param  array  $data       fields to update (insert)
     * @param  array  $where      Array with column => value rows
     * @return int                Rows affected
     * @throws Exception
     */
    public static function save($table_name, $data, $where=array())
    {
        global $wpdb;
        $real_table_name = self::get_table_name($table_name);

        $data = array_map(function ($item) {
            if ( !is_array($item) )   { return $item; }
            if ( isset($item['id']) ) { return $item['id']; }
            return array_pop($item);
        }, $data);

        if (count($where) > 0) {
            $action = "update";
            $result = $wpdb->update($real_table_name, $data, $where);
        } else {
            $action = "insert";
            if ($wpdb->insert($real_table_name, $data)) {
              $result = array( "id" => $wpdb->insert_id);
            }
        }

        if (false === $result) {
            throw new Exception("DB Error on $action in $real_table_name");
        }

        return $result;
    }

    /**
     * Delete row from a table
     * @global wpdb $wpdb Wordpress db class
     * @param  type $table_name Table name
     * @param  type $where      Array with column => value rows
     */
    public static function delete($table_name, $where)
    {
        global $wpdb;

        $real_table_name = self::get_table_name($table_name);

        $result = $wpdb->delete($real_table_name, $where);

        if (false === $result) {
            throw new Exception("DB Error on delete from $real_table_name");
        }

        return $result;
    }
    //                           Public Methods
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                          Protected Methods

    /**
     * Fetch table version from MySQL comment
     * @global wpdb $wpdb
     * @param string $table
     * @return int Version
     */
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
     * Get table columns
     * @global wpdb $wpdb
     * @param string $table
     * @return array Columns info
     */
    protected static function get_columns($table)
    {
        global $wpdb;

        if ( !self::$cache[$table] ) {
            self::$cache[$table] = $wpdb->get_results( "show columns in $table" );
        }

        return self::$cache[$table];
    }

    protected static function build_where($table, $params)
    {
        $columns = self::get_columns($table);
        $where_string = '';
        $where_params = array();

        foreach ($columns as $column) {
            $param = $params[$column->Field];
            if (!$param) { continue; }

            $type = explode('(', $column->Type);
            switch ($type[0]) {
                case 'int':
                    $placeholder = '%d';
                    break;
                default:
                    $placeholder = '%s';
                    break;
            }

            if (empty($where_string)) { $where_string = " where "; }

            $where_string  .= "{$column->Field}=$placeholder ";
            $where_params[] = $param;
        }

        return array("string" => $where_string, "params" => $where_params);
    }

    /**
     * Update table
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

    /**
     * Fetch sql from SQL template
     * @param type $table
     * @param type $version
     * @param type $params
     * @return type
     */
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
