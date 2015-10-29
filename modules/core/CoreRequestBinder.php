<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 10/28/2015
 * Time: 3:28 PM
 */
class CoreRequestBinder{
    public static function bind($classOrObject, $data, $listItemClass = null){
        if(empty($classOrObject)) {
            throw new FwException("Can't bind data, because class name or object is null.");
        }
        $type = gettype($classOrObject);
        $object = $type === 'object' || $type === 'array'
            ? $classOrObject 
            : ($type === 'string' ? new $classOrObject() : null);
        if($object === null) {
            throw new FwException("Can't bind data, because object class provider have invalid value.");
        }
        if(!is_array($data)) {
            return $object;
        }
        if($listItemClass === null) {
            foreach ($data as $key => $value) {
                $setter = 'set' . ucfirst($key);
                if (method_exists($object, $setter)) {
                    $data->$setter($value);
                } else if (property_exists($object, $key)) {
                    $object->$key = $value;
                }
            }
        } else {
            foreach($data as $key => $value) {
                $classOrObject[] = self::bind($listItemClass, $value, null);
            }
        }
        return $object;
    }
}