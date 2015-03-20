<?php

class KwmmbRest {

    private static $protocol = "HTTP/1.1";

    public static function router() {
        $route = explode('/', $_GET['route']);
        $model = "kwmmb_".$route[0];
        $slug  = $route[1];
        $method = $_POST['_method'] ? : $_SERVER['REQUEST_METHOD'];
        $header = "200 OK";

        if ( $slug ) {
            $table_options = KwmmbDb::$table_options[$model];
            $default_slug = empty($table_options['default_slug']) ? 'id' : $table_options['default_slug'];
            $where = array(
               "$default_slug" => $slug
            );
        }
        try {
            kwmmb_log("KwmmbRest: triggered $method on $model".PHP_EOL.print_r($where, true));
            header(self::$protocol." $header");
            wp_send_json( KwmmbRest::$method($model, $where) );
        } catch (Exception $e) {
            header(self::$protocol." 400 Bad Request");
            wp_send_json(array("error" => $e->getMessage()));
        }
    }

    /**
     * Backbone read
     */
    public static function GET($model_name, $where)
    {
        if (!KwmmbDb::get_table_name($model_name)) {
            throw new Exception("Unknown model.");
        }

        $models = KwmmbDb::select($model_name, $where);
        wp_send_json($models);
    }

    /**
     * Backbone create
     */
    public static function POST($model_name)
    {
        $model = json_decode( str_replace('\"','"',$_POST['model']), true );
        if (!KwmmbDb::get_table_name($model_name)) {
            throw new Exception("Unknown model.");
        }

        return KwmmbDb::save($model_name, $model);
    }

    /**
     * Backbone update
     */
    public static function PUT($model_name, $where)
    {
        $model = json_decode( str_replace('\"','"',$_POST['model']), true );
        if (!KwmmbDb::get_table_name($model_name)) {
            throw new Exception("Unknown model");
        }
        
        return KwmmbDb::save($model_name, $model, $where);
    }

    /**
     * Backbone patch
     */
    public static function PATCH()
    {

    }

    /**
     * Backbone delete
     */
    public static function DELETE($model_name, $where)
    {
        $model = json_decode( str_replace('\"','"',$_POST['model']), true );
        if (!KwmmbDb::get_table_name($model_name)) {
            throw new Exception("Unknown model");
        }
        
        return KwmmbDb::delete($model_name, $where);
    }
}