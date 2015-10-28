<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 01.05.2015
 * Time: 21:37
 */
class ScriptCollector{
    const JS_PACKAGE = "js/";
    /**
     * @var array
     */
    private static $scripts;

    public static function collect($name, $external = false){
        if(!$external){
            $name = self::fixName(true, $name);
        }
        self::collectInternal($name);
    }

    public static function collectPackage($packageName){
        $files = self::collectPackageFiles($packageName, true);
        foreach($files as $file) {
            if(is_file($file)) {
                $src = self::fixName(false, $file);
                self::collectInternal($src);
            }
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
    public static function getScripts(){
        if(!is_array(self::$scripts)) {
            return array();
        }
        return self::$scripts;
    }
    
    private static function collectPackageFiles($packageName, $withPackage)
    {
        $files = glob(preg_replace('/\/$/', '', ($withPackage ? self::JS_PACKAGE : "").$packageName). '/*');
        $inner = array();
        foreach($files as $file) {
            if(is_dir($file)) {
                $inner = array_merge($inner, self::collectPackageFiles($file, false));
            }
        }
        return array_merge($inner, $files);
    }

    private static function fixName($withPackageCheck, $name)
    {
        if(!is_file($withPackageCheck ? self::JS_PACKAGE.$name : $name)) {
            throw new FwException("Script with name: " . $name . " not exist.");
        } else {
            $name = CoreConfig::getUrl().($withPackageCheck ? self::JS_PACKAGE.$name : $name);
        }
        return $name;
    }

    private static function collectInternal($name)
    {
        if(self::$scripts === null) {
            self::$scripts = array();
        }
        if(!in_array($name, self::$scripts)){
            self::$scripts[] = $name;
        }
    }
}