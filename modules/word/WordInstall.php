<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 23.03.2015
 * Time: 21:22
 */
class WordInstall implements ModuleInstall{


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

        $word->bindTable('language', 'id', $languages->getName(), OrmTable::MANY_TO_ONE, true, true);
        $word->bindTable('module', 'id', $wordModules->getName(), OrmTable::MANY_TO_ONE, true, true);

        ORM::createTable($languages);
        ORM::createTable($wordModules);
        ORM::createTable($word);

        $langInstaller = new WordInstallLanguage();
        $languages = $langInstaller->install();

        $dictionariesInstaller = new WordInstallDictionary();
        $modules = $dictionariesInstaller->install();

        $wordInstaller = new WordInstallWords();
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
     * @return ModuleDependency[]
     */
    public function getDependencies()
    {
        return array(
            new ModuleDependency("DB")
        );
    }


    private function getLanguagesTable(){
        $languages = new OrmTable("languages");
        $id = new OrmTableField('id', 'integer');
        $id->setAutoIncrement(true)->setPrimary(true);
        $languages->addField($id);

        $locale = new OrmTableField('locale', 'varchar');
        $locale->setLength(2);
        $languages->addField($locale);

        $isDefault = new OrmTableField('is_default', 'bit');
        $isDefault->setLength(1);
        $languages->addField($isDefault);
        return $languages;
    }

    private function getWordTable()
    {
        $out = new OrmTable("word");
        $id = new OrmTableField('id', 'integer');
        $id->setAutoIncrement(true)->setPrimary(true);
        $out->addField($id);

        $language = new OrmTableField('language', 'integer');
        $language->setLength(11)->setIndex(true);
        $out->addField($language);

        $module = new OrmTableField('module', 'varchar');
        $module->setLength(100)->setIndex(true);
        $out->addField($module);

        $name = new OrmTableField('name', 'varchar');
        $name->setLength(100)->setIndex(true);
        $out->addField($name);

        $value = new OrmTableField('value', 'text');
        $out->addField($value);

        return $out;
    }

    private function getModulesTable()
    {
        $out = new OrmTable("word_modules");
        $id = new OrmTableField('id', 'integer');
        $id->setAutoIncrement(true)->setPrimary(true);
        $out->addField($id);

        $module = new OrmTableField('module', 'varchar');
        $module->setLength(50)
            ->setIndex(true);
        $out->addField($module);

        return $out;
    }

    /**
     * Get user input group
     * @return ModuleInput
     */
    public function getUserInput()
    {
        // TODO: Implement getUserInput() method.
    }
}