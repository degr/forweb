<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 14.03.2015
 * Time: 19:32
 */
class Page_Admin_Includes{


    public function getContent()
    {
        $dispatcher = new Page_Dispatcher($_POST['href']);
        $dispatcher->handleRequest();
        $params = $dispatcher->getParams();
        /* @var $pageService Page_Service */
        $pageService = Core::getModule("Page_Service");
        $page = $pageService->findPage($params);
        $template = $page->getTemplate();
        $core = Core::getInstance();
        $blocks = $core->getBlocks($template->getId());
        $includes = $core->getPageIncludes($page);


        $out = $this->getIncludesRepresentation($blocks, $includes, $page);

        return $out;
    }

    /**
     * @param $blocks array
     * @param $includes array
     * @param $page PersistPages
     * @return array
     */
    protected function getIncludesRepresentation($blocks, $includes, $page)
    {
        $out = array();
        /* @var $includesTable ORM_Table */
        $includesTable = ORM::getTable("includes");
        foreach($blocks as $blockId => $block){
            $out[$block] = UI::getFormForTable($includesTable, null, UI::LAYOUT_OVERVIEW);
            $out[$block]['id'] = $blockId;
            $out[$block]['page_id'] = $page->getId();
            unset($out[$block]['fields']);
        }
        /* @var $pageModule Page */
        $pageModule = Core::getModule("Page");
        $modulesList = $pageModule->getModulesList();
        array_unshift($modulesList, ' --- ');
        $methodsList = array();
        if ($includes) {
            $typesList = Page::getIncludeTypesList();
            /* @var $include PersistIncludes */
            foreach ($includes as $include) {
                $form = UI::usePersistObject($include, $includesTable, UI::LAYOUT_OVERVIEW);
                $form['type']['options'] = $typesList;
                $form['module']['options'] = $modulesList;
                $form['module']['tag'] = UI::TAG_SELECT;
                $form['method']['tag'] = UI::TAG_SELECT;

                if(!empty($form['module']['value'])){
                    $moduleName = $form['module']['value'];
                    if(empty($methodsList[$moduleName])){
                        $methodsList[$moduleName] = $pageModule->getMethodsList($moduleName);
                    }
                    $form['method']['options'] = $methodsList[$moduleName];
                }
                foreach($form as &$formfield) {
                    $formfield['class'] = $include->getPosition();
                }
                unset($formfield);
                $data = array(
                    'fields'=>$form,
                    'tag' => UI::TAG_FIELDSET,
                    'layout' => UI::LAYOUT_OVERVIEW
                );
                $out[$blocks[$include->getBlock()]]['fields'][$include->getPosition()][] = $data;
            }

            foreach($out as $key => &$block) {
                $block['title'] = $key;

                $block['fields']['submit'] = UI::getSubmitButton();
            }
        }
        return $out;
    }


    public function onAjaxSaveBlock(){
        $pageId = intval($_GET['page']);
        if(empty($pageId) && $pageId !== 0) {
            return array("text"=>"Page identifier must be specified.");
        }
        $blockId = intval($_GET['block']);
        if(empty($blockId) && $blockId !== 0) {
            return array("text"=>"Block identifier must be specified.");
        }

        $ids = DB::getColumn("SELECT id FROM includes where page = '".DB::escape($pageId)
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
            $data['template'] = 0;
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
                ."' AND page = '".DB::escape($pageId)."' ORDER BY positionNumber";
            $allIds = DB::getColumn($query);
        }
        return array('text'=>implode("<br/>",$out), 'ids'=>$allIds, 'block' => $blockId);
    }

    public function delete(){
        if(!empty($_GET['include'])) {
            if(DB::query("DELETE FROM includes where id = '".addslashes($_GET['include'])."'")) {
                return 1;
            }
        }
        return '0';
    }

    public function getIncludeTextForm()
    {
        $module = DB::getCell("select id from word_modules where module='".DB::escape(Core::SYS_INCLUDES)."'");
        $includeId = intval($_POST['id']);
        if(empty($includeId) || empty($module)) {
            return array();
        }
        $textIdQuery = "select id from word where name=".$includeId." and module = ".$module;
        $textId = DB::getCell($textIdQuery);
        if(empty($textId)) {
            $language = Word::getLanguage();
            DB::query("insert into word (language, module, name, value) VALUES ("
                .$language['id'].", ".$module.", ".$includeId.", '')");
            $textId = DB::getCell($textIdQuery);
        }
        $formProvider = new Word_UI();
        $form = $formProvider->onAjaxGetTermForm($textId, $module);
        $form['form']['fields']['module']['attributes']['type'] = 'hidden';
        $form['form']['fields']['module']['tag'] = 'input';
        $form['form']['fields']['name']['attributes']['type'] = 'hidden';
        return $form;
    }

}