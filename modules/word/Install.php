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
        $word = $this->getWordTable();
        ORM::registerTableOnFly($languages);
        ORM::registerTableOnFly($word);

        $word->bindTable('language', 'id', $languages->getName(), ORM_Objects_Table::MANY_TO_ONE, true, true);

        ORM::createTable($languages);
        ORM::createTable($word);

        $query = "SELECT id FROM languages ";
        $ids = DB::getColumn($query);
        if(empty($ids)) {
            $query = "INSERT INTO languages (locale, is_default) VALUES"
                ."('en', 1), ('ru', 0)";
            DB::query($query);

            $query = "INSERT INTO `word` "
                ."(`id`, `language`, `module`, `name`, `value`)"
                ." VALUES "
                ." (1,'1','user','field_email','email'),"
                ." (2,'2','user','field_email','Емейл'),"
                ." (3,'1','user','field_password','Password'),"
                ." (4,'2','user','field_password','Пароль')";
            DB::query($query);

        }
    }

    /**
     * Return information about this module
     * @return string
     */
    public function getInfo()
    {
        ob_start();
        echo '<h3>ForWeb framework Translations package</h3>';
        echo '<p>Contain two tables: language and word. Language table contain info about site languages, '
            .'word table contain text translations for all languages.</p>';
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
        $languages = new ORM_Objects_Table("languages");
        $id = new ORM_Objects_Field('id', 'integer');
        $id->setAutoIncrement()->setPrimary();
        $languages->addField($id);

        $locale = new ORM_Objects_Field('locale', 'varchar');
        $locale->setLength(2);
        $languages->addField($locale);

        $isDefault = new ORM_Objects_Field('is_default', 'bit');
        $isDefault->setLength(1);
        $languages->addField($isDefault);
        return $languages;
    }

    private function getWordTable()
    {
        $out = new ORM_Objects_Table("word");
        $id = new ORM_Objects_Field('id', 'integer');
        $id->setAutoIncrement()->setPrimary();
        $out->addField($id);

        $language = new ORM_Objects_Field('language', 'integer');
        $language->setLength(11)
            ->setIndex(true);
        $out->addField($language);

        $module = new ORM_Objects_Field('module', 'varchar');
        $module->setLength(100)
            ->setIndex(true);
        $out->addField($module);

        $name = new ORM_Objects_Field('name', 'varchar');
        $name->setLength(100)
            ->setIndex(true);
        $out->addField($name);

        $value = new ORM_Objects_Field('value', 'text');
        $out->addField($value);

        return $out;
    }

}