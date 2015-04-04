<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 14.03.2015
 * Time: 19:35
 */
class Page_Admin_Template{

    public function getContent()
    {
        $dispatcher = new Page_Dispatcher($_POST['href']);
        $dispatcher->handleRequest();
        $params = $dispatcher->getParams();
        /* @var $pageService Page_Service */
        $pageService = Core::getModule("Page_Service");
        $page = $pageService->findPage($params);
        $template = $page->getTemplate();

        $out = array();
        $out['baseForm'] = UI::getFormForTable(ORM::getTable("templates"), $template, UI::LAYOUT_TABLE);
        $templatesArray = $this->getParentOptions();
        $fields = &$out['baseForm']['fields'];
        foreach($fields as &$field){
            $field['title'] = Word::get('admin', 'template_field_'.$field['name']);
        }

        $fields['template']['tag'] = UI::TAG_SELECT;
        $fields['template']['options'] = $templatesArray;
        $fields['parent']['tag'] = UI::TAG_SELECT;
        $fields['parent']['options'] = $templatesArray;
        unset($out['baseForm']['fields']['parent']['options'][$template->getId()]);
        $fields['template']['value'] = $template->getId();
        $fields['template']['options'] = $this->getTemplateOptions();

        $out['baseForm']['fields']['submit'] = UI::getSubmitButton();
        $out['templateIncludes'] = $this->getIncludes($template->getId());
        /* @var $pageModule Page */
        $pageModule = Core::getModule('Page');
        $out['modules'] = $pageModule->getModulesList();
        return $out;
    }

    private function getParentOptions(){
        return array(0=>' --- ') + DB::getAssoc("SELECT id, name FROM templates", "id", "name");
    }

    /**
     * Get includes for template
     * @param $id - template id
     * @param $block_id - block id. If undefined, all blocks for template will be used
     * @return array
     */
    private function getIncludes($id, $block_id=0)
    {
        if(empty($id)) {
            return array();
        }
        $blockCondition1 = !empty($block_id) ? " AND id='".intval($block_id)."' " : "";
        $blockCondition2 = !empty($block_id) ? " AND block='".intval($block_id)."' " : "";
        $out = DB::getTable("SELECT * FROM blocks WHERE template = ".$id." "
            .$blockCondition1." ORDER BY position");

        $query = "SELECT i.*, b.name as block_name FROM includes AS i "
            ." INNER JOIN blocks AS b ON (i.block = b.id) "
            ."WHERE i.template = '".$id."' "
            .$blockCondition2." ORDER BY positionNumber";

        $includes = DB::getTable($query);
        $pageModule = Core::getModule("Page");
        $methods = array();
        foreach($includes as $include) {
            foreach($out as $key => $item) {
                if($item['id'] == $include['block']) {
                    if($include['type'] == 'executable') {
                        if(empty($methods[$include['module']])) {
                            $methods[$include['module']] = $pageModule->getMethodsList($include['module']);
                        }
                        $include['methods_list'] = $methods[$include['module']];
                    }
                    $out[$key]['includes'][] = $include;

                    break;
                }
            }
        }
        return $out;
    }

    /**
     * @return array
     */
    private function getTemplateOptions()
    {
        $out = array();
        $files = glob(UI::TEMPLATES_DIR."/*");
        foreach($files as $file) {
            if (is_file($file)) {
                $name = basename($file);
                $out[$name] = $name;
            }
        }
        return $out;
    }

    public function processTemplateEdit()
    {
        if(empty($_POST['id'])) {
            $out = Word::get('admin', 'new_template_created');
        } else {
            $out = Word::get('admin', 'template_updated');
        }
        $table = ORM::getTable("templates");
        if(empty($_POST['parent'])) {
            $_POST['parent'] = 0;
        }
        $template = ORM::buildObject($table, $_POST);
        ORM::saveData($table, $template[0]);
        return $out;
    }

    public function deleteTemplate()
    {
        if(empty($_POST['id'])) {
            return array('text'=>"Can't delete template without id.", 'success'=>0);
        }
        if($_POST['id'] == '1') {
            return array('text'=>"Can't delete basic template", 'success'=>0);
        }
        $id = intval($_POST['id']);
        $pages = DB::getColumn("SELECT name FROM pages where template = ".$id);
        if(!empty($pages)) {
            return "Can't delete template, because it used on pages: ".implode(", ", $pages);
        }

        $service = new Service("templates");
        $service->deleteById($id);
        return array('text'=>"Template was deleted.", 'success'=>1);
    }

    public function createTemplate()
    {
        $table = ORM::getTable("templates");
        $form = UI::getFormForTable($table, array(), UI::LAYOUT_GRID);
        unset($form['fields']['id']);
        foreach($form['fields'] as &$field) {
            $field['title'] = Word::get('admin', 'template_field_'.$field['name']);
        }
        unset($field);
        $form['fields']['parent']['tag'] = UI::TAG_SELECT;
        $form['fields']['parent']['options'] = $this->getParentOptions();
        $form['fields']['template']['tag'] = UI::TAG_SELECT;
        $form['fields']['template']['options'] = $this->getTemplateOptions();
        $form['fields']['submit'] = UI::getSubmitButton();
        return $form;
    }

