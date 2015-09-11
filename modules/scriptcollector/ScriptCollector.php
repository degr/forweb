<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 01.05.2015
 * Time: 21:37
 */
class ScriptCollector{
    /**
     * @var array
     */
    private static $scripts;

    public static function collect($name, $external = false){
        if(self::$scripts === null) {
            self::$scripts = array();
        }
        if(!$external){

            if(!is_file('js/'.$name)) {
                throw new FwException("Script with name: " . $name . " not exist.");
            } else {
                $name = CoreConfig::getUrl()."js/".$name;
            }
        }
        if(!in_array($name, self::$scripts)){
            self::$scripts[] = $name;
        }
    }

    public static function get(){
        if(!is_array(self::$scripts)) {
            return '';
        }
        $out = '';
        foreach(self::$scripts as $script) {
            $out .= '<script src="'.$script."\"></script>\n";
        }
        return $out;
    }
}