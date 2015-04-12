<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 26.03.2015
 * Time: 20:37
 */

class Word_UI {
    public function onUiLanguagesOverview(){
        $table = ORM::getTable("languages");

        $headers = array();
        foreach ($table->getFields() as $field) {
            $headers[$field->getName()] = Word::get("word", 'language_header_'.$field->getName());
        }
        unset($headers['id']);
        $query = "select * from languages";
        $data = DB::getTable($query);
        $form = UI::getOverviewTable($data, $headers, array('id'));
        $form['controls'] = array('delete' => array('href'=>'#','onclick'=>'Word.deleteItem(this, "deleteLanguage");return false', 'class'=>'icon-delete'));
        $form['editable'] = true;
        return $form;
    }

    public function onUiModulesOverview()
    {
        $table = ORM::getTable("word_modules");

        $headers = array();
        foreach ($table->getFields() as $field) {
            $headers[$field->getName()] = Word::get("word", 'module_header_'.$field->getName());
        }
        unset($headers['id']);
        $query = "select * from word_modules";
        $data = DB::getTable($query);
        $form = UI::getOverviewTable($data, $headers, array('id'));
        $form['controls'] = array(
            'delete' => array('href'=>'#','onclick'=>'Word.deleteItem(this, "deleteModule");return false', 'class'=>'icon-delete left'),
            'edit' => array('href' => '#','onclick'=>'Word.showModuleTerms(this);return false;','class'=>'icon-edit left')
        );
        $form['editable'] = true;
        return $form;
    }

    public function onAjaxShowModuleTerms()
    {
        $out = array();
        $module = intval($_POST['id']);
        $page = intval($_POST['page']);
        $useFilter = false;
        if(!empty($_POST['filters'])) {
            $filters = json_decode($_POST['filters'], true);
        }


        if(!empty($_POST['filter_name'])){
            $nameFilter = DB::escape($_POST['filter_name']);
            $useFilter = true;
        } else {
            $nameFilter = '';
        }
        if(!empty($_POST['filter_value'])){
            $valueFilter = DB::escape($_POST['filter_value']);
            $useFilter = true;
        }else{
            $valueFilter = '';
        }
        $wordsOnPage = 20;
        if(empty($module)) {
            return array();
        }
        $language = Word::getLanguage();
        if(empty($language['id'])) {
           return array();
        }
        $ids = DB::getColumn("SELECT id FROM word WHERE ".(!empty($module) ? 'module = '.$module : "")
            ." AND language = ".$language['id']
            .($useFilter
                ?
                    (!empty($nameFilter) ? " AND name LIKE '%".$nameFilter."%' " : "")
                    .(!empty($valueFilter) ? " AND value LIKE '%".$valueFilter."%'" : "")
                : ""
            )
            ." ORDER BY name ASC LIMIT ".($page * $wordsOnPage).", ".$wordsOnPage);

        if(empty($ids)) {
            $ids[] = -1;
        }
        $query = "SELECT w.id, w.language as languageId, l.locale as language, w.module as moduleId, m.module, w.name, w.value "
            ."FROM word as w "
            ."INNER JOIN word_modules as m ON w.module = m.id "
            ."INNER JOIN languages as l ON l.id = w.language WHERE w.id IN (".implode(",", $ids).")"
            ;
        $data = DB::getTable($query);
        $table = ORM::getTable("word");

        $headers = array();
        foreach ($table->getFields() as $field) {
            $headers[$field->getName()] = Word::get("word", 'term_header_'.$field->getName());
        }
        unset($headers['id']);

        $form = UI::getOverviewTable($data, $headers, array('id', 'langaugeId', 'moduleId'));
        $form['controls'] = array(
            'delete' => array('href'=>'#','onclick'=>'Word.deleteItem(this, "deleteTerm");return false', 'class'=>'icon-delete left'),
            'edit' => array('href' => '#','onclick'=>'Word.addTerm(this, '.$module.');return false;','class'=>'icon-edit left')
        );

        if(!empty($module)) {
            $out['title'] = DB::getCell("SELECT module FROM word_modules where id = ".$module);
        } else {
            $out['title'] = Word::get("common", 'all');
        }
        $paginator = array(
            'count' => DB::getCell("select count(1) from word where module = ".$module." AND language = ".$language['id']),
            'page' => $page,
            'itemsOnPage' => $wordsOnPage
        );

        $out['module'] = $module;
        $out['form'] = $form;
        $out['paginator'] = $paginator;
        $out['filters'] = $this->getFiltersForm();
        if(!empty($nameFilter)){
            $out['filters']['name']['value'] = $nameFilter;
        }
        if(!empty($valueFilter)){
            $out['filters']['value']['value'] = $valueFilter;
        }

        return $out;
    }

    public function onAjaxGetTermForm($id, $module)
    {
        $id = intval($id);
        $module = intval($module);
        if(empty($module)) {
            return array();
        }
        $currentLanguage = Word::getLanguage();
        if(empty($id)) {
            $data = array();
            $translations = array();
        } else {
            $data = DB::getRow("SELECT * FROM word WHERE id = ".$id." AND language = ".$currentLanguage['id']);
            if(!empty($data)) {
                $translations = DB::getAssoc(
                    "SELECT language, value FROM word WHERE name = '".DB::escape($data['name'])."'",
                    'language',
                    'value'
                );
            } else {
                $translations = array();
            }
        }
        $table = ORM::getTable("word");
        $form = UI::getFormForTable($table, $data, UI::LAYOUT_BLOCK);
        unset($form['fields']['language']);
        $form['fields']['module']['tag'] = UI::TAG_SELECT;
        $form['fields']['module']['options'] = DB::getAssoc('SELECT id, module FROM word_modules', 'id', 'module');
        if(!empty($module)) {
            $form['fields']['module']['value'] = $module;
        }
        $languages = DB::getAssoc("SELECT id, locale FROM languages", 'id', 'locale');

        $multylang = array(
            'tag' => UI::TAG_MULTYLANGUAGE,
            'name' => 'value',
            'layout' => UI::LAYOUT_GRID,
            'title' => Word::get('word', 'field_word'),
            'languages' => $languages,
            'language' => $currentLanguage['id'],
            'options' => $translations,
            'id' => 'word_term_value'
        );
        $form['fields']['value'] = $multylang;
        $form['fields']['submit'] = UI::getSubmitButton();

        return array(
            'form' => $form,
            'module' => $module
        );
    }

    /**
     * Return filters for term form
     */
    protected function getFiltersForm(){
        $table = ORM::getTable('word');
        $name = $table->getField('name');
        $value = $table->getField('value');

        $pseudo = new ORM_Table('pseudo_languages');
        $pseudo->addField($name);
        $pseudo->addField($value);
        $form = UI::getFormForTable($pseudo, array(), UI::LAYOUT_BLOCK);
        $form['id'] = 'word_filters';
        $form['fields']['value']['tag'] = UI::TAG_INPUT;
        unset($form['fields']['value']['title']);
        unset($form['fields']['name']['title']);
        $form['fields']['name']['attributes']['onblur'] ='Word.filterTerms()';
        $form['fields']['name']['attributes']['placeholder'] = Word::get('admin','word_name_filter');
        $form['fields']['value']['attributes']['onblur'] = 'Word.filterTerms()';
        $form['fields']['value']['attributes']['placeholder'] = Word::get('admin','word_value_filter');
        return $form;
    }
}