<?php

/**
 * Order class
 */
class Booking
{
    //--------------------------------------------------------------------------
    //                             Properties
    private $id;
    private $str_id;
    private $name;
    private $comfort;
    private $email;
    private $phone;
    private $adults;
    private $child_0_5;
    private $child_6_12;
    private $date_start;
    private $date_end;
    private $verified;
    private $item;
    private $comment;
    //                             Properties
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                        Getters and Setters
    public function get_id()
    {
        return $this->id;
    }

    public function get_str_id()
    {
        return $this->str_id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_comfort()
    {
        return $this->comfort;
    }

    public function get_email()
    {
        return $this->email;
    }

    public function get_phone()
    {
        return $this->phone;
    }

    public function get_adults()
    {
        return $this->adults;
    }

    public function get_child_0_5()
    {
        return $this->child_0_5;
    }

    public function get_child_6_12()
    {
        return $this->child_6_12;
    }

    public function get_date_start()
    {
        return $this->date_start;
    }

    public function get_date_end()
    {
        return $this->date_end;
    }

    public function get_verified()
    {
        return $this->verified;
    }

    public function get_item()
    {
        return $this->item;
    }

    public function get_comment()
    {
        return $this->comment;
    }

    public function set_id($id)
    {
        $this->id = $this->validate($id, 'int');
        return $this;
    }

    public function set_str_id($str_id)
    {
        $this->str_id = $this->validate($str_id, 'string');
        return $this;
    }

    public function set_name($name)
    {
        $this->name = $this->validate($name, 'string');
        return $this;
    }

    public function set_comfort($comfort)
    {
        $this->comfort = $this->validate($comfort, 'string');
        return $this;
    }

    public function set_email($email)
    {
        $this->email = $this->validate($email, 'email');
        return $this;
    }

    public function set_phone($phone)
    {
        $this->phone = $this->validate($phone, 'phone');
        return $this;
    }

    public function set_adults($adults)
    {
        $this->adults = $this->validate($adults, 'int');
        return $this;
    }

    public function set_child_0_5($child_0_5)
    {
        $this->child_0_5 = $this->validate($child_0_5, 'int');
        return $this;
    }

    public function set_child_6_12($child_6_12)
    {
        $this->child_6_12 = $this->validate($child_6_12, 'int');
        return $this;
    }

    public function set_date_start($date_start)
    {
        $this->date_start = $this->validate($date_start, 'date');
        return $this;
    }

    public function set_date_end($date_end)
    {
        $this->date_end = $this->validate($date_end, 'date');
        return $this;
    }

    public function set_verified($verified)
    {
        $this->verified = $this->validate($verified, 'int');
        return $this;
    }

    public function set_item($item)
    {
        $this->item = $this->validate($item, 'item');
        return $this;
    }

    public function set_comment($comment)
    {
        $this->comment = $this->validate($comment, 'string');
        return $this;
    }

    public static function get_table_name()
    {
        return KwmmbDb::get_table_name('kwmmb_bookings');
    }

    //                        Getters and Setters
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                            Constructor
    public function __construct($id, $str_id, $name, $comfort, $email, $phone, $adults, $child_0_5, $child_6_12, $date_start, $date_end, $verified, $item, $comment)
    {
        $this
            ->set_name($name)
            ->set_comfort($comfort)
            ->set_email($email)
            ->set_phone($phone)
            ->set_adults($adults)
            ->set_child_0_5($child_0_5)
            ->set_child_6_12($child_6_12)
            ->set_date_start($date_start)
            ->set_date_end($date_end)
            ->set_verified($verified)
            ->set_item($item)
            ->set_comment($comment);

        if (!empty($id)) {
            $this->set_id($id);
        }

        if (empty($str_id)) {
            $str_id = self::generate_str_id();
        }

        $this->set_str_id($str_id);
    }
    //                            Constructor
    //--------------------------------------------------------------------------
    //---------------------------Static Methods--------------------------
    public static function create_from_obj($obj)
    {
        if ( !is_object($obj) ) {
            return null;
        }

        if (is_object($obj->item)) {
            $obj->item = $obj->item->id;
        }

        return new self(
            $obj->id,
            $obj->str_id,
            $obj->name,
            $obj->comfort,
            $obj->email,
            $obj->phone,
            $obj->adults,
            $obj->child_0_5,
            $obj->child_6_12,
            $obj->date_start,
            $obj->date_end,
            $obj->verified,
            $obj->item,
            $obj->comment
       );
    }

    public static function get_by_id($id) {
        global $wpdb;

        $id = (int) $id;
        if (!$id) {
            return null;
        }
        $dbObj = $wpdb->get_row("SELECT * FROM ".self::get_table_name()." WHERE id=$id");

        return self::create_from_obj($dbObj);
    }

    public static function get_by_str_id($str_id) {
        global $wpdb;

        $str_id = $this->validate($str_id, 'string');
        if (!$str_id) {
            return null;
        }
        $dbObj = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".self::get_table_name()." WHERE str_id=%s", $str_id));

        return self::create_from_obj($dbObj);
    }

    public static function get_all() {
        global $wpdb;

        $objs = $wpdb->get_results("SELECT * FROM ".self::get_table_name());
        $result = array();
        foreach($objs as $obj) {
            $result[$obj->id] = self::create_from_obj($obj);
        }

        return $result;
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

    //                           Static Methods
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                           Public Methods
    public function persist() {
        global $wpdb;

        if (self::get_by_id($this->id)) {
            $wpdb->update(self::get_table_name(), get_object_vars($this), array('id' => $this->id));
        } else {
            $wpdb->insert(self::get_table_name(), get_object_vars($this));
            $this->set_id($wpdb->insert_id);
        }

        return true;
    }

    public function remove()
    {
        global $wpdb;

        if (!self::get_by_id($this->id)) {
            throw new Exception("Booking {$this->str_id} not found.");
        }

        return $wpdb->delete(self::get_table_name(), array('id' => $this->id));
    }

    public function as_array()
    {
        return get_object_vars($this);
    }
    //                           Public Methods
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                          Protected Methods
    //
    //                          Protected Methods
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                           Private Methods
    private function validate($value, $type='string') {
        global $wpdb;
        $escapedValue = $wpdb->escape($value);

        switch ($type) {
            case 'int':
                $escapedValue = (int) $escapedValue;
                if (!is_integer($escapedValue)) {
                    kwmmb_log("Validator: '$escapedValue'");
                    throw new Exception("Ошибка ваgfgлидации: не корректный '$type'.");
                }
                break;
            case 'email':
                if (false == preg_match('/^[a-z0-9-_\\.]{2,}@[a-z0-9-_\\.]{2,}\\.[a-z0-9]{2,}$/i', $escapedValue)) {
                    kwmmb_log("Validator: '$escapedValue' : ".'/^[a-z0-9-_\\.]{2,}@[a-z0-9-_\\.]{2,}\\.[a-z0-9]{2,}$/i');
                    throw new Exception("Ошибка ваgfgлидации: не корректный '$type'.");
                }
                break;
            case 'phone':
                if (false == preg_match('/^(\\+|)7[0-9]{10}$/', $escapedValue)) {
                    kwmmb_log("Validator: '$escapedValue'");
                    throw new Exception("Ошибка ваgfgлидации: не корректный '$type'.");
                }
            default:
                break;
        }

        return $escapedValue;
    }
    //                           Private Methods
    //--------------------------------------------------------------------------
}
