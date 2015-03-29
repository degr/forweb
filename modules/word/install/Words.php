<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 29.03.2015
 * Time: 11:47
 */

class Word_Install_Words {

    public function install($languages, $modules)
    {
        $insertQuery = "INSERT INTO word (language, module, name, value) VALUES ";
        $check = DB::getAssoc("SELECT module, name FROM word WHERE language = 10", "module", "name");
        if(!isset($check[4]["panel_new_page"])){
            $items[] = "(10,4,'panel_new_page','New page')";
        }
        if(!isset($check[4]["word_reset_term"])){
            $items[] = "(10,4,'word_reset_term','')";
        }
        if(!isset($check[4]["panel_edit_page"])){
            $items[] = "(10,4,'panel_edit_page','This page')";
        }
        if(!isset($check[4]["panel_show_pages"])){
            $items[] = "(10,4,'panel_show_pages','Pages tree')";
        }
        if(!isset($check[4]["panel_page_content"])){
            $items[] = "(10,4,'panel_page_content','Page content')";
        }
        if(!isset($check[4]["panel_templates"])){
            $items[] = "(10,4,'panel_templates','Templates')";
        }
        if(!isset($check[4]["panel_edit_access"])){
            $items[] = "(10,4,'panel_edit_access','Access')";
        }
        if(!isset($check[4]["panel_word"])){
            $items[] = "(10,4,'panel_word','Translates')";
        }
        if(!isset($check[4]["panel_configuration"])){
            $items[] = "(10,4,'panel_configuration','Configuration')";
        }
        if(!isset($check[4]["panel_position"])){
            $items[] = "(10,4,'panel_position','< Position >')";
        }
        if(!isset($check[4]["panel_languages"])){
            $items[] = "(10,4,'panel_languages','Languages')";
        }
        if(!isset($check[4]["panel_modules"])){
            $items[] = "(10,4,'panel_modules','Dictionaries')";
        }
        if(!isset($check[4]["panel_create_template"])){
            $items[] = "(10,4,'panel_create_template','Create template')";
        }
        if(!isset($check[4]["panel_edit_template"])){
            $items[] = "(10,4,'panel_edit_template','Edit template')";
        }
        if(!isset($check[4]["panel_delete_template"])){
            $items[] = "(10,4,'panel_delete_template','Delete template')";
        }
        if(!isset($check[4]["page_form_field_name"])){
            $items[] = "(10,4,'page_form_field_name','Page name')";
        }
        if(!isset($check[4]["page_form_field_url"])){
            $items[] = "(10,4,'page_form_field_url','Page URL')";
        }
        if(!isset($check[4]["page_form_field_parent"])){
            $items[] = "(10,4,'page_form_field_parent','Parent page')";
        }
        if(!isset($check[4]["page_form_field_template"])){
            $items[] = "(10,4,'page_form_field_template','Page template')";
        }
        if(!isset($check[4]["page_form_field_position"])){
            $items[] = "(10,4,'page_form_field_position','')";
        }
        if(!isset($check[4]["admin_page_name"])){
            $items[] = "(10,4,'admin_page_name','Page name')";
        }
        if(!isset($check[4]["admin_page_url"])){
            $items[] = "(10,4,'admin_page_url','Page url')";
        }
        if(!isset($check[4]["admin_page_parent"])){
            $items[] = "(10,4,'admin_page_parent','Parent page')";
        }
        if(!isset($check[4]["admin_page_template"])){
            $items[] = "(10,4,'admin_page_template','Page template')";
        }
        if(!isset($check[4]["admin_page_position"])){
            $items[] = "(10,4,'admin_page_position','Page number')";
        }
        if(!isset($check[4]["delete_page"])){
            $items[] = "(10,4,'delete_page','To delete page, press here')";
        }
        if(!isset($check[4]["home_page_delete"])){
            $items[] = "(10,4,'home_page_delete','You can\'t delete home page.')";
        }
        if(!isset($check[4]["unknown_error"])){
            $items[] = "(10,4,'unknown_error','Something goes wrong. We are sorry...')";
        }
        if(!isset($check[4]["page_contain_children"])){
            $items[] = "(10,4,'page_contain_children','You can\'t delete page width child pages.')";
        }
        if(!isset($check[4]["page_created"])){
            $items[] = "(10,4,'page_created','Page created.')";
        }
        if(!isset($check[4]["page_modified"])){
            $items[] = "(10,4,'page_modified','Page data modified.')";
        }
        if(!isset($check[4]["page_url_empty"])){
            $items[] = "(10,4,'page_url_empty','Page url can\'t be empty.')";
        }
        if(!isset($check[4]["page_url_not_unique"])){
            $items[] = "(10,4,'page_url_not_unique','Page with such url is already exist. Change url, or parent page.')";
        }
        if(!isset($check[4]["show_content"])){
            $items[] = "(10,4,'show_content','Show content')";
        }
        if(!isset($check[4]["hide_content"])){
            $items[] = "(10,4,'hide_content','Hide content')";
        }
        if(!isset($check[4]["field_type"])){
            $items[] = "(10,4,'field_type','Type')";
        }
        if(!isset($check[4]["field_module"])){
            $items[] = "(10,4,'field_module','Module')";
        }
        if(!isset($check[4]["field_method"])){
            $items[] = "(10,4,'field_method','Method')";
        }
        if(!isset($check[4]["put_comment"])){
            $items[] = "(10,4,'put_comment','Put a comment here')";
        }
        if(!isset($check[4]["include_textarea"])){
            $items[] = "(10,4,'include_textarea','Insert any text content here, and than push red cross on left top angle.')";
        }
        if(!isset($check[4]["template_field_name"])){
            $items[] = "(10,4,'template_field_name','Template name')";
        }
        if(!isset($check[4]["template_field_parent"])){
            $items[] = "(10,4,'template_field_parent','Parent template (not implemented yet)')";
        }
        if(!isset($check[4]["template_field_template"])){
            $items[] = "(10,4,'template_field_template','Template file')";
        }
        if(!isset($check[4]["new_access_group"])){
            $items[] = "(10,4,'new_access_group','New User Group')";
        }
        if(!isset($check[4]["new_access_action"])){
            $items[] = "(10,4,'new_access_action','New access permission')";
        }
        if(!isset($check[4]["new_access_group_prompt"])){
            $items[] = "(10,4,'new_access_group_prompt','Enter new user group name.
 Use \"_\" char as word separator')";
        }
        if(!isset($check[4]["new_access_action_prompt"])){
            $items[] = "(10,4,'new_access_action_prompt','Enter new access action name.
 Use \"_\" char as word separator')";
        }
        if(!isset($check[4]["delete_include_confirm"])){
            $items[] = "(10,4,'delete_include_confirm','Are you realy want to delete this include?
 Some site content can disappear.')";
        }
        if(!isset($check[4]["confirm_yes"])){
            $items[] = "(10,4,'confirm_yes','I know what I do')";
        }
        if(!isset($check[4]["confirm_no"])){
            $items[] = "(10,4,'confirm_no','No')";
        }
        if(!isset($check[4]["delete_language_confirm"])){
            $items[] = "(10,4,'delete_language_confirm','Realy delete this item?
 All terms with this alias will be deleted too, and operation can\'t be undone.')";
        }
        DB::query($insertQuery.implode(",", $items));
        $items = array();
        if(!isset($check[4]["delete_access_group"])){
            $items[] = "(10,4,'delete_access_group','Realy delete this user group?')";
        }
        if(!isset($check[4]["delete_config_option"])){
            $items[] = "(10,4,'delete_config_option','Realy delete this option?
All options required for stable system work.')";
        }
        if(!isset($check[4]["delete_template_block"])){
            $items[] = "(10,4,'delete_template_block','Realy delete this block?
 Some site content can suddenly disappear.')";
        }
        if(!isset($check[4]["new_template_created"])){
            $items[] = "(10,4,'new_template_created','New template was created')";
        }
        if(!isset($check[4]["template_updated"])){
            $items[] = "(10,4,'template_updated','Данные шаблона обновлены')";
        }
        if(!isset($check[4]["block_created"])){
            $items[] = "(10,4,'block_created','Block was created.')";
        }
        if(!isset($check[4]["block_name_not_unique"])){
            $items[] = "(10,4,'block_name_not_unique','This template already contain block with this name.')";
        }
        if(!isset($check[4]["template_id_empty"])){
            $items[] = "(10,4,'template_id_empty','Template id must be specified')";
        }
        if(!isset($check[4]["block_name_empty"])){
            $items[] = "(10,4,'block_name_empty','Block name must be specified')";
        }
        if(!isset($check[4]["new_page_template"])){
            $items[] = "(10,4,'new_page_template','New template created. It can be used with page edit form.')";
        }
        if(!isset($check[4]["back_to_dictionaries"])){
            $items[] = "(10,4,'back_to_dictionaries','To Dictionaries overview')";
        }
        if(!isset($check[4]["back_to_current_dict"])){
            $items[] = "(10,4,'back_to_current_dict','Back to current dictionary')";
        }
        if(!isset($check[4]["word_name_filter"])){
            $items[] = "(10,4,'word_name_filter','Filter by name')";
        }
        if(!isset($check[4]["word_value_filter"])){
            $items[] = "(10,4,'word_value_filter','Filter by value')";
        }
        DB::query($insertQuery.implode(",", $items));
        $check = DB::getAssoc("SELECT module, name FROM word WHERE language = 9", "module", "name");if(!isset($check[4]["panel_new_page"])){
        $items[] = "(9,4,'panel_new_page','Создать страницу')";
    }
        if(!isset($check[4]["word_reset_term"])){
            $items[] = "(9,4,'word_reset_term','Новый термин')";
        }
        if(!isset($check[4]["panel_edit_page"])){
            $items[] = "(9,4,'panel_edit_page','Эта страница')";
        }
        if(!isset($check[4]["panel_show_pages"])){
            $items[] = "(9,4,'panel_show_pages','Дерево страниц')";
        }
        if(!isset($check[4]["panel_page_content"])){
            $items[] = "(9,4,'panel_page_content','Блоки страницы')";
        }
        if(!isset($check[4]["panel_templates"])){
            $items[] = "(9,4,'panel_templates','Шаблоны')";
        }
        if(!isset($check[4]["panel_edit_access"])){
            $items[] = "(9,4,'panel_edit_access','Доступ')";
        }
        if(!isset($check[4]["panel_word"])){
            $items[] = "(9,4,'panel_word','Переводы')";
        }
        if(!isset($check[4]["panel_configuration"])){
            $items[] = "(9,4,'panel_configuration','Настройки')";
        }
        if(!isset($check[4]["panel_position"])){
            $items[] = "(9,4,'panel_position','< Позиция >')";
        }
        if(!isset($check[4]["panel_languages"])){
            $items[] = "(9,4,'panel_languages','Языки')";
        }
        if(!isset($check[4]["panel_modules"])){
            $items[] = "(9,4,'panel_modules','Словари')";
        }
        if(!isset($check[4]["panel_create_template"])){
            $items[] = "(9,4,'panel_create_template','Создать шаблон')";
        }
        if(!isset($check[4]["panel_edit_template"])){
            $items[] = "(9,4,'panel_edit_template','Править шаблон')";
        }
        if(!isset($check[4]["panel_delete_template"])){
            $items[] = "(9,4,'panel_delete_template','Удалить шаблон')";
        }
        if(!isset($check[4]["page_form_field_name"])){
            $items[] = "(9,4,'page_form_field_name','Название страницы')";
        }
        if(!isset($check[4]["page_form_field_url"])){
            $items[] = "(9,4,'page_form_field_url','URL страницы')";
        }
        if(!isset($check[4]["page_form_field_parent"])){
            $items[] = "(9,4,'page_form_field_parent','Родительская страница')";
        }
        if(!isset($check[4]["page_form_field_template"])){
            $items[] = "(9,4,'page_form_field_template','Шаблон страницы')";
        }
        if(!isset($check[4]["page_form_field_position"])){
            $items[] = "(9,4,'page_form_field_position','Позиция страницы')";
        }
        if(!isset($check[4]["admin_page_name"])){
            $items[] = "(9,4,'admin_page_name','Название страницы')";
        }
        if(!isset($check[4]["admin_page_url"])){
            $items[] = "(9,4,'admin_page_url','URL страницы')";
        }
        if(!isset($check[4]["admin_page_parent"])){
            $items[] = "(9,4,'admin_page_parent','Страница - родитель')";
        }
        if(!isset($check[4]["admin_page_template"])){
            $items[] = "(9,4,'admin_page_template','Шаблон страницы')";
        }
        if(!isset($check[4]["admin_page_position"])){
            $items[] = "(9,4,'admin_page_position','Номер страницы')";
        }
        if(!isset($check[4]["delete_page"])){
            $items[] = "(9,4,'delete_page','Удалить эту страницу')";
        }
        if(!isset($check[4]["home_page_delete"])){
            $items[] = "(9,4,'home_page_delete','Нельзя удалить главную страницу.')";
        }
        if(!isset($check[4]["unknown_error"])){
            $items[] = "(9,4,'unknown_error','Что-то не сработало. Нам очень жаль...')";
        }
        if(!isset($check[4]["page_contain_children"])){
            $items[] = "(9,4,'page_contain_children','Нельзя удалить страницу, пока существуют дочерние страницы.')";
        }
        if(!isset($check[4]["page_created"])){
            $items[] = "(9,4,'page_created','Страница создана.')";
        }
        if(!isset($check[4]["page_modified"])){
            $items[] = "(9,4,'page_modified','Страница изменена.')";
        }
        if(!isset($check[4]["page_url_empty"])){
            $items[] = "(9,4,'page_url_empty','URL страницы не может быть пустым.')";
        }
        if(!isset($check[4]["page_url_not_unique"])){
            $items[] = "(9,4,'page_url_not_unique','Страница с таким URL\\\'ом уже существует. Либо измените URL, либо родительскую страницу.')";
        }
        if(!isset($check[4]["show_content"])){
            $items[] = "(9,4,'show_content','Показать содержимое')";
        }
        if(!isset($check[4]["hide_content"])){
            $items[] = "(9,4,'hide_content','Спрятать содержимое')";
        }
        if(!isset($check[4]["field_type"])){
            $items[] = "(9,4,'field_type','Тип')";
        }
        if(!isset($check[4]["field_module"])){
            $items[] = "(9,4,'field_module','Модуль')";
        }
        if(!isset($check[4]["field_method"])){
            $items[] = "(9,4,'field_method','Метод')";
        }
        if(!isset($check[4]["put_comment"])){
            $items[] = "(9,4,'put_comment','Оставь здесь комментарий')";
        }
        if(!isset($check[4]["include_textarea"])){
            $items[] = "(9,4,'include_textarea','Введите сюда любое содержимое, затем нажмите красный крест справа сверху.')";
        }
        if(!isset($check[4]["template_field_name"])){
            $items[] = "(9,4,'template_field_name','Название шаблона')";
        }
        if(!isset($check[4]["template_field_parent"])){
            $items[] = "(9,4,'template_field_parent','Родительский шаблон (пока что не работает)')";
        }
        if(!isset($check[4]["template_field_template"])){
            $items[] = "(9,4,'template_field_template','Файл шаблона')";
        }
        if(!isset($check[4]["new_access_group"])){
            $items[] = "(9,4,'new_access_group','Новая группа пользователей')";
        }
        if(!isset($check[4]["new_access_action"])){
            $items[] = "(9,4,'new_access_action','Новая проверка доступа')";
        }
        if(!isset($check[4]["new_access_group_prompt"])){
            $items[] = "(9,4,'new_access_group_prompt','Введите название новой группы пользователей.
Используйте \'_\' вместо пробелов.')";
        }
        if(!isset($check[4]["new_access_action_prompt"])){
            $items[] = "(9,4,'new_access_action_prompt','Введите название новой проверки доступа.
Используйте символ \'_\' вместо пробелов.')";
        }
        if(!isset($check[4]["delete_include_confirm"])){
            $items[] = "(9,4,'delete_include_confirm','Вы действительно хотите удалить это вложение?
Часть содержимого сайта будет удалена.')";
        }
        if(!isset($check[4]["confirm_yes"])){
            $items[] = "(9,4,'confirm_yes','Я знаю что делаю')";
        }
        if(!isset($check[4]["confirm_no"])){
            $items[] = "(9,4,'confirm_no','Нет')";
        }
        if(!isset($check[4]["delete_language_confirm"])){
            $items[] = "(9,4,'delete_language_confirm','Действительно удалить?
Все связанные переводы будут также удалены, и операцию нельзя будет отменить.')";
        }
        DB::query($insertQuery.implode(",", $items));
        $items = array();
        if(!isset($check[4]["delete_access_group"])){
            $items[] = "(9,4,'delete_access_group','Действительно удалить эту группу пользователей?')";
        }
        if(!isset($check[4]["delete_config_option"])){
            $items[] = "(9,4,'delete_config_option','Действительно удалить эту настройку?
Скорее всего она необходима для стабильной работы системы.')";
        }
        if(!isset($check[4]["delete_template_block"])){
            $items[] = "(9,4,'delete_template_block','Действительно удалить этот блок?
Все связанное с ним содержимое будет также удалено.')";
        }
        if(!isset($check[4]["new_template_created"])){
            $items[] = "(9,4,'new_template_created','Новый шаблон был создан.')";
        }
        if(!isset($check[4]["template_updated"])){
            $items[] = "(9,4,'template_updated','')";
        }
        if(!isset($check[4]["block_created"])){
            $items[] = "(9,4,'block_created','Создан новый блок')";
        }
        if(!isset($check[4]["block_name_not_unique"])){
            $items[] = "(9,4,'block_name_not_unique','Этот шаблон уже содержит блок с таким именем.')";
        }
        if(!isset($check[4]["template_id_empty"])){
            $items[] = "(9,4,'template_id_empty','Идентификатор шаблона не установлен')";
        }
        if(!isset($check[4]["block_name_empty"])){
            $items[] = "(9,4,'block_name_empty','Название блока не должно быть пустым')";
        }
        if(!isset($check[4]["new_page_template"])){
            $items[] = "(9,4,'new_page_template','Новый шаблон создан. Его можно подключить из формы редактирования страницы.')";
        }
        if(!isset($check[4]["back_to_dictionaries"])){
            $items[] = "(9,4,'back_to_dictionaries','К обзору словарей')";
        }
        if(!isset($check[4]["back_to_current_dict"])){
            $items[] = "(9,4,'back_to_current_dict','Назад, к текущему словарю')";
        }
        if(!isset($check[4]["word_name_filter"])){
            $items[] = "(9,4,'word_name_filter','Фильтр по имени')";
        }
        if(!isset($check[4]["word_value_filter"])){
            $items[] = "(9,4,'word_value_filter','Фильтр по значению')";
        }
        DB::query($insertQuery.implode(",", $items));
        $check = DB::getAssoc("SELECT module, name FROM word WHERE language = 10", "module", "name");if(!isset($check[5]["submit"])){
        $items[] = "(10,5,'submit','Submit')";
    }
        if(!isset($check[5]["include_type_html"])){
            $items[] = "(10,5,'include_type_html','HTML markup')";
        }
        if(!isset($check[5]["include_type_text"])){
            $items[] = "(10,5,'include_type_text','Text')";
        }
        if(!isset($check[5]["include_type_image"])){
            $items[] = "(10,5,'include_type_image','Image')";
        }
        if(!isset($check[5]["include_type_executable"])){
            $items[] = "(10,5,'include_type_executable','Executable code')";
        }
        DB::query($insertQuery.implode(",", $items));
        $check = DB::getAssoc("SELECT module, name FROM word WHERE language = 9", "module", "name");if(!isset($check[5]["submit"])){
        $items[] = "(9,5,'submit','Отправить')";
    }
        if(!isset($check[5]["include_type_html"])){
            $items[] = "(9,5,'include_type_html','HTML разметка')";
        }
        if(!isset($check[5]["include_type_text"])){
            $items[] = "(9,5,'include_type_text','Текст')";
        }
        if(!isset($check[5]["include_type_image"])){
            $items[] = "(9,5,'include_type_image','Изображение')";
        }
        if(!isset($check[5]["include_type_executable"])){
            $items[] = "(9,5,'include_type_executable','Исполняемый код')";
        }
        DB::query($insertQuery.implode(",", $items));
        $check = DB::getAssoc("SELECT module, name FROM word WHERE language = 10", "module", "name");if(!isset($check[3]["logout"])){
        $items[] = "(10,3,'logout','Logout')";
    }
        if(!isset($check[3]["field_email"])){
            $items[] = "(10,3,'field_email','Email')";
        }
        if(!isset($check[3]["field_password"])){
            $items[] = "(10,3,'field_password','Password')";
        }
        DB::query($insertQuery.implode(",", $items));
        $check = DB::getAssoc("SELECT module, name FROM word WHERE language = 9", "module", "name");if(!isset($check[3]["logout"])){
        $items[] = "(9,3,'logout','Выход')";
    }
        if(!isset($check[3]["field_email"])){
            $items[] = "(9,3,'field_email','Емейл')";
        }
        if(!isset($check[3]["field_password"])){
            $items[] = "(9,3,'field_password','Пароль')";
        }
        DB::query($insertQuery.implode(",", $items));
        $check = DB::getAssoc("SELECT module, name FROM word WHERE language = 10", "module", "name");if(!isset($check[1]["module_header_module"])){
        $items[] = "(10,1,'module_header_module','Module name')";
    }
        if(!isset($check[1]["language_header_locale"])){
            $items[] = "(10,1,'language_header_locale','Language')";
        }
        if(!isset($check[1]["new_term_created"])){
            $items[] = "(10,1,'new_term_created','New term saved.')";
        }
        if(!isset($check[1]["term_header_language"])){
            $items[] = "(10,1,'term_header_language','Language')";
        }
        if(!isset($check[1]["term_header_module"])){
            $items[] = "(10,1,'term_header_module','Module')";
        }
        if(!isset($check[1]["term_header_name"])){
            $items[] = "(10,1,'term_header_name','Term key')";
        }
        if(!isset($check[1]["term_header_value"])){
            $items[] = "(10,1,'term_header_value','Term value')";
        }
        if(!isset($check[1]["language_header_is_default"])){
            $items[] = "(10,1,'language_header_is_default','Default language')";
        }
        if(!isset($check[1]["terms_updated"])){
            $items[] = "(10,1,'terms_updated','Tern updated')";
        }
        DB::query($insertQuery.implode(",", $items));
        $check = DB::getAssoc("SELECT module, name FROM word WHERE language = 9", "module", "name");if(!isset($check[1]["module_header_module"])){
        $items[] = "(9,1,'module_header_module','Название модуля')";
    }
        if(!isset($check[1]["language_header_locale"])){
            $items[] = "(9,1,'language_header_locale','Язык')";
        }
        if(!isset($check[1]["new_term_created"])){
            $items[] = "(9,1,'new_term_created','Новый термин успешно сохранен.')";
        }
        if(!isset($check[1]["term_header_language"])){
            $items[] = "(9,1,'term_header_language','Язык')";
        }
        if(!isset($check[1]["term_header_module"])){
            $items[] = "(9,1,'term_header_module','Модуль')";
        }
        if(!isset($check[1]["term_header_name"])){
            $items[] = "(9,1,'term_header_name','Ключ термина')";
        }
        if(!isset($check[1]["term_header_value"])){
            $items[] = "(9,1,'term_header_value','Значение термина')";
        }
        if(!isset($check[1]["language_header_is_default"])){
            $items[] = "(9,1,'language_header_is_default','Язык по умолчанию')";
        }
        if(!isset($check[1]["terms_updated"])){
            $items[] = "(9,1,'terms_updated','Термин обновлен')";
        }
        DB::query($insertQuery.implode(",", $items));
    }
}

