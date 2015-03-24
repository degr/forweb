<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 15.03.2015
 * Time: 15:31
 */

class Page_MethodList {

    public function getContent($moduleName)
    {
        if($moduleName == null) {
            if(empty($_REQUEST['moduleName'])) {
                return array();
            } else {
                $moduleName = $_REQUEST['moduleName'];
            }
        }
        if(Core::isModuleExist($moduleName)){
            $out = array();
            $reflectObj = new ReflectionClass($moduleName);
            $methods = $reflectObj->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach($methods as $method) {
                if(strtolower($method->name) !== '__construct') {
                    $out[$method->name] = $method->name;
                }
            }
            return $out;
        } else {
            return array();
        }
    }
}