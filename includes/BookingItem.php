<?php

/**
 * BookingItem class
 */
class BookingItem
{
    //--------------------------------------------------------------------------
    //                             Properties
    private $id;
    private $name;
    private $description;
    private $tents_count;
    private $standards_count;
    private $comforts_count;
    private $ecolux_count;
    private $points;
    //                             Properties
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                        Getters and Setters
    public function get_id()
    {
        return $this->id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function get_tents_count()
    {
        return $this->tents_count;
    }

    public function get_standards_count()
    {
        return $this->standards_count;
    }

    public function get_comforts_count()
    {
        return $this->comforts_count;
    }

    public function get_ecolux_count()
    {
        return $this->ecolux_count;
    }

    public function get_points()
    {
        return $this->points;
    }

    public function set_id($id)
    {
        $this->id = $id;
        return $this;
    }

    public function set_name($name)
    {
        $this->name = $name;
        return $this;
    }

    public function set_description($description)
    {
        $this->description = $description;
        return $this;
    }

    public function set_tents_count($tents_count)
    {
        $this->tents_count = $tents_count;
        return $this;
    }

    public function set_standards_count($standards_count)
    {
        $this->standards_count = $standards_count;
        return $this;
    }

    public function set_comforts_count($comforts_count)
    {
        $this->comforts_count = $comforts_count;
        return $this;
    }

    public function set_ecolux_count($ecolux_count)
    {
        $this->ecolux_count = $ecolux_count;
        return $this;
    }

    public function set_points($points)
    {
        $this->points = $points;
        return $this;
    }

    public static function get_table_name()
    {
        return KwmmbDb::get_table_name('kwmmb_booking_items');
    }

    //                        Getters and Setters
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                            Constructor
    public function __construct($id, $name, $description, $tents_count, $standards_count, $comforts_count, $ecolux_count, $points)
    {
        $this->set_id($id)
            ->set_name($name)
            ->set_description($description)
            ->set_tents_count($tents_count)
            ->set_standards_count($standards_count)
            ->set_comforts_count($comforts_count)
            ->set_ecolux_count($ecolux_count)
            ->set_points($points);
    }
    //                            Constructor
    //--------------------------------------------------------------------------
    //---------------------------Static Methods--------------------------
    /**
     * Factory method for create entity from object
     * @param type $obj
     * @return \self
     */
    public static function create_from_obj($obj)
    {
        if ($obj === null) {
            return null;
        }

        return new self(
            $obj->id,
            $obj->name,
            $obj->description,
            $obj->tents_count,
            $obj->standards_count,
            $obj->comforts_count,
            $obj->ecolux_count,
            $obj->points
       );
    }

    public static function get_by_id($id) {
        global $wpdb;

        $id = self::validate($id, 'int');
        if (!$id) {
            return null;
        }
        $dbObj = $wpdb->get_row("SELECT * FROM ".self::get_table_name()." WHERE id=$id");

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
    //                           Static Methods
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //                           Public Methods
    public function persist() {
        global $wpdb;

        kwmmb_log("Persisting model ".__CLASS__." with data:\n".print_r(get_object_vars($this), true));

        if (self::get_by_id($this->id)) {
            $wpdb->update(self::get_table_name(), get_object_vars($this), array('id' => $this->id));
        } else {
            $wpdb->insert(self::get_table_name(), get_object_vars($this));
        }
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
    private static function validate($value, $type='string') {
        global $wpdb;
        $escapedValue = $wpdb->escape($value);

        switch ($type) {
            case 'int':
                $escapedValue = (int) $escapedValue;
                if (!is_integer($escapedValue)) {
                    throw new Exception("Validation Error: '$escapedValue' is not valid '$type'.");
                }
                break;
            case 'email':
                if (false == preg_match('/^[a-z0-9-_\.]{2,}@[a-z0-9-_\.]{2,}\.[a-z0-9]{2,}$/i', $escapedValue)) {
                    throw new Exception("Validation Error: '$escapedValue' is not valid '$type'.");
                }
            case 'phone':
                if (false == preg_match('/^(\+|)7[0-9]{10}$/', $escapedValue)) {
                    throw new Exception("Validation Error: '$escapedValue' is not valid '$type'.");
                }
            default:
                break;
        }

        return $escapedValue;
    }
    //                           Private Methods
    //--------------------------------------------------------------------------
}
