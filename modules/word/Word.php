<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 23.03.2015
 * Time: 21:11
 */
class Word extends Module{
    const GET_SHOW_KEYS = "show_keys";
    /**
     * Get module ajax handlers
     * @return AjaxHandler[]
     */
    public function getAjaxHandlers()
    {
        if($this->ajaxHandlers == null) {
            $this->ajaxHandlers = array(
                'onUiLanguagesOverview' => new AjaxHandler('onUiLanguagesOverview', AjaxHandler::JSON),
                'onAjaxSetDefaultLanguage' => new AjaxHandler('onAjaxSetDefaultLanguage', AjaxHandler::JSON),
                'onAjaxUpdateLanguage' => new AjaxHandler('onAjaxUpdateLanguage', AjaxHandler::TEXT),
                'deleteLanguage' => new AjaxHandler('onAjaxDeleteLanguage', AjaxHandler::TEXT),
                'deleteModule' => new AjaxHandler('onAjaxDeleteModule', AjaxHandler::TEXT),
                'onUiModulesOverview' => new AjaxHandler('onUiModulesOverview', AjaxHandler::JSON),
                'onAjaxUpdateModule' => new AjaxHandler('onAjaxUpdateModule', AjaxHandler::JSON),
                'showModuleTerms' => new AjaxHandler('onAjaxShowModuleTerms', AjaxHandler::JSON),
                'getTermForm' => new AjaxHandler('onAjaxGetTermForm', AjaxHandler::JSON),
                'saveTerm' => new AjaxHandler('onAjaxSaveTerm', AjaxHandler::JSON),
                'deleteTerm' => new AjaxHandler('onAjaxDeleteTerm', AjaxHandler::JSON)
            );
        }
        return $this->ajaxHandlers;
    }

    /**
     * Get module form handlers
     * @return FormHandler[]
     */
    public function getFormHandlers()
    {
        // TODO: Implement getFormHandlers() method.
    }

    protected static $language;

    protected static $languages;
    /**
     * Get translated word,
     * if term specified, or return all module translations
     * @param string $module
     * @param string $term - if specified string will be returned, if not - array with all terms for module
     * @param boolean $one  - if true, result will not be cached. More fast, better use in ajax queries.
     * @param string $language  - if specified, term for this language will be returned.
     * @return string|string[]
     * @throws FwException in some cases
     */
    public static function get($module, $term = '', $one = false, $language=''){
        static $cache;
        if(empty($module)) {
            throw new FwException("Can't return term without module.");
        }
        if($one && empty($term)) {
            throw new FwException("Can't return one term without name.");
        }
        if(isset($_GET[self::GET_SHOW_KEYS]) && Access::can("can_edit_terms")){
            return $module."::".(!empty($term) ? $term : "[ALL]");
        }

        $languageObject = Word::getLanguage($language);
        $languageId = $languageObject['id'];

        if(empty($cache[$languageId][$module]) && !$one) {
            $query = "SELECT name, value FROM word WHERE language='".DB::escape($languageId)."'";
            $cache[$languageId][$module] = DB::getAssoc($query, 'name', 'value');
        } else if($one) {
            $out = DB::getCell("SELECT value FROM word WHERE language='".DB::escape($languageId)."' AND name='"
                .DB::escape($term)."'");
            return empty($out) ? '['.$module."::".$term.']' : $out;
        }

        if(empty($term)) {
            return $cache[$languageId][$module];
        }


        if(!empty($cache[$languageId][$module][$term])) {
            return $cache[$languageId][$module][$term];
        } else {
            return '['.$module."::".$term.']';
        }
    }


    /**
     * Get current active locale
     * @return array(id=>1,locale=>en,is_default=>1)
     */
    public static function getLanguage($languageName = ''){
        $languages = &Word::getLanguages();
        if(Word::$language == null) {
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
        if(!empty($languageName)) {
            foreach($languages as $language) {
                if($languageName == $language['name']){
                    return $language;
                }
            }
            return null;
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


    /**
     * Ajax handler
     * @return array
     */
    public function onUiLanguagesOverview(){
        $provider = new Word_UI();
        return $provider->onUiLanguagesOverview();
    }

    public function onAjaxSetDefaultLanguage(){
        Access::denied('can_edit_languages');
        $provider = new Word_Actions();
        return $provider->onAjaxSetDefaultLanguage();
    }
    public function onAjaxUpdateLanguage(){
        Access::denied('can_edit_languages');
        $provider = new Word_Actions();
        return $provider->onAjaxUpdateLanguage();
    }
    public function onAjaxDeleteLanguage(){
        Access::denied('can_edit_languages');
        $provider = new Word_Actions();
        return $provider->deleteLanguage();
    }

    public function onAjaxDeleteModule(){
        Access::denied('can_edit_terms');
        $provider = new Word_Actions();
        return $provider->deleteModule();
    }
    public function onUiModulesOverview(){
        Access::denied('can_edit_terms');
        $provider = new Word_UI();
        return $provider->onUiModulesOverview();
    }
    public function onAjaxUpdateModule(){
        Access::denied('can_edit_terms');
        $provider = new Word_Actions();
        return $provider->onAjaxUpdateModule();
    }
    public function onAjaxShowModuleTerms(){
        Access::denied('can_edit_terms');
        $provider = new Word_UI();
        return $provider->onAjaxShowModuleTerms();
    }
    public function onAjaxGetTermForm(){
        Access::denied('can_edit_terms');
        $provider = new Word_UI();
        return $provider->onAjaxGetTermForm($_POST['id'], $_POST['module']);
    }
    public function onAjaxSaveTerm(){
        Access::denied('can_edit_terms');
        $provider = new Word_Actions();
        return $provider->onAjaxSaveTerm();
    }
    public function onAjaxDeleteTerm(){
        Access::denied('can_edit_terms');
        $provider = new Word_Actions();
        $id = intval($_POST['id']);
        return $provider->onAjaxDeleteTerm($id);
    }

}