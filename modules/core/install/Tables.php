<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 12:02
 */
class Core_Install_Tables{

    public function getPagesTable(){
        $table = new ORM_Table('pages');
        $id = new ORM_Table_Field("id", "integer");
        $id->setAutoIncrement(true)->setPrimary(true)->setLength(11);
        $table->addField($id);

        $name = new ORM_Table_Field("name", "varchar");
        $name->setLength(50);
        $table->addField($name);

        $url = new ORM_Table_Field("url", "varchar");
        $url->setLength(50);
        $table->addField($url);

        $parent = new ORM_Table_Field("parent", "integer");
        $parent->setLength(11);
        $table->addField($parent);

        $template = new ORM_Table_Field("template", "integer");
        $template->setLength(11);
        $table->addField($template);

        $position = new ORM_Table_Field("position", "integer");
        $position->setLength(11);
        $table->addField($position);

        $active = new ORM_Table_Field("active", "bit");
        $active->setLength(1);
        $table->addField($active);

        $inMenu = new ORM_Table_Field("in_menu", "bit");
        $inMenu->setLength(1);
        $table->addField($inMenu);

        return $table;
    }



    public function getTemplatesTable(){
        $table = new ORM_Table("templates");
        $id = new ORM_Table_Field("id", "integer");
        $id->setAutoIncrement(true)->setPrimary(true)->setLength(11);
        $table->addField($id);

        $name = new ORM_Table_Field("name", "varchar");
        $name->setLength(50);
        $table->addField($name);

        $value = new ORM_Table_Field("parent", "integer");
        $value->setLength(50);
        $table->addField($value);

        $template = new ORM_Table_Field("template", "varchar");
        $template->setLength(255);
        $table->addField($template);


        return $table;
    }

    public function getIncludesTable(){
        $table = new ORM_Table("includes");
        $id = new ORM_Table_Field("id", "integer");
        $id->setAutoIncrement(true)->setPrimary(true)->setLength(11);
        $table->addField($id);

        $page = new ORM_Table_Field("page", "integer");
        $page->setLength(11);
        $page->setDefaultValue(0);
        $table->addField($page);

        $template = new ORM_Table_Field("template", "integer");
        $template->setLength(11);
        $template->setDefaultValue(0);
        $table->addField($template);

        $type = new ORM_Table_Field("type", "enum");
        $type->setEnumValues(array('html', 'text', 'image', 'executable'));
        $table->addField($type);

        $block = new ORM_Table_Field("block", "integer");
        $block->setLength(11);
        $table->addField($block);

        $positionNumber = new ORM_Table_Field("positionNumber", "integer");
        $positionNumber->setLength(11);
        $table->addField($positionNumber);

        $position = new ORM_Table_Field("position", "enum");
        $position->setEnumValues(array('before', 'template', 'after'));
        $table->addField($position);

        $content = new ORM_Table_Field("content", "text");
        $table->addField($content);

        $className = new ORM_Table_Field("module", "varchar");
        $className->setLength(50);
        $table->addField($className);

        $method = new ORM_Table_Field("method", "varchar");
        $method->setLength(50);
        $table->addField($method);

        $comment = new ORM_Table_Field("comment", "varchar");
        $comment->setLength(100);
        $table->addField($comment);

        return $table;
    }

    public function getBlocksTable(){
        $table = new ORM_Table("blocks");
        $id = new ORM_Table_Field("id", "integer");
        $id->setAutoIncrement(true)->setPrimary(true)->setLength(11);
        $table->addField($id);

        $name = new ORM_Table_Field("name", "varchar");
        $name->setLength(100);
        $table->addField($name);

        $position = new ORM_Table_Field("position", "integer");
        $position->setLength(11);
        $table->addField($position);

        $template = new ORM_Table_Field("template", "integer");
        $template->setLength(11);
        $table->addField($template);

        return $table;
    }
}