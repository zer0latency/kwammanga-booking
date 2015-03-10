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
        if ($wpdb->insert(KwmmbDb::get_table_name('kwmmb_code'), get_object_vars($new)))
        {
            $new->set_id($wpdb->insert_id);
            return $new;
        }

        return null;
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
    //                          Protected Methods
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                           Private Methods
    //
    //                           Private Methods
    //--------------------------------------------------------------------------
}
