<?php

/**
 * KwmmbCode class
 */
class Code
{
    //--------------------------------------------------------------------------
    //                             Properties
    protected $id;
    protected $phone;
    protected $code;
    protected $booking_id;
    protected $ip;
    protected $date_create;

    private static $message = "Ваш код подтверждения: %s.";
    private static $smsaero_url = "https://gate.smsaero.ru/send/?";
    //                             Properties
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                        Getters and Setters
    public function get_id()
    {
        return $this->id;
    }

    public function get_phone()
    {
        return $this->phone;
    }

    public function get_code()
    {
        return $this->code;
    }

    public function get_booking_id()
    {
        return $this->booking_id;
    }

    public function get_ip()
    {
        return $this->ip;
    }

    public function get_date_create()
    {
        return $this->date_create;
    }

    public function set_id($id)
    {
        $this->id = $id;
        return $this;
    }

    public function set_phone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function set_code($code)
    {
        $this->code = $code;
        return $this;
    }

    public function set_booking_id($booking_id)
    {
        $this->booking_id = $booking_id;
        return $this;
    }

    public function set_ip($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    public function set_date_create($date_create)
    {
        $this->date_create = $date_create;
        return $this;
    }
    //                        Getters and Setters
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                            Constructor
    //
    //                            Constructor
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                           Public Methods
    public static function create($phone, $ip, $booking_id) {
        global $wpdb;

        if ( count(self::get_by(array('ip'         => $ip)))        >2
          || count(self::get_by(array('phone'      => $phone)))     >2
          || count(self::get_by(array('booking_id' => $booking_id)))>2 )
        {
            throw new Exception("Превышено кол-во отправленных кодов. Повторите попытку через 10 минут.");
        }

        $new = new Code();
        $new->set_code(self::generate_code())
            ->set_date_create(date('Y-m-d H:i:s'))
            ->set_booking_id($booking_id)
            ->set_ip($ip)
            ->set_phone($phone);

        // Save to database
        if (!$wpdb->insert(KwmmbDb::get_table_name('kwmmb_code'), get_object_vars($new)))
        {
            return null;
        }
        $new->set_id($wpdb->insert_id);

        // Try to send SMS message.
        if (!self::send_to_smsaero($new))
        {
            return null;
        }

        return $new;
    }

    /**
     * Check validity of code and booking_id
     * @param int $booking_id
     * @param string $code
     * @return boolean
     */
    public static function check($booking_id, $code) {
        $objs = self::get_by(array('booking_id' => $booking_id));

        foreach ($objs as $obj) {
            if ($obj->code === $code) {
                return true;
            }
        }

        return false;
    }

    public static function get_by($cond)
    {
        global $wpdb;

        $items = $wpdb->get_results(
            $wpdb->prepare("select * from ".KwmmbDb::get_table_name('kwmmb_code')." where ".key($cond)."=%s;", current($cond))
        );

        return $items;
    }
    //                           Public Methods
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                          Protected Methods
    protected static function generate_code() {
        $dict = str_split('0123456789');
        $code_length = 6;
        $code = '';
        for ($i=0; $i < $code_length; $i++) {
            $code .= $dict[rand(0, count($dict))];
        }

        return $code;
    }

    protected static function send_to_smsaero(Code $code) {
        $params = array(
            'user='.    get_option('smsaero_user'),
            'password='.get_option('smsaero_password'),
            'to='.      $code->get_phone(),
            'text='.    sprintf(self::$message, $code->get_code()),
            'from='.    get_option('smsaero_sender'),
        );

        $request = self::$smsaero_url . implode('&', $params);

        $response = file_get_contents( urlencode($request) );

        if (stripos($response, 'accept') === false) {
            kwmmb_log("Unable to send SMS: ".$response);
            return false;
        }

        return true;
    }
    //                          Protected Methods
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                           Private Methods
    //
    //                           Private Methods
    //--------------------------------------------------------------------------
}
