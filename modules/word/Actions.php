<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 26.03.2015
 * Time: 21:43
 */
class Word_Actions{

    public function onAjaxSetDefaultLanguage()
    {
        $id = intval($_POST['id']);
        if(empty($id)) {
            return 0;
        }
        $query = "UPDATE languages SET is_default = 0";
        DB::query($query);
        $query = "UPDATE languages SET is_default = 1 WHERE id = ".$id;
        DB::query($query);
        return 1;
    }

    public function onAjaxUpdateLanguage()
    {
        $id = intval($_POST['id']);
        $locale = DB::escape($_POST['item']);

        if(empty($locale)) {
            return 0;
        }
        if(empty($id)) {
            $query = "SELECT id from languages where locale = '".$locale."'";
            $id = DB::getCell($query);
            if(!empty($id)) {
                return 0;
            }
            $query = "INSERT INTO languages (locale, is_default) values ('".$locale."', 0)";
            DB::query($query);
            $query = "select id from languages where locale = '".$locale."'";
            return DB::getCell($query);
        } else {
            $query = "update languages set locale = '".$locale."' WHERE id=".$id;
            DB::query($query);
            return $id;
        }
    }

    public function deleteLanguage()
    {
        $id = intval($_POST['id']);
        if(empty($id)) {
            return 0;
        }
        $query = "DELETE FROM word where language = ".$id;
        DB::query($query);
        $query = "DELETE FROM languages where id =".$id;
        DB::query($query);
        return 1;
    }

    public function onAjaxUpdateModule()
    {
        $id = intval($_POST['id']);
        $module = DB::escape($_POST['item']);

        if(empty($module)) {
            return 0;
        }
        if($this->isSysIncludes($id)) {
            return 0;
        }
        if(empty($id)) {
            $query = "SELECT id from word_modules where module = '".$module."'";
            $id = DB::getCell($query);
            if(!empty($id)) {
                return 0;
            }
            $query = "INSERT INTO word_modules (module) values ('".$module."')";
            DB::query($query);
            $query = "select id from word_modules where module = '".$module."'";
            return DB::getCell($query);
        } else {
            $query = "update word_modules set module = '".$module."' WHERE id=".$id;
            DB::query($query);
            return $id;
        }
    }

    public function deleteModule()
    {
        $id = intval($_POST['id']);
        if(empty($id)) {
            return 0;
        }
        if($this->isSysIncludes($id)){
            return 0;
        }
        $query = "DELETE FROM word where module = ".$id;
        DB::query($query);
        $query = "DELETE FROM word_modules where id =".$id;
        DB::query($query);
        return 1;
    }

    protected function isSysIncludes($id){
        $query = "select module from word_modules where id = ".$id;
        $cell = DB::getCell($query);
        if($cell == Core::SYS_INCLUDES){
            return true;
        } else {
            return false;
        }
    }

    public function onAjaxSaveTerm()
    {
        $id = intval($_POST['id']);
        $module = intval($_POST['module']);
        $name = DB::escape($_POST['name']);
        if(empty($module)) {
            return array('errors' => 1, 'text'=>Word::get("word", 'module_not_specified', true));
        }
        if(empty($name)) {
            return array('errors' => 1, 'text'=>Word::get("word", 'term_name_not_specified', true));
        }
        if(empty($id)) {
            $checkId = DB::getCell("SELECT id FROM word WHERE name = '" . $name . "' AND module='" . $module . "'");
            if(!empty($checkId)) {
                return array('errors' => 1, 'text'=>Word::get('word', 'term_already_exist', true));
            }
        }
        $values = array();
        foreach($_POST['value'] as $key => $value) {
            $val = array();
            $val['language'] = DB::escape($key);
            $val['module'] = DB::escape($module);
            $val['name'] = DB::escape($name);
            $val['value'] = DB::escape($value);
            $values[] = $val;
        }
        if(empty($values)) {
            return array('errors' => 1, 'text'=>Word::get("word", 'values_not_specified', true));
        }
        if(empty($id)) {
            $query = "INSERT INTO word (language, module, name, value) VALUES ";
            $valuesString = array();
            foreach($values as $val) {
                $valuesString[] = "('".implode("', '", $val)."')";
            }
            DB::query($query.implode(',', $valuesString));
            return array(
                'errors' => 0,
                'text' => Word::get('word', 'new_term_created', true),
                'id' => DB::getCell('SELECT id FROM word WHERE name="'.$name.'" AND module='.$module)
            );
        } else {
            $name = DB::getCell("SELECT name FROM word WHERE id='".$id."'");

            $ids = DB::getAssoc("SELECT language, id FROM word WHERE name = '".$name."'", 'language', 'id');
            /* @var $wordModule Word */
            $wordModule = Core::getModule("Word");
            $languages = $wordModule->getLanguages();
            $checkForNewLanguage = true;
            foreach($languages as $key => $language) {
                if(empty($ids[$key])) {
                    DB::query("INSERT INTO word (language, module, name, value) VALUES "
                        ."(".$key.", ".$module.", ".$name.", '')"
                    );
                    if($checkForNewLanguage) {
                        $checkForNewLanguage = false;
                    }
                }
            }
            if(!$checkForNewLanguage) {
                $ids = DB::getAssoc("SELECT language, id FROM word WHERE name = '".$name."'", 'language', 'id');
            }

            $queryStart = "UPDATE word SET ";
            $queryEnd = "WHERE id = ";
            foreach($values as $val) {
                $toUpdate = array();
                foreach($val as $key => $value) {
                    $toUpdate[] = $key . "='" . DB::escape($value) . "'";
                }
                DB::query($queryStart.implode(",",$toUpdate).$queryEnd.$ids[$val['language']]);
            }
            return array('errors' => 0, 'text' => Word::get('word', 'terms_updated', true));
        }
    }

    public function onAjaxDeleteTerm($id)
    {
        if(empty($id)) {
            return 0;
        }
        $obj = DB::getRow("SELECT name, module FROM word WHERE id = ".$id);
        if(empty($obj)) {
            return 0;
        }

        $query = "DELETE FROM word WHERE "
            ."name='".DB::escape($obj['name'])."' AND module = '".DB::escape($obj["module"])."'";
        DB::query($query);
        return 1;
    }
}