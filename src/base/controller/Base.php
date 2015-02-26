<?php


/**
 * Class BaseController
 */
class BaseController
{
    protected $db = NULL;
    public $last_error = array();
    public $last_error_message = '';

    public function load_db()
    {
        return is_null($this->db) ? ($this->db = \Ilex\Loader::db()) : $this->db;
    }

    public function validate($src, $validators)
    {
        $this->last_error = array();
        $this->last_error_message = '';
        foreach ($validators as $key => $validator) {
            $result = $this->_validate($src, $key, $validator);
            if ($result !== TRUE) {
                $this->last_error[$key] = $result;
                $this->last_error_message .= $result . ' ';
            }
        }
        return count($this->last_error) === 0;
    }

    private function _validate($src, $key, $validator)
    {
        $factors = is_array($validator) ? $validator : array($validator);
        if (!isset($src[$key])) {
            return in_array('require', $factors) ? ($key . ' is required.') : TRUE;
        }
        if (isset($factors['num'])) {
            if (!is_numeric($src[$key])) {
                return $key . ' should be a number.';
            }
            if ($factors['num'] === 'int') {
                $src[$key] = intval($src[$key]);
            } else {
                $src[$key] = floatval($src[$key]);
            }
            unset($factors['num']);
        }
        $value = $src[$key];
        foreach ($factors as $name => $detail) {
            switch (strval($name)) {
                case 'lt':
                    if ($value < $detail) {
                        continue;
                    } else {
                        return $key . ' should be a number less than ' . strval($detail) . '.';
                    }
                case 'gt':
                    if ($value > $detail) {
                        continue;
                    } else {
                        return $key . ' should be a number greater than ' . strval($detail) . '.';
                    }
                case 'le':
                    if ($value <= $detail) {
                        continue;
                    } else {
                        return $key . ' should be a number less than or equal to ' . strval($detail) . '.';
                    }
                case 'ge':
                    if ($value >= $detail) {
                        continue;
                    } else {
                        return $key . ' should be a number greater than or equal to ' . strval($detail) . '.';
                    }
                case 'ne':
                    if ($value !== $detail) {
                        continue;
                    } else {
                        return $key . ' should not equal to ' . strval($detail) . '.';
                    }
            }
        }
        return TRUE;
    }

}