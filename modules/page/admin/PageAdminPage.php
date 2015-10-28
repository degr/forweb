<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 14.03.2015
 * Time: 20:21
 */
class PageAdminPage{

    /**
     * Prepare JSON object for page form
     * for amdin panel
     * @param $page PersistPages
     * @return array JSON
     * @throws Exception
     */
    public function getContent($page){
        $layout = UI::LAYOUT_TABLE;

        /* @var $pageService PageService */
        $pageService = Core::getModule("Page")->getService();
        $table = ORM::getTable($pageService->getTable()->getName());

        $form = UI::getFormForTable($table, $page, $layout);
        foreach($form['fields'] as &$field) {
            $field['title'] = Word::get('admin', 'admin_page_'.$field['title']);
        }
        unset($field);
        $form['fields']['parent']['tag'] = UI::TAG_SELECT;
        $query = "SELECT id, name FROM ".$table->getName();
        $parentsArray = DB::getAssoc($query, 'id', 'name');
        if($page != null) {
            unset($parentsArray[$page->getId()]);
        }

        $form['fields']['parent']['options'] = $parentsArray;

        $form['fields']['template']['tag'] = UI::TAG_SELECT;
        $query = "SELECT id, name FROM templates";
        $form['fields']['template']['options'] = DB::getAssoc($query, 'id', 'name');

        $form['fields']['submit'] = UI::getSubmitButton();
        $form['fields']['submit']['layout'] = $layout;
        $form['fields']['position']['attributes']['type'] = "hidden";

        if($page != null) {
            $id = $page->getId();
            if(!empty($id)){
                $link = $pageService->getPagePath($page);
                $form['fields']['link'] = array(
                    'tag' => 'html',
                    'value' => '<a href="'.CoreConfig::getUrl().$link.'">'.$link.'</a>',
                    'layout' => $layout,
                    'title' => Word::get('admin', 'page_form_field_url')
                );
                $form['fields']['delete'] = array(
                    'tag' => 'html',
                    'value' => '<a href="#" onclick="Admin.deletePage(event);">'.Word::get('admin', 'delete_page').'</a>',
                    'layout' => $layout
                );
            }
            if($id == 1) {
                unset($form['fields']['parent']);
            }
            $form['fields']['template']['value'] = $page->getTemplateId();
        }

        return $form;
    }

    public function editPage()
    {
        /* @var $pageService PageService*/
        $pageService = Core::getModule("Page")->getService();
        if(!empty($_POST['ajax_key']) && $_POST['ajax_key'] === 'pageedit'){
            $table = $pageService->getTable();
            $dto = ORM::buildObject($table, $_POST);
            /** @var $page PersistPages */
            if(empty($dto[1])) {
                $page = $dto[0];
            } else {
                $page = null;
            }
            if(!empty($_POST['deletePage'])){
                /* @var $parent PersistPages */
                $parent = $page->getParentPage();
                $deletePageKey = $this->onDeletePage($page, $parent, $table);
                if($deletePageKey == 'page_was_deleted') {
                    $parentLink = $pageService->getPagePath($parent);
                    $deleteText = Word::get('admin', $deletePageKey);
                }
            } elseif($page != null){
                $url = $page->getUrl();
                if($url != '') {
                    $onPageUpdate = $this->onUpdatePage($page, $table);
                    $saveText = $onPageUpdate['text'];
                    $savedStatus = $onPageUpdate['status'];
                }else {
                    $saveText = Word::get('admin', 'page_url_empty');
                    $savedStatus = false;
                }
            }
        }elseif(isset($_POST['href'])){
            $params = $pageService->parseUrlForParams($_POST['href']);
            $pageDto = $pageService->findPage($params);
            /** @var $page PersistPages */
            $page = $pageDto['page'];
        }else{
            $page = null;
        }
        /* @var $pageModule Page */
        $pageModule = Core::getModule('Page');
        $form = $pageModule->getPageForm($page);

        if(!empty($deleteText)){
            $form['text'] = $deleteText;
            if(!empty($parentLink)){
                $form['parentLink'] = CoreConfig::getUrl().$parentLink;
            }
        }
        if(!empty($saveText)){
            $form['text'] = $saveText;
            $savedStatus = empty($savedStatus) ? "" : $savedStatus;
            $form['savedStatus'] = $savedStatus;
        }
        return $form;
    }

