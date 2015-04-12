<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 23.03.2015
 * Time: 21:22
 */
class Word_Install implements Module_IInstall{


    /**
     * Make all necessary preparations for this module
     * as PersistTables deploy, cache files creating etc..
     * @return void
     */
    public function install()
    {
        $languages = $this->getLanguagesTable();
        $wordModules = $this->getModulesTable();
        $word = $this->getWordTable();
        ORM::registerTableOnFly($languages);
        ORM::registerTableOnFly($wordModules);
        ORM::registerTableOnFly($word);

        $word->bindTable('language', 'id', $languages->getName(), ORM_Table::MANY_TO_ONE, true, true);
        $word->bindTable('module', 'id', $wordModules->getName(), ORM_Table::MANY_TO_ONE, true, true);

        ORM::createTable($languages);
        ORM::createTable($wordModules);
        ORM::createTable($word);

        $langInstaller = new Word_Install_Language();
        $languages = $langInstaller->install();

        $dictionariesInstaller = new Word_Install_Dictionary();
        $modules = $dictionariesInstaller->install();

        $wordInstaller = new Word_Install_Words();
        $wordInstaller->install($languages, $modules);
    }

    /**
     * Return information about this module
     * @return string
     */
    public function getInfo()
    {
        ob_start();
        echo '<h3>ForWeb framework Translations package</h3>';
        echo '<p>Contain three tables: language, word_module and word. Language table contain info about site languages, '
            .' word modules about this module modules, word table contain text translations for all languages. '
            .'Each term from word table have alias to language and to module.</p>';
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    /**
     * get module dependencies for deploy
     * @return Module_Dependency[]
     */
    public function getDependencies()
    {
        return array(
            new Module_Dependency("DB")
        );
    }


    private function getLanguagesTable(){
        $languages = new ORM_Table("languages");
        $id = new ORM_Table_Field('id', 'integer');
        $id->setAutoIncrement()->setPrimary();
        $languages->addField($id);

        $locale = new ORM_Table_Field('locale', 'varchar');
        $locale->setLength(2);
        $languages->addField($locale);

        $isDefault = new ORM_Table_Field('is_default', 'bit');
        $isDefault->setLength(1);
        $languages->addField($isDefault);
        return $languages;
    }

    private function getWordTable()
    {
        $out = new ORM_Table("word");
        $id = new ORM_Table_Field('id', 'integer');
        $id->setAutoIncrement()->setPrimary();
        $out->addField($id);

        $language = new ORM_Table_Field('language', 'integer');
        $language->setLength(11)
            ->setIndex(true);
        $out->addField($language);

        $module = new ORM_Table_Field('module', 'varchar');
        $module->setLength(100)
            ->setIndex(true);
        $out->addField($module);

        $name = new ORM_Table_Field('name', 'varchar');
        $name->setLength(100)
            ->setIndex(true);
        $out->addField($name);

        $value = new ORM_Table_Field('value', 'text');
        $out->addField($value);

        return $out;
    }

    private function getModulesTable()
    {
        $out = new ORM_Table("word_modules");
        $id = new ORM_Table_Field('id', 'integer');
        $id->setAutoIncrement()->setPrimary();
        $out->addField($id);

        $module = new ORM_Table_Field('module', 'varchar');
        $module->setLength(50)
            ->setIndex(true);
        $out->addField($module);

        return $out;
    }

}