    public function saveNewTemplate()
    {
        $table = ORM::getTable("templates");
        $template = ORM::buildObject($table, json_decode($_POST['form'], true));
        ORM::saveData($table, $template[0]);
        return array('text'=>Word::get("admin",'new_page_template'));
    }

    /**
     *
     */
    public function getDeleteTemplateForm()
    {
        $form = array(
            'fields' => array(),
            'id' => "delete_template_form",
            'method' => "POST",
            'type' => "form"
        );
        $select = new UI_Formfield();
        $select->setTag(UI::TAG_SELECT);
        $select->setOptions(DB::getAssoc("SELECT id, name FROM templates", 'id', 'name'));
        $select->setName('id');
        $select->setLayout('grid');
        $form['fields']['select'] = $select->toJSON();
        $form['fields']['submit'] = UI::getSubmitButton();
        $form['fields']['submit']['value'] = 'delete';
        return $form;
    }

    public function createBlock()
    {
        $name = $_POST['name'];
        if(empty($name)) {
            return array('text' => Word::get('admin','block_name_empty'), 'errors'=>1);
        }
        $templateId = intval($_POST['templateId']);
        if(empty($templateId)) {
            return array('text' => Word::get('admin','template_id_empty'), 'errors'=>1);
        }
        $allBlocksQuery = "SELECT * FROM blocks where template=".$templateId." ORDER BY position DESC";
        $blockData = DB::getTable($allBlocksQuery, 'name');
        if(!empty($blockData[$name])) {
            return array('text'=>Word::get('admin','block_name_not_unique'), 'errors'=>1);
        }
        $currentPosition = 1;
        if(count($blockData) > 0) {
            $block = reset($blockData);
            $currentPosition = intval($block['position']) + 1;
        }
        $query = "INSERT INTO blocks(name, position, template) VALUES ('"
            .DB::escape($name)."', ".$currentPosition.", ".$templateId.")";
        DB::query($query);

        $block = DB::getRow($allBlocksQuery." LIMIT 1");
        $inc = $this->getIncludes($templateId, $block['id']);

        return array('text'=>Word::get('admin', 'block_created'), 'errors'=>0, 'block'=>reset($inc));
    }

    public function deleteBlock()
    {
        $id = intval($_POST['id']);
        if(empty($id)) {
            return '0';
        }
        $templateId = intval($_POST['templateId']);
        if(empty($templateId)) {
            return '0';
        }
        $service = new Service("blocks");
        $service->deleteById($id);
        return 1;
    }

    public function saveBlocksPosition()
    {
        $templateId = intval($_POST['template']);
        if(empty($templateId)) {
            return '0';
        }
        $query = "SELECT id FROM blocks WHERE template=".$templateId;
        $ids = DB::getColumn($query);
        $requestIds = json_decode($_POST['blocks'], true);
        if(count($ids) !== count($requestIds)){
            return '0';
        }
        foreach($ids as $id) {
            if(!in_array($id, $requestIds)) {
                return '0';
            }
        }
        $i = 1;
        foreach($requestIds as $newId) {
            $query = "UPDATE blocks SET position=".$i." WHERE id=".intval($newId);
            echo $query."<br/>";
            DB::query($query);
            $i++;
        }
        return 1;
    }

    public function updateBlock()
    {
        $templateId = intval($_GET['template']);
        if(empty($templateId)) {
            return '0';
        }
        $blockId = intval($_GET['block']);
        if(empty($blockId)) {
            return '0';
        }

        $ids = DB::getColumn("SELECT id FROM includes where template = '".DB::escape($templateId)
            ."' AND block = '".$blockId."'");
        $newIds = array();
        $out = array();
        $table = ORM::getTable("includes");
        $onCreate = false;
        foreach($_POST as $stringData) {
            $data = json_decode($stringData, true);
            if(!empty($data['id'])) {
                $out[] = "Was updated include with id: ".$data['id'];
                $newIds[] = $data['id'];
            } else {
                $onCreate = true;
                $out[] = "Was created new include.";
            }
            $data['page'] = 0;
            /* @var $includeObject PersistIncludes */
            $includeObject = ORM::buildObject($table, $data);
            ORM::saveData($table, $includeObject[0]);
        }
        $ids = array_diff($ids, $newIds);
        if(count($ids) > 0) {
            foreach ($ids as &$id) {
                $id = DB::escape($id);
            }
            $out[] = "Was deleted includes with ids: [".implode(", ", $ids)."]";
            unset($id);
            DB::query("DELETE FROM includes WHERE id IN('".implode("','", $ids)."')");
        }
        $allIds = array();
        if($onCreate) {
            $query = "SELECT id FROM includes where block='".$blockId
                ."' AND template = '".DB::escape($templateId)."' ORDER BY positionNumber";
            $allIds = DB::getColumn($query);
        }
        return array('text'=>implode("<br/>",$out), 'ids'=>$allIds, 'block' => $blockId);
    }
}