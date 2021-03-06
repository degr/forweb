<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/10/2015
 * Time: 5:42 PM
 */
class ValidationRuleRegExp implements ValidationRule
{
    private $regexp;
    public function __construct($regexp) {
        $this->regexp = $regexp;
    }


    public function validate($data)
    {
        preg_match($this->regexp, $data, $matches);
        return count($matches) > 0;
    }
}