    /**
     * @param $page PersistPages
     */
    private function updatePagePositions($page)
    {
        $position = $page->getPosition();
        $positions = DB::getAssoc(
            "select id, position from pages where parent = ".$page->getParent()." ORDER BY position",
            "id",
            "position"
        );
        $check = true;
        foreach($positions as $key => $pos) {
            if($pos == $position && $key != $page->getId()) {
                $check = false;
                break;
            }
        }
        if(!$check) {
            $i = 0;
            foreach($positions as $key => $pos) {
                DB::query("UPDATE pages set position = ".$i." where id=".$key);
                $i++;
            }
        }
    }

    public function changePagePositions()
    {
        $parent = intval($_POST['parent']);
        $data = json_decode($_POST['items'], true);
        if(empty($parent)) {
            return;
        }
        $ids = DB::getColumn("select id from pages where parent = ".$parent);
        if(count($ids) != count($data)) {
            return;
        }

        foreach($ids as $id) {
            if(!isset($data[$id])) {
                return;
            }else {
                $data[$id] = intval($data[$id]);
            }
        }
        $i = 0;
        foreach($data as $value) {
            if(!in_array($i, $data)) {
                return;
            }
            $i++;
        }

        foreach($data as $key => $value) {
            DB::query("update pages set position = ".$value." where id = ".$key);
        }
    }

    /**
     * @param $page PersistPages
     * @param $parent PersistPages
     * @param $table OrmTable
     * @return string
     */
    private function onDeletePage($page, $parent, $table)
    {
        /** @var $pageService PageService */
        $pageService = Core::getModule('Page')->getService();

        if($parent == null){
            $out = 'home_page_delete';
        } else {
            $deleteFilter = new OrmQueryCustomFilter("pages.parent = '".$page->getId()."'", true);

            $childs = $pageService->loadAll($deleteFilter);
            if(empty($childs[$pageService->getTable()->getName()])){
                if(ORM::delete($table, $page)) {
                    $page = null;
                    $out = "page_was_deleted";
                }else{
                    $out = 'unknown_error';
                }
            } else {
                $out = 'page_contain_children';
            }
        }
        return $out;
    }

    /**
     * @param $page PersistPages
     * @param $table OrmTable
     * @return array
     * @throws FwException
     */
    private function onUpdatePage($page, $table)
    {
        /** @var $pageService PageService */
        $pageService = Core::getModule('Page')->getService();

        $filters = array(
            new OrmQueryCustomFilter(" pages.parent='".$page->getParent()."' AND pages.url='".$page->getUrl()."'", true)
        );

        $id = $page->getId();
        if(!empty($id)){
            $filters[] = new OrmQueryCustomFilter(" pages.id != '".$id."'", true);
            if($id == 1){
                $page->setUrl('home');
                $page->setParent(0);
            }
        }

        $check = $pageService->loadOneWithFilters($filters);
        $position = $page->getPosition();
        if(empty($position)) {
            $page->setPosition(0);
        }
        $out = array(
            'text' => null,
            'status' => null
        );
        if($check === null) {
            $path = $pageService->getPagePath($page);
            $path = FilesSystem::removeFirstAndLastSlashes($path);
            $firstFolder = array_shift(explode('/', $path));
            if(!in_array($firstFolder, Core::$FORBIDDEN_URLS) && !is_dir($path)) {
                ORM::saveData($table, $page);
                if (empty($id)) {
                    $out['text'] = Word::get('admin', 'page_created');
                    $out['status'] = true;
                } else {
                    $out['text'] = Word::get('admin', 'page_modified');
                    $out['status'] = false;
                }
                $this->updatePagePositions($page);
            } else {
                $out['text'] = Word::get('admin','page_url_forbidden');
                $out['status'] = false;
            }
        } else {
            $out['text'] = Word::get('admin','page_url_not_unique');
            $out['status'] = false;
        }
        return $out;
    }
}