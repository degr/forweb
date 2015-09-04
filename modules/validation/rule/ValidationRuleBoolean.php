<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 8/6/2015
 * Time: 3:30 PM
 */
class ValidationRuleBoolean implements ValidationRule{

    public function validate($data)
    {
        return self::convert($data) != null;
    }

    public static function convert($value) {
        if($value === true || $value === 1 || $value === '1' || $value == 'on' || $value === 'true'
            || $value === 'TRUE' || $value === 'Y' ) {
            return true;
        } else if ( $value === false  ||  $value === 0 || $value === 'false' || $value === 'FALSE' || $value === 'off'
            || $value === 'OFF' || $value == '0') {
            return false;
        } else {
            return null;
        }
    }
}