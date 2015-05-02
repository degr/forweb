<?php
class Word_Install_Words {
	public function install($languages, $modules) {
	$insertQuery = "INSERT INTO word (language, module, name, value) VALUES ";//
	$languageId = $languages['en'];
	$moduleId = $modules['admin'];
	$words = array(
		'panel_new_page'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_new_page', 'value'=>'New page'),
		'word_reset_term'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'word_reset_term', 'value'=>''),
		'panel_edit_page'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_edit_page', 'value'=>'This page'),
		'panel_show_pages'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_show_pages', 'value'=>'Pages tree'),
		'panel_page_content'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_page_content', 'value'=>'Page content'),
		'panel_templates'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_templates', 'value'=>'Templates'),
		'panel_edit_access'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_edit_access', 'value'=>'Access'),
		'panel_word'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_word', 'value'=>'Translates'),
		'panel_configuration'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_configuration', 'value'=>'Configuration'),
		'panel_position'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_position', 'value'=>'< Position >'),
		'panel_languages'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_languages', 'value'=>'Languages'),
		'panel_modules'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_modules', 'value'=>'Dictionaries'),
		'panel_create_template'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_create_template', 'value'=>'Create template'),
		'panel_edit_template'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_edit_template', 'value'=>'Edit template'),
		'panel_delete_template'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_delete_template', 'value'=>'Delete template'),
		'page_form_field_name'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'page_form_field_name', 'value'=>'Page name'),
		'page_form_field_url'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'page_form_field_url', 'value'=>'Page URL'),
		'page_form_field_parent'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'page_form_field_parent', 'value'=>'Parent page'),
		'page_form_field_template'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'page_form_field_template', 'value'=>'Page template'),
		'page_form_field_position'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'page_form_field_position', 'value'=>''),
		'admin_page_name'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'admin_page_name', 'value'=>'Page name'),
		'admin_page_url'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'admin_page_url', 'value'=>'Page url'),
		'admin_page_parent'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'admin_page_parent', 'value'=>'Parent page'),
		'admin_page_template'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'admin_page_template', 'value'=>'Page template'),
		'admin_page_position'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'admin_page_position', 'value'=>'Page number'),
		'delete_page'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'delete_page', 'value'=>'To delete page, press here'),
		'home_page_delete'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'home_page_delete', 'value'=>'You can\'t delete home page.'),
		'unknown_error'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'unknown_error', 'value'=>'Something goes wrong. We are sorry...'),
		'page_contain_children'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'page_contain_children', 'value'=>'You can\'t delete page width child pages.'),
		'page_created'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'page_created', 'value'=>'Page created.'),
		'page_modified'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'page_modified', 'value'=>'Page data modified.'),
		'page_url_empty'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'page_url_empty', 'value'=>'Page url can\'t be empty.'),
		'page_url_not_unique'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'page_url_not_unique', 'value'=>'Page with such url is already exist. Change url, or parent page.'),
		'show_content'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'show_content', 'value'=>'Show content'),
		'hide_content'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'hide_content', 'value'=>'Hide content'),
		'field_type'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'field_type', 'value'=>'Type'),
		'field_module'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'field_module', 'value'=>'Module'),
		'field_method'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'field_method', 'value'=>'Method'),
		'put_comment'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'put_comment', 'value'=>'Put a comment here'),
		'include_textarea'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'include_textarea', 'value'=>'Insert any text content here, and than push red cross on left top angle.'),
		'template_field_name'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'template_field_name', 'value'=>'Template name'),
		'template_field_parent'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'template_field_parent', 'value'=>'Parent template (not implemented yet)'),
		'template_field_template'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'template_field_template', 'value'=>'Template file'),
		'new_access_group'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'new_access_group', 'value'=>'New User Group'),
		'new_access_action'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'new_access_action', 'value'=>'New access permission'),
		'new_access_group_prompt'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'new_access_group_prompt', 'value'=>'Enter new user group name.<br/> Use \\\"_\\\" char as word separator'),
		'new_access_action_prompt'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'new_access_action_prompt', 'value'=>'Enter new access action name.<br/> Use \\\"_\\\" char as word separator'),
		'delete_include_confirm'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'delete_include_confirm', 'value'=>'Are you realy want to delete this include?<br/> Some site content can disappear.'),
		'confirm_yes'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'confirm_yes', 'value'=>'I know what I do'),
		'confirm_no'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'confirm_no', 'value'=>'No'),
		'delete_language_confirm'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'delete_language_confirm', 'value'=>'Realy delete this item?<br/> All terms with this alias will be deleted too, and operation can\'t be undone.'),
		'delete_access_group'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'delete_access_group', 'value'=>'Realy delete this user group?'),
		'delete_config_option'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'delete_config_option', 'value'=>'Realy delete this option?<br/>All options required for stable system work.'),
		'delete_template_block'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'delete_template_block', 'value'=>'Realy delete this block?<br/> Some site content can suddenly disappear.'),
		'new_template_created'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'new_template_created', 'value'=>'New template was created'),
		'template_updated'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'template_updated', 'value'=>'Данные шаблона обновлены'),
		'block_created'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'block_created', 'value'=>'Block was created.'),
		'block_name_not_unique'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'block_name_not_unique', 'value'=>'This template already contain block with this name.'),
		'template_id_empty'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'template_id_empty', 'value'=>'Template id must be specified'),
		'block_name_empty'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'block_name_empty', 'value'=>'Block name must be specified'),
		'new_page_template'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'new_page_template', 'value'=>'New template created. It can be used with page edit form.'),
		'back_to_dictionaries'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'back_to_dictionaries', 'value'=>'To Dictionaries overview'),
		'back_to_current_dict'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'back_to_current_dict', 'value'=>'Back to current dictionary'),
		'word_name_filter'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'word_name_filter', 'value'=>'Filter by name'),
		'word_value_filter'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'word_value_filter', 'value'=>'Filter by value'),
		'back_to_page_blocks'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'back_to_page_blocks', 'value'=>'Back, to page edit form'),
		'include_not_saved'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'include_not_saved', 'value'=>'You can\'t edit include content before include save.'),
		'panel_edit_files'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_edit_files', 'value'=>'Files'),
		'panel_word_keys'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_word_keys', 'value'=>'Show keys'),
		'panel_file_images'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_file_images', 'value'=>'Images'),
		'panel_file_templates'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_file_templates', 'value'=>'Templates'),
		'panel_file_js'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_file_js', 'value'=>'JavaScript'),
		'panel_file_css'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'panel_file_css', 'value'=>'Styles (css)'),
		'files_delete_file'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'files_delete_file', 'value'=>'Are you realy want to delete this file? Operation can not be undone.'),
		'file_path_empty'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'file_path_empty', 'value'=>'File path is empty.'),
		'file_not_deleted'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'file_not_deleted', 'value'=>'Can\'t delete file. Perhaps you have no access, or file is not exist.'),
		'file_deleted'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'file_deleted', 'value'=>'File was deleted.'),
		'file_uploaded'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'file_uploaded', 'value'=>'File uploaded to server.'),
		'file_or_folder_exist'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'file_or_folder_exist', 'value'=>'File with this name already exist in current folder. Are you want to continue? (old file will be deleted)'),
		'enter_new_file_name'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'enter_new_file_name', 'value'=>'Enter new file (directory) name.'),
		'admin_page_active'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'admin_page_active', 'value'=>'Page active'),
		'admin_page_in_menu'=>array('language'=>$languages['en'], 'module'=>$modules['admin'],'name'=>'admin_page_in_menu', 'value'=>'Page in menu'),
	);
	$check = DB::getColumn('SELECT name FROM word WHERE language = "'.$languageId.'" AND module = "'.$moduleId.'"');
	foreach ($check as $name) {
		unset($words[$name]);
	}
	foreach($words as $word) {
		DB::query($insertQuery."('".$word['language']."', '".$word['module']."', '".$word['name']."', '".DB::escape($word['value'])."')");
	}
	$languageId = $languages['ru'];
	$moduleId = $modules['admin'];
	$words = array(
		'panel_new_page'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_new_page', 'value'=>'Создать страницу'),
		'word_reset_term'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'word_reset_term', 'value'=>'Новый термин'),
		'panel_edit_page'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_edit_page', 'value'=>'Эта страница'),
		'panel_show_pages'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_show_pages', 'value'=>'Дерево страниц'),
		'panel_page_content'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_page_content', 'value'=>'Блоки страницы'),
		'panel_templates'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_templates', 'value'=>'Шаблоны'),
		'panel_edit_access'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_edit_access', 'value'=>'Доступ'),
		'panel_word'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_word', 'value'=>'Переводы'),
		'panel_configuration'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_configuration', 'value'=>'Настройки'),
		'panel_position'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_position', 'value'=>'< Позиция >'),
		'panel_languages'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_languages', 'value'=>'Языки'),
		'panel_modules'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_modules', 'value'=>'Словари'),
		'panel_create_template'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_create_template', 'value'=>'Создать шаблон'),
		'panel_edit_template'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_edit_template', 'value'=>'Править шаблон'),
		'panel_delete_template'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_delete_template', 'value'=>'Удалить шаблон'),
		'page_form_field_name'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'page_form_field_name', 'value'=>'Название страницы'),
		'page_form_field_url'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'page_form_field_url', 'value'=>'URL страницы'),
		'page_form_field_parent'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'page_form_field_parent', 'value'=>'Родительская страница'),
		'page_form_field_template'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'page_form_field_template', 'value'=>'Шаблон страницы'),
		'page_form_field_position'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'page_form_field_position', 'value'=>'Позиция страницы'),
		'admin_page_name'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'admin_page_name', 'value'=>'Название страницы'),
		'admin_page_url'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'admin_page_url', 'value'=>'URL страницы'),
		'admin_page_parent'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'admin_page_parent', 'value'=>'Страница - родитель'),
		'admin_page_template'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'admin_page_template', 'value'=>'Шаблон страницы'),
		'admin_page_position'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'admin_page_position', 'value'=>'Номер страницы'),
		'delete_page'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'delete_page', 'value'=>'Удалить эту страницу'),
		'home_page_delete'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'home_page_delete', 'value'=>'Нельзя удалить главную страницу.'),
		'unknown_error'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'unknown_error', 'value'=>'Что-то не сработало. Нам очень жаль...'),
		'page_contain_children'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'page_contain_children', 'value'=>'Нельзя удалить страницу, пока существуют дочерние страницы.'),
		'page_created'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'page_created', 'value'=>'Страница создана.'),
		'page_modified'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'page_modified', 'value'=>'Страница изменена.'),
		'page_url_empty'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'page_url_empty', 'value'=>'URL страницы не может быть пустым.'),
		'page_url_not_unique'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'page_url_not_unique', 'value'=>'Страница с таким URL\\\'ом уже существует. Либо измените URL, либо родительскую страницу.'),
		'show_content'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'show_content', 'value'=>'Показать содержимое'),
		'hide_content'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'hide_content', 'value'=>'Спрятать содержимое'),
		'field_type'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'field_type', 'value'=>'Тип'),
		'field_module'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'field_module', 'value'=>'Модуль'),
		'field_method'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'field_method', 'value'=>'Метод'),
		'put_comment'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'put_comment', 'value'=>'Оставь здесь комментарий'),
		'include_textarea'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'include_textarea', 'value'=>'Введите сюда любое содержимое, затем нажмите красный крест справа сверху.'),
		'template_field_name'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'template_field_name', 'value'=>'Название шаблона'),
		'template_field_parent'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'template_field_parent', 'value'=>'Родительский шаблон (пока что не работает)'),
		'template_field_template'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'template_field_template', 'value'=>'Файл шаблона'),
		'new_access_group'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'new_access_group', 'value'=>'Новая группа пользователей'),
		'new_access_action'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'new_access_action', 'value'=>'Новая проверка доступа'),
		'new_access_group_prompt'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'new_access_group_prompt', 'value'=>'Введите название новой группы пользователей.<br/>Используйте \'_\' вместо пробелов.'),
		'new_access_action_prompt'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'new_access_action_prompt', 'value'=>'Введите название новой проверки доступа.<br/>Используйте символ \'_\' вместо пробелов.'),
		'delete_include_confirm'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'delete_include_confirm', 'value'=>'Вы действительно хотите удалить это вложение?<br/>Часть содержимого сайта будет удалена.'),
		'confirm_yes'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'confirm_yes', 'value'=>'Я знаю что делаю'),
		'confirm_no'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'confirm_no', 'value'=>'Нет'),
		'delete_language_confirm'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'delete_language_confirm', 'value'=>'Действительно удалить?<br/>Все связанные переводы будут также удалены, и операцию нельзя будет отменить.'),
		'delete_access_group'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'delete_access_group', 'value'=>'Действительно удалить эту группу пользователей?'),
		'delete_config_option'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'delete_config_option', 'value'=>'Действительно удалить эту настройку?<br/>Скорее всего она необходима для стабильной работы системы.'),
		'delete_template_block'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'delete_template_block', 'value'=>'Действительно удалить этот блок?<br/>Все связанное с ним содержимое будет также удалено.'),
		'new_template_created'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'new_template_created', 'value'=>'Новый шаблон был создан.'),
		'template_updated'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'template_updated', 'value'=>''),
		'block_created'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'block_created', 'value'=>'Создан новый блок'),
		'block_name_not_unique'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'block_name_not_unique', 'value'=>'Этот шаблон уже содержит блок с таким именем.'),
		'template_id_empty'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'template_id_empty', 'value'=>'Идентификатор шаблона не установлен'),
		'block_name_empty'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'block_name_empty', 'value'=>'Название блока не должно быть пустым'),
		'new_page_template'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'new_page_template', 'value'=>'Новый шаблон создан. Его можно подключить из формы редактирования страницы.'),
		'back_to_dictionaries'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'back_to_dictionaries', 'value'=>'К обзору словарей'),
		'back_to_current_dict'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'back_to_current_dict', 'value'=>'Назад, к текущему словарю'),
		'word_name_filter'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'word_name_filter', 'value'=>'Фильтр по имени'),
		'word_value_filter'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'word_value_filter', 'value'=>'Фильтр по значению'),
		'back_to_page_blocks'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'back_to_page_blocks', 'value'=>'Назад, к редактированию страницы'),
		'include_not_saved'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'include_not_saved', 'value'=>'Нельзя редактировать контент инклуда, если он не был сохранен.'),
		'panel_edit_files'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_edit_files', 'value'=>'Файлы'),
		'panel_word_keys'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_word_keys', 'value'=>'Показать ключи'),
		'panel_file_images'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_file_images', 'value'=>'Картинки'),
		'panel_file_templates'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_file_templates', 'value'=>'Шаблоны'),
		'panel_file_js'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_file_js', 'value'=>'JavaScript'),
		'panel_file_css'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'panel_file_css', 'value'=>'Стили (css)'),
		'files_delete_file'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'files_delete_file', 'value'=>'Действительно удалить этот файл? Операцию нельзя будет отменить.'),
		'file_path_empty'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'file_path_empty', 'value'=>'Не указан путь к удаляемому файлу.'),
		'file_not_deleted'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'file_not_deleted', 'value'=>'Не удалось удалить файл. Возможно нет доступа, или файл не существует.'),
		'file_deleted'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'file_deleted', 'value'=>'Файл успешно удален.'),
		'file_uploaded'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'file_uploaded', 'value'=>'Файл загружен на сервер.'),
		'file_or_folder_exist'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'file_or_folder_exist', 'value'=>'Файл с таким именем уже существует в данной папке. Продолжить? (старый файл отправится в ад, например).'),
		'enter_new_file_name'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'enter_new_file_name', 'value'=>'Введите название нового файла (папки)'),
		'admin_page_active'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'admin_page_active', 'value'=>'Страница активна'),
		'admin_page_in_menu'=>array('language'=>$languages['ru'], 'module'=>$modules['admin'],'name'=>'admin_page_in_menu', 'value'=>'Страница в меню'),
	);
	$check = DB::getColumn('SELECT name FROM word WHERE language = "'.$languageId.'" AND module = "'.$moduleId.'"');
	foreach ($check as $name) {
		unset($words[$name]);
	}
	foreach($words as $word) {
		DB::query($insertQuery."('".$word['language']."', '".$word['module']."', '".$word['name']."', '".DB::escape($word['value'])."')");
	}
	$languageId = $languages['en'];
	$moduleId = $modules['common'];
	$words = array(
		'submit'=>array('language'=>$languages['en'], 'module'=>$modules['common'],'name'=>'submit', 'value'=>'Submit'),
		'include_type_html'=>array('language'=>$languages['en'], 'module'=>$modules['common'],'name'=>'include_type_html', 'value'=>'HTML markup'),
		'include_type_text'=>array('language'=>$languages['en'], 'module'=>$modules['common'],'name'=>'include_type_text', 'value'=>'Text'),
		'include_type_image'=>array('language'=>$languages['en'], 'module'=>$modules['common'],'name'=>'include_type_image', 'value'=>'Image'),
		'include_type_executable'=>array('language'=>$languages['en'], 'module'=>$modules['common'],'name'=>'include_type_executable', 'value'=>'Executable code'),
	);
	$check = DB::getColumn('SELECT name FROM word WHERE language = "'.$languageId.'" AND module = "'.$moduleId.'"');
	foreach ($check as $name) {
		unset($words[$name]);
	}
	foreach($words as $word) {
		DB::query($insertQuery."('".$word['language']."', '".$word['module']."', '".$word['name']."', '".DB::escape($word['value'])."')");
	}
	$languageId = $languages['ru'];
	$moduleId = $modules['common'];
	$words = array(
		'submit'=>array('language'=>$languages['ru'], 'module'=>$modules['common'],'name'=>'submit', 'value'=>'Отправить'),
		'include_type_html'=>array('language'=>$languages['ru'], 'module'=>$modules['common'],'name'=>'include_type_html', 'value'=>'HTML разметка'),
		'include_type_text'=>array('language'=>$languages['ru'], 'module'=>$modules['common'],'name'=>'include_type_text', 'value'=>'Текст'),
		'include_type_image'=>array('language'=>$languages['ru'], 'module'=>$modules['common'],'name'=>'include_type_image', 'value'=>'Изображение'),
		'include_type_executable'=>array('language'=>$languages['ru'], 'module'=>$modules['common'],'name'=>'include_type_executable', 'value'=>'Исполняемый код'),
	);
	$check = DB::getColumn('SELECT name FROM word WHERE language = "'.$languageId.'" AND module = "'.$moduleId.'"');
	foreach ($check as $name) {
		unset($words[$name]);
	}
	foreach($words as $word) {
		DB::query($insertQuery."('".$word['language']."', '".$word['module']."', '".$word['name']."', '".DB::escape($word['value'])."')");
	}
	$languageId = $languages['en'];
	$moduleId = $modules['files'];
	$words = array(
		'dir_write_forbidden'=>array('language'=>$languages['en'], 'module'=>$modules['files'],'name'=>'dir_write_forbidden', 'value'=>'This folder protected from writing.'),
		'file_save'=>array('language'=>$languages['en'], 'module'=>$modules['files'],'name'=>'file_save', 'value'=>'File saved'),
	);
	$check = DB::getColumn('SELECT name FROM word WHERE language = "'.$languageId.'" AND module = "'.$moduleId.'"');
	foreach ($check as $name) {
		unset($words[$name]);
	}
	foreach($words as $word) {
		DB::query($insertQuery."('".$word['language']."', '".$word['module']."', '".$word['name']."', '".DB::escape($word['value'])."')");
	}
	$languageId = $languages['ru'];
	$moduleId = $modules['files'];
	$words = array(
		'dir_write_forbidden'=>array('language'=>$languages['ru'], 'module'=>$modules['files'],'name'=>'dir_write_forbidden', 'value'=>'Директория защищена от записи.'),
		'file_save'=>array('language'=>$languages['ru'], 'module'=>$modules['files'],'name'=>'file_save', 'value'=>'Файл сохранен'),
	);
	$check = DB::getColumn('SELECT name FROM word WHERE language = "'.$languageId.'" AND module = "'.$moduleId.'"');
	foreach ($check as $name) {
		unset($words[$name]);
	}
	foreach($words as $word) {
		DB::query($insertQuery."('".$word['language']."', '".$word['module']."', '".$word['name']."', '".DB::escape($word['value'])."')");
	}
	$languageId = $languages['en'];
	$moduleId = $modules['sys_includes'];
	$words = array(
		'15'=>array('language'=>$languages['en'], 'module'=>$modules['sys_includes'],'name'=>'15', 'value'=>'<h1>Congratulations, just now you install ForWeb framework</h1>
<p>This PHP based framework have modules structure. It\\\'s scalable flexible, and simple in understanding and controlling by administrator. Also, this framework writed for single-page applications</p>
<p>For quick strart user must understand how does it create pages, and understand what admin panel doing. As ususal, adin panel is fixes at left side of your screen.</p>
<p>Data output system have unusual but simple structure and each developer, who will maintenace application must understand how it work. ForWeb framework documentation is available on <a href=\\\\\"http://forweb.org/\\\\\">http://forweb.org/</a>.</p>
<p>Administator panel provide functionality for page creating and editing, page templates creating and editing, access operations, such as allow/disallow access to user group, new user groups creating, provide access to some files, that stored on your hosting, upload images and some more. Also, ForWeb have multy-language support, and using admin panel you can edit translations.</p>'),
	);
	$check = DB::getColumn('SELECT name FROM word WHERE language = "'.$languageId.'" AND module = "'.$moduleId.'"');
	foreach ($check as $name) {
		unset($words[$name]);
	}
	foreach($words as $word) {
		DB::query($insertQuery."('".$word['language']."', '".$word['module']."', '".$word['name']."', '".DB::escape($word['value'])."')");
	}
	$languageId = $languages['ru'];
	$moduleId = $modules['sys_includes'];
	$words = array(
		'15'=>array('language'=>$languages['ru'], 'module'=>$modules['sys_includes'],'name'=>'15', 'value'=>'<h1>Вы только что установили ForWeb фреймворк</h1>
<p>Этот фреймворк написан на PHP, и имеет модульную структуру. Его легко расширять новым функционалом, он удобен в использовании для конечного пользователя, и рассчитан на single-page приложения.</p>
<p>Для начала работы необходимо понять, каким образом работает приложение, и научится работать с панелью администратора, которая по умолчанию находится слева.</p>
<p>Система вывода данных имеет довольно сложную структуру, знать которую должен программист, который будет заниматься сопровождением приложения, подробная документация находится по адресу <a href=\\\"http://forweb.org/\\\">http://forweb.org/</a>.</p>
<p>Панель администратора позволяет создавать страницы, редактировать их, создавать новые шаблоны для страниц, управлять доступом пользователей, создавать новые группы пользователей, менять настройки приложения, редактировать некоторые файлы, которые хранятся на хостинге, заливать картинки. Помимо этого, ForWeb имеет мультиязыковую поддержку, и через админ панель можно с переводами.</p>'),
	);
	$check = DB::getColumn('SELECT name FROM word WHERE language = "'.$languageId.'" AND module = "'.$moduleId.'"');
	foreach ($check as $name) {
		unset($words[$name]);
	}
	foreach($words as $word) {
		DB::query($insertQuery."('".$word['language']."', '".$word['module']."', '".$word['name']."', '".DB::escape($word['value'])."')");
	}
	$languageId = $languages['en'];
	$moduleId = $modules['user'];
	$words = array(
		'logout'=>array('language'=>$languages['en'], 'module'=>$modules['user'],'name'=>'logout', 'value'=>'Logout'),
		'field_email'=>array('language'=>$languages['en'], 'module'=>$modules['user'],'name'=>'field_email', 'value'=>'Email'),
		'field_password'=>array('language'=>$languages['en'], 'module'=>$modules['user'],'name'=>'field_password', 'value'=>'Password'),
	);
	$check = DB::getColumn('SELECT name FROM word WHERE language = "'.$languageId.'" AND module = "'.$moduleId.'"');
	foreach ($check as $name) {
		unset($words[$name]);
	}
	foreach($words as $word) {
		DB::query($insertQuery."('".$word['language']."', '".$word['module']."', '".$word['name']."', '".DB::escape($word['value'])."')");
	}
	$languageId = $languages['ru'];
	$moduleId = $modules['user'];
	$words = array(
		'logout'=>array('language'=>$languages['ru'], 'module'=>$modules['user'],'name'=>'logout', 'value'=>'Выход'),
		'field_email'=>array('language'=>$languages['ru'], 'module'=>$modules['user'],'name'=>'field_email', 'value'=>'Емейл'),
		'field_password'=>array('language'=>$languages['ru'], 'module'=>$modules['user'],'name'=>'field_password', 'value'=>'Пароль'),
	);
	$check = DB::getColumn('SELECT name FROM word WHERE language = "'.$languageId.'" AND module = "'.$moduleId.'"');
	foreach ($check as $name) {
		unset($words[$name]);
	}
	foreach($words as $word) {
		DB::query($insertQuery."('".$word['language']."', '".$word['module']."', '".$word['name']."', '".DB::escape($word['value'])."')");
	}
	$languageId = $languages['en'];
	$moduleId = $modules['word'];
	$words = array(
		'module_header_module'=>array('language'=>$languages['en'], 'module'=>$modules['word'],'name'=>'module_header_module', 'value'=>'Module name'),
		'language_header_locale'=>array('language'=>$languages['en'], 'module'=>$modules['word'],'name'=>'language_header_locale', 'value'=>'Language'),
		'new_term_created'=>array('language'=>$languages['en'], 'module'=>$modules['word'],'name'=>'new_term_created', 'value'=>'New term saved.'),
		'term_header_language'=>array('language'=>$languages['en'], 'module'=>$modules['word'],'name'=>'term_header_language', 'value'=>'Language'),
		'term_header_module'=>array('language'=>$languages['en'], 'module'=>$modules['word'],'name'=>'term_header_module', 'value'=>'Module'),
		'term_header_name'=>array('language'=>$languages['en'], 'module'=>$modules['word'],'name'=>'term_header_name', 'value'=>'Term key'),
		'term_header_value'=>array('language'=>$languages['en'], 'module'=>$modules['word'],'name'=>'term_header_value', 'value'=>'Term value'),
		'language_header_is_default'=>array('language'=>$languages['en'], 'module'=>$modules['word'],'name'=>'language_header_is_default', 'value'=>'Default language'),
		'terms_updated'=>array('language'=>$languages['en'], 'module'=>$modules['word'],'name'=>'terms_updated', 'value'=>'Tern updated'),
	);
	$check = DB::getColumn('SELECT name FROM word WHERE language = "'.$languageId.'" AND module = "'.$moduleId.'"');
	foreach ($check as $name) {
		unset($words[$name]);
	}
	foreach($words as $word) {
		DB::query($insertQuery."('".$word['language']."', '".$word['module']."', '".$word['name']."', '".DB::escape($word['value'])."')");
	}
	$languageId = $languages['ru'];
	$moduleId = $modules['word'];
	$words = array(
		'module_header_module'=>array('language'=>$languages['ru'], 'module'=>$modules['word'],'name'=>'module_header_module', 'value'=>'Название модуля'),
		'language_header_locale'=>array('language'=>$languages['ru'], 'module'=>$modules['word'],'name'=>'language_header_locale', 'value'=>'Язык'),
		'new_term_created'=>array('language'=>$languages['ru'], 'module'=>$modules['word'],'name'=>'new_term_created', 'value'=>'Новый термин успешно сохранен.'),
		'term_header_language'=>array('language'=>$languages['ru'], 'module'=>$modules['word'],'name'=>'term_header_language', 'value'=>'Язык'),
		'term_header_module'=>array('language'=>$languages['ru'], 'module'=>$modules['word'],'name'=>'term_header_module', 'value'=>'Модуль'),
		'term_header_name'=>array('language'=>$languages['ru'], 'module'=>$modules['word'],'name'=>'term_header_name', 'value'=>'Ключ термина'),
		'term_header_value'=>array('language'=>$languages['ru'], 'module'=>$modules['word'],'name'=>'term_header_value', 'value'=>'Значение термина'),
		'language_header_is_default'=>array('language'=>$languages['ru'], 'module'=>$modules['word'],'name'=>'language_header_is_default', 'value'=>'Язык по умолчанию'),
		'terms_updated'=>array('language'=>$languages['ru'], 'module'=>$modules['word'],'name'=>'terms_updated', 'value'=>'Термин обновлен'),
	);
	$check = DB::getColumn('SELECT name FROM word WHERE language = "'.$languageId.'" AND module = "'.$moduleId.'"');
	foreach ($check as $name) {
		unset($words[$name]);
	}
	foreach($words as $word) {
		DB::query($insertQuery."('".$word['language']."', '".$word['module']."', '".$word['name']."', '".DB::escape($word['value'])."')");
	}
	}
}