<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/10/2015
 * Time: 2:44 PM
 */
 class ValidationRuleLength implements ValidationRule{

     private $min;
     private $max;

     public function __construct($min = -1, $max = -1){
         if($min == -1 && $max == -1) {
             throw new FwException("Can't create validation rules, malformed params.");
         }
         $this->min = $min;
         $this->max = $max;
     }

     public function validate($data)
     {
         if(is_array($data)) {
             $length = count($data);
         } else if(is_integer($data)) {
             $length = $data;
         } else if(is_string($data)) {
             $length = mb_strlen($data);
         } else {
             $length = -1;
         }
         return ($this->min == -1 || $length >= $this->min) && ($this->max == -1 || $length <= $this->max);
     }
 }