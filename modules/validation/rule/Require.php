<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/10/2015
 * Time: 2:43 PM
 */
 class Validation_Rule_Require implements Validation_IRule{

     public function validate($data)
     {
         return !empty($data) || $data === 0;
     }
 }