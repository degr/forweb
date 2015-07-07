<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/7/2015
 * Time: 6:28 PM
 */
 class Cache{

     const DATA_PREFFIX = '.data';
     const TIME_START = 'time';
     const TIME_KEEP = 'keep';

     public function save($object, $module, $key, $time = 0){
         $folder = 'Cache/'.$module;
         if(!is_dir($folder)) {
             mkdir($folder, 0777, true);
         }
         $file = $folder.'/'.md5($key);
         file_put_contents($file, serialize($object));
         if($time != 0) {
             file_put_contents(
                 $file.self::DATA_PREFFIX,
                 json_encode(array(self::TIME_START=>time(), self::TIME_KEEP => $time))
             );
         }
     }

     public function load($module, $key){
         $file = 'Cache/'.$module.'/'.md5($key);
         if(is_file($file)) {
             $inTime = true;
             if(is_file($file.self::DATA_PREFFIX)) {
                 $description = json_decode(file_get_contents($file.self::DATA_PREFFIX));
                 $inTime = (time() - $description[self::TIME_START] - $description[self::TIME_KEEP]) > 0;
                 if(!$inTime) {
                     unlink($file);
                 }
             }
             return $inTime ? unserialize(file_get_contents($file)) : null;
         } else {
             return null;
         }
     }
 }