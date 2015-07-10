<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/10/2015
 * Time: 2:44 PM
 */
 class Validation_Rule_Length implements Validation_IRule{

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
             $count = count($data);
             return ($this->min == -1 || $count >= $this->min) && ($this->max == -1 || $count <= $this->max);
         }
     }
 }