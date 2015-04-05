<?php


namespace Ilex\lib;


/**
 * Class Validate
 * @package Ilex\lib
 */
class Validate
{
    public static $patterns = array(
        'alpha' => '/^[\pL\pM]+$/u',
        'alpha_num' => '/^[\pL\pM\pN]+$/u',
        'aA' => '/^[a-z]+$/i',
        'aA0' => '/^[a-z0-9]+$/i',
        'chinese' => '/^[\x{4e00}-\x{9fa5}]+$/u',
        'captcha' => '/^[a-z0-9]{4}$/i',
        'mobile' => '/^1[3-9][0-9]{9}$/',
        'email' => '/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i',
    );

    public static function batch(&$values, $rulePackages)
    {
        $errors = array();
        foreach ($rulePackages as $i => $rulePackage) {
            $name = isset($rulePackage['name']) ? $rulePackage['name'] : $i;

            if (isset($rulePackage['require'])) {
                $rule = $rulePackage['require'];
                if (!isset($values[$name])) {
                    $errors[$name] = array($rule['message']);
                    continue;
                }
            } elseif (!isset($values[$name])) {
                if (isset($rulePackage['default'])) {
                    $values[$name] = $rulePackage['default'];
                }
                continue;
            }

            if (isset($rulePackage['type'])) {
                $rule = $rulePackage['type'];
                switch ($rule['type']) {
                    case 'int':
                        if (!static::is_int($values[$name])) {
                            $errors[$name] = array($rule['message']);
                            continue;
                        }
                        $values[$name] = intval($values[$name]);
                        break;
                    case 'float':
                        if (!static::is_float($values[$name])) {
                            $errors[$name] = array($rule['message']);
                            continue;
                        }
                        $values[$name] = floatval($values[$name]);
                        break;
                    case 'array':
                        if (!is_array($values[$name])) {
                            $errors[$name] = array($rule['message']);
                            continue;
                        }
                        break;
                    default:
                        throw new \Exception('Unrecognizable type "' . $rule['type'] . '" for Validation.');
                }
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
            if (in_array($ruleName, array('name', 'require', 'default', 'type'))) {
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
     * Kit
     * ----------------------- -----------------------
     */

    public static function is_int($value)
    {
        if (is_int($value)) {
            return TRUE;
        } elseif (preg_match('@^\d+$@', $value) === 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public static function is_float($value)
    {
        if (is_float($value) OR is_int($value)) {
            return TRUE;
        } elseif (preg_match('@^(\d+(\.\d*)?|\.\d+)$@', $value) === 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * ----------------------- -----------------------
     * Rules
     * ----------------------- -----------------------
     */

    public static function re($value, $rule) {
        return preg_match(
            isset($rule['pattern']) ? $rule['pattern'] : static::$patterns[$rule['type']],
            $value
        ) === 1;
    }

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