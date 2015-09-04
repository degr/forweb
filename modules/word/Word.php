<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 23.03.2015
 * Time: 21:11
 */
class Word extends Module{
    const GET_SHOW_KEYS = "show_keys";
    const SWITCH_LANGUAGE = "lang";
    const SESSION_LANGUAGE = 'language';
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
            $query = "SELECT w.name, w.value FROM word w INNER JOIN word_modules m ON m.id = w.module WHERE w.language='"
                .DB::escape($languageId)."' AND m.module= '".DB::escape($module)."'";
            $cache[$languageId][$module] = DB::getAssoc($query, 'name', 'value');

        } else if($one) {
            $out = DB::getCell("SELECT w.value FROM word w INNER JOIN word_modules m ON w.module = m.id WHERE w.language='"
                .DB::escape($languageId)."' AND w.name='".DB::escape($term)."' AND m.module='".DB::escape($module)."'");
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
    public static function getLanguage($locale = ''){
        $languages = &Word::getLanguages();
        if(Word::$language == null) {
            if(!empty($_SESSION[Word::SESSION_LANGUAGE]) && !empty($languages[$_SESSION[Word::SESSION_LANGUAGE]])) {
                Word::$language = $languages[$_SESSION[Word::SESSION_LANGUAGE]];
            }
            if(empty(Word::$language)) {
                foreach ($languages as $language) {
                    if (!empty($language['is_default'])) {
                        Word::$language = $language;
                        break;
                    }
                }
            }
            $_SESSION[Word::SESSION_LANGUAGE] = Word::$language['id'];
        }
        if(!empty($locale)) {
            foreach($languages as $language) {
                if($locale == $language['locale']){
                    return $language;
                }
            }
            return null;
        }
        return Word::$language;
    }


    public static function getLanguages(){
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
        $provider = new WordUi();
        return $provider->onUiLanguagesOverview();
    }

    public function onAjaxSetDefaultLanguage(){
        Access::denied('can_edit_languages');
        $provider = new WordActions();
        return $provider->onAjaxSetDefaultLanguage();
    }
    public function onAjaxUpdateLanguage(){
        Access::denied('can_edit_languages');
        $provider = new WordActions();
        return $provider->onAjaxUpdateLanguage();
    }
    public function onAjaxDeleteLanguage(){
        Access::denied('can_edit_languages');
        $provider = new WordActions();
        return $provider->deleteLanguage();
    }

    public function onAjaxDeleteModule(){
        Access::denied('can_edit_terms');
        $provider = new WordActions();
        return $provider->deleteModule();
    }
    public function onUiModulesOverview(){
        Access::denied('can_edit_terms');
        $provider = new WordUi();
        return $provider->onUiModulesOverview();
    }
    public function onAjaxUpdateModule(){
        Access::denied('can_edit_terms');
        $provider = new WordActions();
        return $provider->onAjaxUpdateModule();
    }
    public function onAjaxShowModuleTerms(){
        Access::denied('can_edit_terms');
        $provider = new WordUi();
        return $provider->onAjaxShowModuleTerms();
    }
    public function onAjaxGetTermForm(){
        Access::denied('can_edit_terms');
        $provider = new WordUi();
        return $provider->onAjaxGetTermForm($_POST['id'], $_POST['module']);
    }
    public function onAjaxSaveTerm(){
        Access::denied('can_edit_terms');
        $provider = new WordActions();
        return $provider->onAjaxSaveTerm();
    }
    public function onAjaxDeleteTerm(){
        Access::denied('can_edit_terms');
        $provider = new WordActions();
        $id = intval($_POST['id']);
        return $provider->onAjaxDeleteTerm($id);
    }

    public function getLanguageSwitchForm(UI $ui){
        if(!empty($_REQUEST[Word::SWITCH_LANGUAGE])) {
            $language = Word::getLanguage($_REQUEST[Word::SWITCH_LANGUAGE]);
            if(!empty($language) && $_SESSION[Word::SESSION_LANGUAGE] != $language['locale']) {
                $_SESSION[Word::SESSION_LANGUAGE] = $language['id'];
                $urlParts = parse_url($_SERVER['REQUEST_URI']);
                $url = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?') + 1);
                $query = explode("&", $urlParts['query']);
                foreach($query as $item) {
                    if($item == Word::SWITCH_LANGUAGE."=".$language['locale']){
                        continue;
                    }
                    $url.=$item;
                }
                CoreUtils::redirect($url);
            }
        }
        $ui->setLayout("word/language.switch.tpl");
        $ui->addVariable('languages', Word::getLanguages());
    }
    /**
     * fix url and make redirect if it necessary.
     * This function must be called only if Core::MULTIPLE_LANGUAGES == true && Core::LANGUAGE_IN_URL == true
     * @param $dispatcher PageDispatcher
     * @throws FwException
     */
    public static function onLanguageUrl(){
        $loc = Core::getPathParam(-1);
        /** @var $me Word*/
        $me = Core::getModule("Word");
        $language = null;
        $currentLanguage = $me->getLanguage();
        $onRedirectToDefault = $loc === '';
        if(!$onRedirectToDefault) {
            $language = $me->getLanguage($loc);
            $onRedirectToDefault = $language === null;
            if(!$onRedirectToDefault) {
                if($currentLanguage['id'] != $language['id']){
                    $_SESSION[Word::SESSION_LANGUAGE] = $language['id'];
                    Word::$language = $language;
                    $url = preg_replace("/".$currentLanguage['locale']."\/$/", '', Config::getLocalUrl());
                    $path = preg_replace("/^\/|".$loc."|\//", '', $_SERVER['REQUEST_URI']);
                    CoreUtils::redirect($url.$path);
                }
            }
        }
        if($onRedirectToDefault){
            if($currentLanguage === null) {
                throw new FwException("Can't process request. Language not found, but multylanguage site configured.");
            }
            $url = preg_replace("/\/$/", '', Config::getLocalUrl());
            CoreUtils::redirect($url.$_SERVER['REQUEST_URI']);
        }
    }
    /**
     * Get module event handlers
     * @return EventHandler[]
     */
    public function getEventHandlers()
    {
        // TODO: Implement getEventHandlers() method.
    }
}