<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 29.03.2015
 * Time: 11:28
 */

class WordInstallDictionary {
    public function install(){
        $requiredModules = array(Core::SYS_INCLUDES, 'admin', 'common', 'user', 'word');
        $query = "SELECT id, module FROM word_modules WHERE module IN ('".implode("','", $requiredModules)."')";
        $modules = DB::getAssoc($query,'module','id');
        foreach($requiredModules as $module) {
            if(empty($modules[$module])) {
                $query = "INSERT INTO word_modules (module) values ('".$module."')";
                DB::query($query);
                $module[$module] = DB::getCell("SELECT id FROM word_modules where module='".$module."'");
            }
        }
        return $modules;
    }
}