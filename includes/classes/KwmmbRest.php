<?php

class KwmmbRest {

    private static $protocol = "HTTP/1.1";
    private static $pubRoutes = array(
      "new_booking" => "KwmmbRest::saveBooking",
      "bookings"  => "KwmmbRest::getBooking",
      "check_code"   => "KwmmbRest::checkCode"
    );

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

    public static function pubRouter() {
        $route = explode('/', $_GET['route']);
        $route_name = $route[0];
        $route_param = $route[1];

        $method = self::$pubRoutes[$route_name];
        if (empty($method)) { kwmmb_log("Route not found: {$route_name}"); wp_die(); }
        kwmmb_log("Route: $method");

        return call_user_func($method, $route_param);
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

    public static function saveBooking() {
      $model = json_decode( str_replace('\"','"',$_POST['model']), true );
      $model['str_id'] = self::generate_str_id();
      $result = KwmmbDb::save("kwmmb_bookings", $model);

      if (!count($result)) {
        wp_die();
      }

      if ($model['verified'] == 0) {
        $code = Code::create($model['phone'], $_SERVER['REMOTE_ADDR'], $result["id"]);
      }

      $result["str_id"] = $model['str_id'];
      wp_send_json($result);
    }

    public static function getBooking($str_id) {
      if (isset($_POST['_method'])) {
        $model = json_decode( str_replace('\"','"',$_POST['model']), true );
        wp_send_json(KwmmbDb::save('kwmmb_bookings', $model, array('str_id' => $str_id)));
      }
      wp_send_json(KwmmbDb::select('kwmmb_bookings', array('str_id' => $str_id)));
    }

    public static function checkCode() {
      $model = json_decode( str_replace('\"','"',$_POST['model']), true );

      $status = Code::check($model['booking_id'], $model['code']);

      if ($status) {
        KwmmbDb::save('kwmmb_bookings', array('verified'=>1), array('id' => $model['booking_id']));
        KwmmbRest::sendMail($model['booking_id']);
      }

      wp_send_json(array( "success" => $status));
    }

    protected static function generate_str_id() {
      $chars = str_split("qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890");
      $length = 10;
      $str_id = "";

      for ($i=0; $i<$length; $i++) {
          $str_id.= $chars[mt_rand(0, count($chars))];
      }

      return $str_id;
    }

    protected static function sendMail($booking_id) {
      $to = 'dandydan2k@gmail.com';
      $subject = 'Kwammanga.ru - ваш заказ принят.';
      $model = KwmmbDb::select("kwmmb_bookings", array( "id" => $booking_id));
      $body = KwmmbAssetic::render("assets/views/email.html", array('model' => $model));
      $headers = array('Content-Type: text/html; charset=UTF-8', 'From: Kwammanga.ru <'.get_bloginfo ( 'admin_email' ).'>');

      wp_mail( $to, $subject, $body, $headers );
    }
}