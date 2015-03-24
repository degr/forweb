<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 23.03.2015
 * Time: 21:11
 */
class Word{

    protected static $language;

    protected static $languages;
    /**
     * Get translated word,
     * if term specified, or return all module translations
     * @param $module
     * @param string $term
     * @return string|string[]
     */
    public static function get($module, $term = '', $language=''){
        static $cache;
        $languageObject = Word::getLanguage();
        $languageId = $languageObject['id'];
        if(empty($cache[$language][$module])) {
            $query = "SELECT name, value FROM word WHERE language='".DB::escape($languageId)."'";
            $cache[$language][$module] = DB::getAssoc($query, 'name', 'value');
        }
        if(empty($term)) {
            return $cache[$module];
        }

        if(!empty($cache[$language][$module][$term])) {
            return $cache[$language][$module][$term];
        } else {
            return '';
        }
    }


    /**
     * Get current active locale
     * @return array(id=>1,locale=>en,is_default=>1)
     */
    public static function getLanguage(){
        if(Word::$language == null) {
            $languages = Word::getLanguages();
            if(!empty($_SESSION['language']) && !empty($languages[$_SESSION['language']])) {
                Word::$language = $languages[$_SESSION['language']];
            }
            if(empty(Word::$language)) {
                foreach ($languages as $language) {
                    if (!empty($language['is_default'])) {
                        Word::$language = $language;
                        break;
                    }
                }
            }
            $_SESSION['language'] = Word::$language['id'];
        }
        return Word::$language;
    }


    public function getLanguages(){
        if(Word::$languages == null) {
                Word::$languages = DB::getTable(
                    "SELECT * FROM languages",
                    'id'
                );
            }
        return Word::$languages;
    }
}