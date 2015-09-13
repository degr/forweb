<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 12:02
 */
class CoreInstallTables{

    public function getPagesTable(){
        $table = new OrmTable('pages');
        $id = new OrmTableField("id", "integer");
        $id->setAutoIncrement(true)->setPrimary(true)->setLength(11);
        $table->addField($id);

        $name = new OrmTableField("name", "varchar");
        $name->setLength(50);
        $table->addField($name);

        $url = new OrmTableField("url", "varchar");
        $url->setLength(50);
        $table->addField($url);

        $parent = new OrmTableField("parent", "integer");
        $parent->setLength(11);
        $table->addField($parent);

        $template = new OrmTableField("template", "integer");
        $template->setLength(11);
        $table->addField($template);

        $position = new OrmTableField("position", "integer");
        $position->setLength(11);
        $table->addField($position);

        $active = new OrmTableField("active", "bit");
        $active->setLength(1);
        $table->addField($active);

        $inMenu = new OrmTableField("in_menu", "bit");
        $inMenu->setLength(1);
        $table->addField($inMenu);

        $paramsCount = new OrmTableField("params_count", "integer");
        $paramsCount->setLength(11);
        $table->addField($paramsCount);

        return $table;
    }



    public function getTemplatesTable(){
        $table = new OrmTable("templates");
        $id = new OrmTableField("id", "integer");
        $id->setAutoIncrement(true)->setPrimary(true)->setLength(11);
        $table->addField($id);

        $name = new OrmTableField("name", "varchar");
        $name->setLength(50);
        $table->addField($name);

        $value = new OrmTableField("parent", "integer");
        $value->setLength(50);
        $table->addField($value);

        $template = new OrmTableField("template", "varchar");
        $template->setLength(255);
        $table->addField($template);


        return $table;
    }

    public function getIncludesTable(){
        $table = new OrmTable("includes");
        $id = new OrmTableField("id", "integer");
        $id->setAutoIncrement(true)->setPrimary(true)->setLength(11);
        $table->addField($id);

        $page = new OrmTableField("page", "integer");
        $page->setLength(11);
        $page->setDefaultValue(0);
        $table->addField($page);

        $template = new OrmTableField("template", "integer");
        $template->setLength(11);
        $template->setDefaultValue(0);
        $table->addField($template);

        $type = new OrmTableField("type", "enum");
        $type->setEnumValues(array('html', 'text', 'image', 'executable'));
        $table->addField($type);

        $block = new OrmTableField("block", "integer");
        $block->setLength(11);
        $table->addField($block);

        $positionNumber = new OrmTableField("positionNumber", "integer");
        $positionNumber->setLength(11);
        $table->addField($positionNumber);

        $position = new OrmTableField("position", "enum");
        $position->setEnumValues(array('before', 'template', 'after'));
        $table->addField($position);

        $content = new OrmTableField("content", "text");
        $table->addField($content);

        $className = new OrmTableField("module", "varchar");
        $className->setLength(50);
        $table->addField($className);

        $method = new OrmTableField("method", "varchar");
        $method->setLength(50);
        $table->addField($method);

        $comment = new OrmTableField("comment", "varchar");
        $comment->setLength(100);
        $table->addField($comment);

        return $table;
    }

    public function getBlocksTable(){
        $table = new OrmTable("blocks");
        $id = new OrmTableField("id", "integer");
        $id->setAutoIncrement(true)->setPrimary(true)->setLength(11);
        $table->addField($id);

        $name = new OrmTableField("name", "varchar");
        $name->setLength(100);
        $table->addField($name);

        $position = new OrmTableField("position", "integer");
        $position->setLength(11);
        $table->addField($position);

        $template = new OrmTableField("template", "integer");
        $template->setLength(11);
        $table->addField($template);

        return $table;
    }
}