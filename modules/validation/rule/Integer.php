<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/10/2015
 * Time: 2:58 PM
 */
class Validate_Rule_Integer implements Validation_IRule
{

    const POSITIVE = 1;
    const BOTH = 0;
    const NEGATIVE = -1;

    private $type;
    private $allowZero;

    public function __construct($type = 0, $allowZero = true){
        switch($type) {
            case self::POSITIVE:
            case self::BOTH:
            case self::NEGATIVE:
                $this->type = $type;
                $this->allowZero = $allowZero;
                break;
            default:
                throw new FwException("Malformed validation rule integer. Type musy be equal to 1, 0 or -1.");
        }
    }

    public function validate($data)
    {
        $int = intval($data);
        if(($int === 0 && $data !== 0) || ($int === 0 && $data !== '0')) {
            return false;
        }
        if($this->type == self::POSITIVE) {
            return $this->allowZero === true ? $int >= 0 : $int > 0;
        } elseif($this->type == self::NEGATIVE) {
            return $this->allowZero === true ? $int <= 0 : $int < 0;
        } else {
            return $this->allowZero === true ? true : $int != 0;
        }
    }
}