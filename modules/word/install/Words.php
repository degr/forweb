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
        $words = array(
            'panel_new_page'=>array('language'=>'2', 'module'=>'4','name'=>'panel_new_page', 'value'=>'New page'),
            'word_reset_term'=>array('language'=>'2', 'module'=>'4','name'=>'word_reset_term', 'value'=>''),
            'panel_edit_page'=>array('language'=>'2', 'module'=>'4','name'=>'panel_edit_page', 'value'=>'This page'),
            'panel_show_pages'=>array('language'=>'2', 'module'=>'4','name'=>'panel_show_pages', 'value'=>'Pages tree'),
            'panel_page_content'=>array('language'=>'2', 'module'=>'4','name'=>'panel_page_content', 'value'=>'Page content'),
            'panel_templates'=>array('language'=>'2', 'module'=>'4','name'=>'panel_templates', 'value'=>'Templates'),
            'panel_edit_access'=>array('language'=>'2', 'module'=>'4','name'=>'panel_edit_access', 'value'=>'Access'),
            'panel_word'=>array('language'=>'2', 'module'=>'4','name'=>'panel_word', 'value'=>'Translates'),
            'panel_configuration'=>array('language'=>'2', 'module'=>'4','name'=>'panel_configuration', 'value'=>'Configuration'),
            'panel_position'=>array('language'=>'2', 'module'=>'4','name'=>'panel_position', 'value'=>'< Position >'),
            'panel_languages'=>array('language'=>'2', 'module'=>'4','name'=>'panel_languages', 'value'=>'Languages'),
            'panel_modules'=>array('language'=>'2', 'module'=>'4','name'=>'panel_modules', 'value'=>'Dictionaries'),
            'panel_create_template'=>array('language'=>'2', 'module'=>'4','name'=>'panel_create_template', 'value'=>'Create template'),
            'panel_edit_template'=>array('language'=>'2', 'module'=>'4','name'=>'panel_edit_template', 'value'=>'Edit template'),
            'panel_delete_template'=>array('language'=>'2', 'module'=>'4','name'=>'panel_delete_template', 'value'=>'Delete template'),
            'page_form_field_name'=>array('language'=>'2', 'module'=>'4','name'=>'page_form_field_name', 'value'=>'Page name'),
            'page_form_field_url'=>array('language'=>'2', 'module'=>'4','name'=>'page_form_field_url', 'value'=>'Page URL'),
            'page_form_field_parent'=>array('language'=>'2', 'module'=>'4','name'=>'page_form_field_parent', 'value'=>'Parent page'),
            'page_form_field_template'=>array('language'=>'2', 'module'=>'4','name'=>'page_form_field_template', 'value'=>'Page template'),
            'page_form_field_position'=>array('language'=>'2', 'module'=>'4','name'=>'page_form_field_position', 'value'=>''),
            'admin_page_name'=>array('language'=>'2', 'module'=>'4','name'=>'admin_page_name', 'value'=>'Page name'),
            'admin_page_url'=>array('language'=>'2', 'module'=>'4','name'=>'admin_page_url', 'value'=>'Page url'),
            'admin_page_parent'=>array('language'=>'2', 'module'=>'4','name'=>'admin_page_parent', 'value'=>'Parent page'),
            'admin_page_template'=>array('language'=>'2', 'module'=>'4','name'=>'admin_page_template', 'value'=>'Page template'),
            'admin_page_position'=>array('language'=>'2', 'module'=>'4','name'=>'admin_page_position', 'value'=>'Page number'),
            'delete_page'=>array('language'=>'2', 'module'=>'4','name'=>'delete_page', 'value'=>'To delete page, press here'),
            'home_page_delete'=>array('language'=>'2', 'module'=>'4','name'=>'home_page_delete', 'value'=>'You can\'t delete home page.'),
            'unknown_error'=>array('language'=>'2', 'module'=>'4','name'=>'unknown_error', 'value'=>'Something goes wrong. We are sorry...'),
            'page_contain_children'=>array('language'=>'2', 'module'=>'4','name'=>'page_contain_children', 'value'=>'You can\'t delete page width child pages.'),
            'page_created'=>array('language'=>'2', 'module'=>'4','name'=>'page_created', 'value'=>'Page created.'),
            'page_modified'=>array('language'=>'2', 'module'=>'4','name'=>'page_modified', 'value'=>'Page data modified.'),
            'page_url_empty'=>array('language'=>'2', 'module'=>'4','name'=>'page_url_empty', 'value'=>'Page url can\'t be empty.'),
            'page_url_not_unique'=>array('language'=>'2', 'module'=>'4','name'=>'page_url_not_unique', 'value'=>'Page with such url is already exist. Change url, or parent page.'),
            'show_content'=>array('language'=>'2', 'module'=>'4','name'=>'show_content', 'value'=>'Show content'),
            'hide_content'=>array('language'=>'2', 'module'=>'4','name'=>'hide_content', 'value'=>'Hide content'),
            'field_type'=>array('language'=>'2', 'module'=>'4','name'=>'field_type', 'value'=>'Type'),
            'field_module'=>array('language'=>'2', 'module'=>'4','name'=>'field_module', 'value'=>'Module'),
            'field_method'=>array('language'=>'2', 'module'=>'4','name'=>'field_method', 'value'=>'Method'),
            'put_comment'=>array('language'=>'2', 'module'=>'4','name'=>'put_comment', 'value'=>'Put a comment here'),
            'include_textarea'=>array('language'=>'2', 'module'=>'4','name'=>'include_textarea', 'value'=>'Insert any text content here, and than push red cross on left top angle.'),
            'template_field_name'=>array('language'=>'2', 'module'=>'4','name'=>'template_field_name', 'value'=>'Template name'),
            'template_field_parent'=>array('language'=>'2', 'module'=>'4','name'=>'template_field_parent', 'value'=>'Parent template (not implemented yet)'),
            'template_field_template'=>array('language'=>'2', 'module'=>'4','name'=>'template_field_template', 'value'=>'Template file'),
            'new_access_group'=>array('language'=>'2', 'module'=>'4','name'=>'new_access_group', 'value'=>'New User Group'),
            'new_access_action'=>array('language'=>'2', 'module'=>'4','name'=>'new_access_action', 'value'=>'New access permission'),
            'new_access_group_prompt'=>array('language'=>'2', 'module'=>'4','name'=>'new_access_group_prompt', 'value'=>'Enter new user group name.
 Use \"_\" char as word separator'),
            'new_access_action_prompt'=>array('language'=>'2', 'module'=>'4','name'=>'new_access_action_prompt', 'value'=>'Enter new access action name.
 Use \"_\" char as word separator'),
            'delete_include_confirm'=>array('language'=>'2', 'module'=>'4','name'=>'delete_include_confirm', 'value'=>'Are you realy want to delete this include?
 Some site content can disappear.'),
            'confirm_yes'=>array('language'=>'2', 'module'=>'4','name'=>'confirm_yes', 'value'=>'I know what I do'),
            'confirm_no'=>array('language'=>'2', 'module'=>'4','name'=>'confirm_no', 'value'=>'No'),
            'delete_language_confirm'=>array('language'=>'2', 'module'=>'4','name'=>'delete_language_confirm', 'value'=>'Realy delete this item?
 All terms with this alias will be deleted too, and operation can\'t be undone.'),
            'delete_access_group'=>array('language'=>'2', 'module'=>'4','name'=>'delete_access_group', 'value'=>'Realy delete this user group?'),
            'delete_config_option'=>array('language'=>'2', 'module'=>'4','name'=>'delete_config_option', 'value'=>'Realy delete this option?
All options required for stable system work.'),
            'delete_template_block'=>array('language'=>'2', 'module'=>'4','name'=>'delete_template_block', 'value'=>'Realy delete this block?
 Some site content can suddenly disappear.'),
            'new_template_created'=>array('language'=>'2', 'module'=>'4','name'=>'new_template_created', 'value'=>'New template was created'),
            'template_updated'=>array('language'=>'2', 'module'=>'4','name'=>'template_updated', 'value'=>'Данные шаблона обновлены'),
            'block_created'=>array('language'=>'2', 'module'=>'4','name'=>'block_created', 'value'=>'Block was created.'),
            'block_name_not_unique'=>array('language'=>'2', 'module'=>'4','name'=>'block_name_not_unique', 'value'=>'This template already contain block with this name.'),
            'template_id_empty'=>array('language'=>'2', 'module'=>'4','name'=>'template_id_empty', 'value'=>'Template id must be specified'),
            'block_name_empty'=>array('language'=>'2', 'module'=>'4','name'=>'block_name_empty', 'value'=>'Block name must be specified'),
            'new_page_template'=>array('language'=>'2', 'module'=>'4','name'=>'new_page_template', 'value'=>'New template created. It can be used with page edit form.'),
            'back_to_dictionaries'=>array('language'=>'2', 'module'=>'4','name'=>'back_to_dictionaries', 'value'=>'To Dictionaries overview'),
            'back_to_current_dict'=>array('language'=>'2', 'module'=>'4','name'=>'back_to_current_dict', 'value'=>'Back to current dictionary'),
            'word_name_filter'=>array('language'=>'2', 'module'=>'4','name'=>'word_name_filter', 'value'=>'Filter by name'),
            'word_value_filter'=>array('language'=>'2', 'module'=>'4','name'=>'word_value_filter', 'value'=>'Filter by value'),
        );
        $check = DB::getColumn('SELECT name FROM word WHERE language = 2 AND module = "4"', "module", "name");
        foreach ($check as $name) {
            unset($words[$name]);
        }
        foreach($words as $word) {
            DB::query($insertQuery."(".$word['language'], $word['module'], $word['name'], $word['value']);
        }
        $words = array(
            'panel_new_page'=>array('language'=>'1', 'module'=>'4','name'=>'panel_new_page', 'value'=>'Создать страницу'),
            'word_reset_term'=>array('language'=>'1', 'module'=>'4','name'=>'word_reset_term', 'value'=>'Новый термин'),
            'panel_edit_page'=>array('language'=>'1', 'module'=>'4','name'=>'panel_edit_page', 'value'=>'Эта страница'),
            'panel_show_pages'=>array('language'=>'1', 'module'=>'4','name'=>'panel_show_pages', 'value'=>'Дерево страниц'),
            'panel_page_content'=>array('language'=>'1', 'module'=>'4','name'=>'panel_page_content', 'value'=>'Блоки страницы'),
            'panel_templates'=>array('language'=>'1', 'module'=>'4','name'=>'panel_templates', 'value'=>'Шаблоны'),
            'panel_edit_access'=>array('language'=>'1', 'module'=>'4','name'=>'panel_edit_access', 'value'=>'Доступ'),
            'panel_word'=>array('language'=>'1', 'module'=>'4','name'=>'panel_word', 'value'=>'Переводы'),
            'panel_configuration'=>array('language'=>'1', 'module'=>'4','name'=>'panel_configuration', 'value'=>'Настройки'),
            'panel_position'=>array('language'=>'1', 'module'=>'4','name'=>'panel_position', 'value'=>'< Позиция >'),
            'panel_languages'=>array('language'=>'1', 'module'=>'4','name'=>'panel_languages', 'value'=>'Языки'),
            'panel_modules'=>array('language'=>'1', 'module'=>'4','name'=>'panel_modules', 'value'=>'Словари'),
            'panel_create_template'=>array('language'=>'1', 'module'=>'4','name'=>'panel_create_template', 'value'=>'Создать шаблон'),
            'panel_edit_template'=>array('language'=>'1', 'module'=>'4','name'=>'panel_edit_template', 'value'=>'Править шаблон'),
            'panel_delete_template'=>array('language'=>'1', 'module'=>'4','name'=>'panel_delete_template', 'value'=>'Удалить шаблон'),
            'page_form_field_name'=>array('language'=>'1', 'module'=>'4','name'=>'page_form_field_name', 'value'=>'Название страницы'),
            'page_form_field_url'=>array('language'=>'1', 'module'=>'4','name'=>'page_form_field_url', 'value'=>'URL страницы'),
            'page_form_field_parent'=>array('language'=>'1', 'module'=>'4','name'=>'page_form_field_parent', 'value'=>'Родительская страница'),
            'page_form_field_template'=>array('language'=>'1', 'module'=>'4','name'=>'page_form_field_template', 'value'=>'Шаблон страницы'),
            'page_form_field_position'=>array('language'=>'1', 'module'=>'4','name'=>'page_form_field_position', 'value'=>'Позиция страницы'),
            'admin_page_name'=>array('language'=>'1', 'module'=>'4','name'=>'admin_page_name', 'value'=>'Название страницы'),
            'admin_page_url'=>array('language'=>'1', 'module'=>'4','name'=>'admin_page_url', 'value'=>'URL страницы'),
            'admin_page_parent'=>array('language'=>'1', 'module'=>'4','name'=>'admin_page_parent', 'value'=>'Страница - родитель'),
            'admin_page_template'=>array('language'=>'1', 'module'=>'4','name'=>'admin_page_template', 'value'=>'Шаблон страницы'),
            'admin_page_position'=>array('language'=>'1', 'module'=>'4','name'=>'admin_page_position', 'value'=>'Номер страницы'),
            'delete_page'=>array('language'=>'1', 'module'=>'4','name'=>'delete_page', 'value'=>'Удалить эту страницу'),
            'home_page_delete'=>array('language'=>'1', 'module'=>'4','name'=>'home_page_delete', 'value'=>'Нельзя удалить главную страницу.'),
            'unknown_error'=>array('language'=>'1', 'module'=>'4','name'=>'unknown_error', 'value'=>'Что-то не сработало. Нам очень жаль...'),
            'page_contain_children'=>array('language'=>'1', 'module'=>'4','name'=>'page_contain_children', 'value'=>'Нельзя удалить страницу, пока существуют дочерние страницы.'),
            'page_created'=>array('language'=>'1', 'module'=>'4','name'=>'page_created', 'value'=>'Страница создана.'),
            'page_modified'=>array('language'=>'1', 'module'=>'4','name'=>'page_modified', 'value'=>'Страница изменена.'),
            'page_url_empty'=>array('language'=>'1', 'module'=>'4','name'=>'page_url_empty', 'value'=>'URL страницы не может быть пустым.'),
            'page_url_not_unique'=>array('language'=>'1', 'module'=>'4','name'=>'page_url_not_unique', 'value'=>'Страница с таким URL\\\'ом уже существует. Либо измените URL, либо родительскую страницу.'),
            'show_content'=>array('language'=>'1', 'module'=>'4','name'=>'show_content', 'value'=>'Показать содержимое'),
            'hide_content'=>array('language'=>'1', 'module'=>'4','name'=>'hide_content', 'value'=>'Спрятать содержимое'),
            'field_type'=>array('language'=>'1', 'module'=>'4','name'=>'field_type', 'value'=>'Тип'),
            'field_module'=>array('language'=>'1', 'module'=>'4','name'=>'field_module', 'value'=>'Модуль'),
            'field_method'=>array('language'=>'1', 'module'=>'4','name'=>'field_method', 'value'=>'Метод'),
            'put_comment'=>array('language'=>'1', 'module'=>'4','name'=>'put_comment', 'value'=>'Оставь здесь комментарий'),
            'include_textarea'=>array('language'=>'1', 'module'=>'4','name'=>'include_textarea', 'value'=>'Введите сюда любое содержимое, затем нажмите красный крест справа сверху.'),
            'template_field_name'=>array('language'=>'1', 'module'=>'4','name'=>'template_field_name', 'value'=>'Название шаблона'),
            'template_field_parent'=>array('language'=>'1', 'module'=>'4','name'=>'template_field_parent', 'value'=>'Родительский шаблон (пока что не работает)'),
            'template_field_template'=>array('language'=>'1', 'module'=>'4','name'=>'template_field_template', 'value'=>'Файл шаблона'),
            'new_access_group'=>array('language'=>'1', 'module'=>'4','name'=>'new_access_group', 'value'=>'Новая группа пользователей'),
            'new_access_action'=>array('language'=>'1', 'module'=>'4','name'=>'new_access_action', 'value'=>'Новая проверка доступа'),
            'new_access_group_prompt'=>array('language'=>'1', 'module'=>'4','name'=>'new_access_group_prompt', 'value'=>'Введите название новой группы пользователей.
Используйте \'_\' вместо пробелов.'),
            'new_access_action_prompt'=>array('language'=>'1', 'module'=>'4','name'=>'new_access_action_prompt', 'value'=>'Введите название новой проверки доступа.
Используйте символ \'_\' вместо пробелов.'),
            'delete_include_confirm'=>array('language'=>'1', 'module'=>'4','name'=>'delete_include_confirm', 'value'=>'Вы действительно хотите удалить это вложение?
Часть содержимого сайта будет удалена.'),
            'confirm_yes'=>array('language'=>'1', 'module'=>'4','name'=>'confirm_yes', 'value'=>'Я знаю что делаю'),
            'confirm_no'=>array('language'=>'1', 'module'=>'4','name'=>'confirm_no', 'value'=>'Нет'),
            'delete_language_confirm'=>array('language'=>'1', 'module'=>'4','name'=>'delete_language_confirm', 'value'=>'Действительно удалить?
Все связанные переводы будут также удалены, и операцию нельзя будет отменить.'),
            'delete_access_group'=>array('language'=>'1', 'module'=>'4','name'=>'delete_access_group', 'value'=>'Действительно удалить эту группу пользователей?'),
            'delete_config_option'=>array('language'=>'1', 'module'=>'4','name'=>'delete_config_option', 'value'=>'Действительно удалить эту настройку?
Скорее всего она необходима для стабильной работы системы.'),
            'delete_template_block'=>array('language'=>'1', 'module'=>'4','name'=>'delete_template_block', 'value'=>'Действительно удалить этот блок?
Все связанное с ним содержимое будет также удалено.'),
            'new_template_created'=>array('language'=>'1', 'module'=>'4','name'=>'new_template_created', 'value'=>'Новый шаблон был создан.'),
            'template_updated'=>array('language'=>'1', 'module'=>'4','name'=>'template_updated', 'value'=>''),
            'block_created'=>array('language'=>'1', 'module'=>'4','name'=>'block_created', 'value'=>'Создан новый блок'),
            'block_name_not_unique'=>array('language'=>'1', 'module'=>'4','name'=>'block_name_not_unique', 'value'=>'Этот шаблон уже содержит блок с таким именем.'),
            'template_id_empty'=>array('language'=>'1', 'module'=>'4','name'=>'template_id_empty', 'value'=>'Идентификатор шаблона не установлен'),
            'block_name_empty'=>array('language'=>'1', 'module'=>'4','name'=>'block_name_empty', 'value'=>'Название блока не должно быть пустым'),
            'new_page_template'=>array('language'=>'1', 'module'=>'4','name'=>'new_page_template', 'value'=>'Новый шаблон создан. Его можно подключить из формы редактирования страницы.'),
            'back_to_dictionaries'=>array('language'=>'1', 'module'=>'4','name'=>'back_to_dictionaries', 'value'=>'К обзору словарей'),
            'back_to_current_dict'=>array('language'=>'1', 'module'=>'4','name'=>'back_to_current_dict', 'value'=>'Назад, к текущему словарю'),
            'word_name_filter'=>array('language'=>'1', 'module'=>'4','name'=>'word_name_filter', 'value'=>'Фильтр по имени'),
            'word_value_filter'=>array('language'=>'1', 'module'=>'4','name'=>'word_value_filter', 'value'=>'Фильтр по значению'),
        );
        $check = DB::getColumn('SELECT name FROM word WHERE language = 1 AND module = "4"', "module", "name");
        foreach ($check as $name) {
            unset($words[$name]);
        }
        foreach($words as $word) {
            DB::query($insertQuery."(".$word['language'], $word['module'], $word['name'], $word['value']);
        }
        $words = array(
            'submit'=>array('language'=>'2', 'module'=>'5','name'=>'submit', 'value'=>'Submit'),
            'include_type_html'=>array('language'=>'2', 'module'=>'5','name'=>'include_type_html', 'value'=>'HTML markup'),
            'include_type_text'=>array('language'=>'2', 'module'=>'5','name'=>'include_type_text', 'value'=>'Text'),
            'include_type_image'=>array('language'=>'2', 'module'=>'5','name'=>'include_type_image', 'value'=>'Image'),
            'include_type_executable'=>array('language'=>'2', 'module'=>'5','name'=>'include_type_executable', 'value'=>'Executable code'),
        );
        $check = DB::getColumn('SELECT name FROM word WHERE language = 2 AND module = "5"', "module", "name");
        foreach ($check as $name) {
            unset($words[$name]);
        }
        foreach($words as $word) {
            DB::query($insertQuery."(".$word['language'], $word['module'], $word['name'], $word['value']);
        }
        $words = array(
            'submit'=>array('language'=>'1', 'module'=>'5','name'=>'submit', 'value'=>'Отправить'),
            'include_type_html'=>array('language'=>'1', 'module'=>'5','name'=>'include_type_html', 'value'=>'HTML разметка'),
            'include_type_text'=>array('language'=>'1', 'module'=>'5','name'=>'include_type_text', 'value'=>'Текст'),
            'include_type_image'=>array('language'=>'1', 'module'=>'5','name'=>'include_type_image', 'value'=>'Изображение'),
            'include_type_executable'=>array('language'=>'1', 'module'=>'5','name'=>'include_type_executable', 'value'=>'Исполняемый код'),
        );
        $check = DB::getColumn('SELECT name FROM word WHERE language = 1 AND module = "5"', "module", "name");
        foreach ($check as $name) {
            unset($words[$name]);
        }
        foreach($words as $word) {
            DB::query($insertQuery."(".$word['language'], $word['module'], $word['name'], $word['value']);
        }
        $words = array(
            'logout'=>array('language'=>'2', 'module'=>'3','name'=>'logout', 'value'=>'Logout'),
            'field_email'=>array('language'=>'2', 'module'=>'3','name'=>'field_email', 'value'=>'Email'),
            'field_password'=>array('language'=>'2', 'module'=>'3','name'=>'field_password', 'value'=>'Password'),
        );
        $check = DB::getColumn('SELECT name FROM word WHERE language = 2 AND module = "3"', "module", "name");
        foreach ($check as $name) {
            unset($words[$name]);
        }
        foreach($words as $word) {
            DB::query($insertQuery."(".$word['language'], $word['module'], $word['name'], $word['value']);
        }
        $words = array(
            'logout'=>array('language'=>'1', 'module'=>'3','name'=>'logout', 'value'=>'Выход'),
            'field_email'=>array('language'=>'1', 'module'=>'3','name'=>'field_email', 'value'=>'Емейл'),
            'field_password'=>array('language'=>'1', 'module'=>'3','name'=>'field_password', 'value'=>'Пароль'),
        );
        $check = DB::getColumn('SELECT name FROM word WHERE language = 1 AND module = "3"', "module", "name");
        foreach ($check as $name) {
            unset($words[$name]);
        }
        foreach($words as $word) {
            DB::query($insertQuery."(".$word['language'], $word['module'], $word['name'], $word['value']);
        }
        $words = array(
            'module_header_module'=>array('language'=>'2', 'module'=>'1','name'=>'module_header_module', 'value'=>'Module name'),
            'language_header_locale'=>array('language'=>'2', 'module'=>'1','name'=>'language_header_locale', 'value'=>'Language'),
            'new_term_created'=>array('language'=>'2', 'module'=>'1','name'=>'new_term_created', 'value'=>'New term saved.'),
            'term_header_language'=>array('language'=>'2', 'module'=>'1','name'=>'term_header_language', 'value'=>'Language'),
            'term_header_module'=>array('language'=>'2', 'module'=>'1','name'=>'term_header_module', 'value'=>'Module'),
            'term_header_name'=>array('language'=>'2', 'module'=>'1','name'=>'term_header_name', 'value'=>'Term key'),
            'term_header_value'=>array('language'=>'2', 'module'=>'1','name'=>'term_header_value', 'value'=>'Term value'),
            'language_header_is_default'=>array('language'=>'2', 'module'=>'1','name'=>'language_header_is_default', 'value'=>'Default language'),
            'terms_updated'=>array('language'=>'2', 'module'=>'1','name'=>'terms_updated', 'value'=>'Tern updated'),
        );
        $check = DB::getColumn('SELECT name FROM word WHERE language = 2 AND module = "1"', "module", "name");
        foreach ($check as $name) {
            unset($words[$name]);
        }
        foreach($words as $word) {
            DB::query($insertQuery."(".$word['language'], $word['module'], $word['name'], $word['value']);
        }
        $words = array(
            'module_header_module'=>array('language'=>'1', 'module'=>'1','name'=>'module_header_module', 'value'=>'Название модуля'),
            'language_header_locale'=>array('language'=>'1', 'module'=>'1','name'=>'language_header_locale', 'value'=>'Язык'),
            'new_term_created'=>array('language'=>'1', 'module'=>'1','name'=>'new_term_created', 'value'=>'Новый термин успешно сохранен.'),
            'term_header_language'=>array('language'=>'1', 'module'=>'1','name'=>'term_header_language', 'value'=>'Язык'),
            'term_header_module'=>array('language'=>'1', 'module'=>'1','name'=>'term_header_module', 'value'=>'Модуль'),
            'term_header_name'=>array('language'=>'1', 'module'=>'1','name'=>'term_header_name', 'value'=>'Ключ термина'),
            'term_header_value'=>array('language'=>'1', 'module'=>'1','name'=>'term_header_value', 'value'=>'Значение термина'),
            'language_header_is_default'=>array('language'=>'1', 'module'=>'1','name'=>'language_header_is_default', 'value'=>'Язык по умолчанию'),
            'terms_updated'=>array('language'=>'1', 'module'=>'1','name'=>'terms_updated', 'value'=>'Термин обновлен'),
        );
        $check = DB::getColumn('SELECT name FROM word WHERE language = 1 AND module = "1"', "module", "name");
        foreach ($check as $name) {
            unset($words[$name]);
        }
        foreach($words as $word) {
            DB::query($insertQuery."(".$word['language'], $word['module'], $word['name'], $word['value']);
        }
    }
}

