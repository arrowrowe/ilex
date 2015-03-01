<?php


/**
 * Class BaseModel
 * @property \MongoDB $db database
 * @property \MongoCollection $collection
 */
class BaseModel
{
    protected $db = NULL;
    protected $collection = NULL;

    public static function escape($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    protected function load_db()
    {
        return is_null($this->db) ? ($this->db = Ilex\Loader::db()) : $this->db;
    }

    protected function load_model($path)
    {
        $name = \Ilex\Loader::getHandlerFromPath($path);
        return is_null($this->$name) ? ($this->$name = Ilex\Loader::model($path)) : $this->$name;
    }

    public function selectCollection($name)
    {
        return $this->collection = $this->load_db()->selectCollection($name);
    }

    public function validBatch($values, $rulePackages)
    {
        $length = min(count($values), count($rulePackages));
        $errors = array();
        for ($i = 0; $i < $length; $i++) {
            $results = $this->validRules($values[$i], $rulePackages[$i]);
            if ($results !== TRUE) {
                $errors[isset($rulePackages[$i]['name']) ? $rulePackages[$i]['name'] : $i] = $results;
            }
        }
        return count($errors) ? $errors : TRUE;
    }

    public function validRules($value, $rulePackage)
    {
        $errors = array();
        foreach ($rulePackage as $ruleName => $rule) {
            if ($ruleName === 'name') {
                continue;
            }
            $result = $this->validRule($value, $ruleName, $rule, $rule['message']);
            if ($result !== TRUE) {
                $errors[] = $result;
            }
        }
        return count($errors) ? $errors : TRUE;
    }

    public function validRule($value, $ruleName, $rule, $message = FALSE)
    {
        return ValidateLib::$ruleName($value, $rule) ? TRUE : $message;
    }

}


class ValidateLib
{
    public static function re($value, $rule) { return preg_match($rule['pattern'], $value) === 1; }

    public static function    int_gt($value, $rule) { return   intval($value) >  $rule['value']; }
    public static function    int_lt($value, $rule) { return   intval($value) <  $rule['value']; }
    public static function    int_ge($value, $rule) { return   intval($value) >= $rule['value']; }
    public static function    int_le($value, $rule) { return   intval($value) <= $rule['value']; }

    public static function  float_gt($value, $rule) { return floatval($value) >  $rule['value']; }
    public static function  float_lt($value, $rule) { return floatval($value) <  $rule['value']; }
    public static function  float_ge($value, $rule) { return floatval($value) >= $rule['value']; }
    public static function  float_le($value, $rule) { return floatval($value) <= $rule['value']; }

    public static function length_gt($value, $rule) { return   strlen($value) >  $rule['value']; }
    public static function length_lt($value, $rule) { return   strlen($value) <  $rule['value']; }
    public static function length_ge($value, $rule) { return   strlen($value) >= $rule['value']; }
    public static function length_le($value, $rule) { return   strlen($value) <= $rule['value']; }
}