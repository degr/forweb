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
                $parameters = $method->getParameters();
                echo $method."<br>";
                if(count($parameters) != 1) {
                    continue;
                }
                $class = $parameters[0]->getClass();
                if(empty($class)) {
                    continue;
                }
                if($class->getName() !== 'UI') {
                    continue;
                }
                $out[$method->name] = $method->name;
            }
            return $out;
        } else {
            return array();
        }
    }
}