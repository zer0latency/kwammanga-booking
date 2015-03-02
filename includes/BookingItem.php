<?php

/**
 * BookingItem class
 */
class BookingItem
{
    //--------------------------------------------------------------------------
    //                             Properties
    private static $table_name = KWMMB_BASES_TABLE_NAME;

    private $id;
    private $str_id;
    private $name;
    private $comfort;
    private $email;
    private $phone;
    private $adults;
    private $child_0_5;
    private $child_6_12;
    private $item;
    private $comment;
    //                             Properties
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                        Getters and Setters
    function get_id()
    {
        return $this->id;
    }

    function get_str_id()
    {
        return $this->str_id;
    }

    function get_name()
    {
        return $this->name;
    }

    function get_comfort()
    {
        return $this->comfort;
    }

    function get_email()
    {
        return $this->email;
    }

    function get_phone()
    {
        return $this->phone;
    }

    function get_adults()
    {
        return $this->adults;
    }

    function get_child_0_5()
    {
        return $this->child_0_5;
    }

    function get_child_6_12()
    {
        return $this->child_6_12;
    }

    function get_item()
    {
        return $this->item;
    }

    function get_comment()
    {
        return $this->comment;
    }

    function set_id($id)
    {
        $this->id = $this->validate($id, 'int');
        return $this;
    }

    function set_str_id($str_id)
    {
        $this->str_id = $this->validate($str_id, 'string');
        return $this;
    }

    function set_name($name)
    {
        $this->name = $name;
        return $this;
    }

    function set_comfort($comfort)
    {
        $this->comfort = $comfort;
        return $this;
    }

    function set_email($email)
    {
        $this->email = $email;
        return $this;
    }

    function set_phone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    function set_adults($adults)
    {
        $this->adults = $adults;
        return $this;
    }

    function set_child_0_5($child_0_5)
    {
        $this->child_0_5 = $child_0_5;
        return $this;
    }

    function set_child_6_12($child_6_12)
    {
        $this->child_6_12 = $child_6_12;
        return $this;
    }

    function set_item($item)
    {
        $this->item = $item;
        return $this;
    }

    function set_comment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

        //                        Getters and Setters
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                            Constructor
    function __construct($id, $str_id, $name, $comfort, $email, $phone, $adults, $child_0_5, $child_6_12, $item, $comment)
    {
        $this->id = $id;
        $this->str_id = $str_id;
        $this->name = $name;
        $this->comfort = $comfort;
        $this->email = $email;
        $this->phone = $phone;
        $this->adults = $adults;
        $this->child_0_5 = $child_0_5;
        $this->child_6_12 = $child_6_12;
        $this->item = $item;
        $this->comment = $comment;
    }
    //                            Constructor
    //--------------------------------------------------------------------------
    //---------------------------Static Methods--------------------------
    public static function createFromObj($obj) {
        if ($obj === null) {
            return null;
        }

        return new BookingItem(
            $obj->id,
            $obj->str_id,
            $obj->name,
            $obj->comfort,
            $obj->email,
            $obj->phone,
            $obj->adults,
            $obj->child_0_5,
            $obj->child_6_12,
            $obj->item,
            $obj->comment
       );
    }

    public static function getById($id) {
        global $wpdb;

        $id = $this->validate($id, 'int');
        if (!$id) {
            return null;
        }
        $dbObj = $wpdb->get_row("SELECT * FROM $this->table_name WHERE id=$id");

        return BookingItem::createFromObj($dbObj);
    }

    public static function getAll() {
        global $wpdb;

        $objs = $wpdb->get_results("SELECT * FROM $this->table_name");
        $result = array();
        foreach($objs as $obj) {
            $result[] = BookingItem::createFromObj($obj);
        }

        return $result;
    }
    //                           Static Methods
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                           Public Methods
    //
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
                    return false;
                }
                break;
            default:
                break;
        }

        return $escapedValue;
    }
    //                           Private Methods
    //--------------------------------------------------------------------------
}
