<?php


namespace Ilex\lib;


/**
 * Class Validate
 * @package Ilex\lib
 */
class Validate
{

    public static function batch(&$values, $rulePackages)
    {
        $rulePackageCount = count($rulePackages);
        $errors = array();
        for ($i = 0; $i < $rulePackageCount; $i++) {
            $rulePackage = $rulePackages[$i];
            $name = isset($rulePackage['name']) ? $rulePackage['name'] : $i;

            if (isset($rulePackage['require'])) {
                $rule = $rulePackage['require'];
                if (!isset($values[$name])) {
                    $errors[$name] = array($rule['message']);
                    continue;
                }
                if (isset($rule['type'])) {
                    switch ($rule['type']) {
                        case 'int':
                            $values[$name] = intval($values[$name]);
                            break;
                        case 'float':
                            $values[$name] = floatval($values[$name]);
                    }
                }
            } elseif (!isset($values[$name])) {
                if (isset($rulePackage['default'])) {
                    $values[$name] = $rulePackage['default'];
                }
                continue;
            }

            $results = static::package($values[$name], $rulePackage);
            if ($results !== TRUE) {
                $errors[$name] = $results;
            }
        }
        return count($errors) ? $errors : TRUE;
    }

    public static function package($value, $rulePackage)
    {
        $errors = array();
        foreach ($rulePackage as $ruleName => $rule) {
            if (in_array($ruleName, array('name', 'require'))) {
                continue;
            }
            if ($ruleName === 'all') {
                foreach ($value as $valueItem) {
                    $result = static::package($valueItem, $rule);
                    if ($result !== TRUE) {
                        $errors += $result;
                    }
                }
            } else {
                $result = static::rule($value, $ruleName, $rule, $rule['message']);
                if ($result !== TRUE) {
                    $errors[] = $result;
                }
            }
        }
        return count($errors) ? $errors : TRUE;
    }

    public static function rule($value, $ruleName, $rule, $message = FALSE)
    {
        return static::$ruleName($value, $rule) ? TRUE : $message;
    }

    /*
     * ----------------------- -----------------------
     * Rules
     * ----------------------- -----------------------
     */

    public static function re($value, $rule) { return preg_match($rule['pattern'], $value) === 1; }

    public static function eq($value, $rule)   { return $value ==  $rule['value']; }
    public static function same($value, $rule) { return $value === $rule['value']; }

    public static function        gt($value, $rule) { return          $value  >  $rule['value']; }
    public static function        lt($value, $rule) { return          $value  <  $rule['value']; }
    public static function        ge($value, $rule) { return          $value  >= $rule['value']; }
    public static function        le($value, $rule) { return          $value  <= $rule['value']; }

    public static function    int_gt($value, $rule) { return   intval($value) >  $rule['value']; }
    public static function    int_lt($value, $rule) { return   intval($value) <  $rule['value']; }
    public static function    int_ge($value, $rule) { return   intval($value) >= $rule['value']; }
    public static function    int_le($value, $rule) { return   intval($value) <= $rule['value']; }

    public static function  float_gt($value, $rule) { return floatval($value) >  $rule['value']; }
    public static function  float_lt($value, $rule) { return floatval($value) <  $rule['value']; }
    public static function  float_ge($value, $rule) { return floatval($value) >= $rule['value']; }
    public static function  float_le($value, $rule) { return floatval($value) <= $rule['value']; }

    public static function  count_gt($value, $rule) { return    count($value) >  $rule['value']; }
    public static function  count_lt($value, $rule) { return    count($value) <  $rule['value']; }
    public static function  count_ge($value, $rule) { return    count($value) >= $rule['value']; }
    public static function  count_le($value, $rule) { return    count($value) <= $rule['value']; }

    public static function length_gt($value, $rule) { return   strlen($value) >  $rule['value']; }
    public static function length_lt($value, $rule) { return   strlen($value) <  $rule['value']; }
    public static function length_ge($value, $rule) { return   strlen($value) >= $rule['value']; }
    public static function length_le($value, $rule) { return   strlen($value) <= $rule['value']; }
    public static function length_eq($value, $rule) { return   strlen($value) === $rule['value']; }

}