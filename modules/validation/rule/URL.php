<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/10/2015
 * Time: 3:19 PM
 */
class Validation_Rule_Url extends Validation_Rule_RegExp
{
    public function __construct(){
        parent::__construct('/^http\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?$/');
    }
}