<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/10/2015
 * Time: 3:19 PM
 */
class ValidationRuleUrl extends ValidationRuleRegExp
{
    public function __construct(){
        parent::__construct('/^http\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?$/');
    }
}