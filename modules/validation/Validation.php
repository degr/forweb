<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/10/2015
 * Time: 2:41 PM
 */
 class Validation implements IModule{

     /**
      * Get module ajax handlers
      * @return ModuleAjaxHandler[]
      */
     public function getAjaxHandlers()
     {
         return null;
     }

     /**
      * Get module event handlers
      * @return ModuleEventHandler[]
      */
     public function getEventHandlers()
     {
         return null;
     }

     public static function validate($data, $rules){

     }
 }