<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 14.03.2015
 * Time: 20:21
 */
class Page_Admin_Page{

    /**
     * Prepare JSON object for page form
     * for amdin panel
     * @param $page PersistPages
     * @return array JSON
     * @throws Exception
     */
    public function getContent($page){
        $layout = UI::LAYOUT_TABLE;

        /* @var $pageService Page_Service */
        $pageService = Core::getModule("Page_Service");
        $table = ORM::getTable($pageService->getTable()->getName());

        $form = UI::getFormForTable($table, null, $layout);
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
        if($page != null && $page->getId() == 1) {
            unset($form['fields']['parent']);
        }

        $form['fields']['template']['tag'] = UI::TAG_SELECT;
        $query = "SELECT id, name FROM templates";
        $form['fields']['template']['options'] = DB::getAssoc($query, 'id', 'name');

        $form['fields']['submit'] = UI::getSubmitButton();
        $form['fields']['submit']['layout'] = $layout;

        if($page != null){
            $vars = $page->toArray();
            foreach($vars as $key => $value){
                if(!empty($form['fields'][$key])){
                    $form['fields'][$key]['value'] = $value;
                }
            }
        }

        if($page != null) {

            $id = $page->getId();

            if(!empty($id)){
                $link = $pageService->getPagePath($page);
                $form['fields']['link'] = array(
                    'tag' => 'html',
                    'value' => '<a href="'.Config::get("url").$link.'">'.$link.'</a>',
                    'layout' => $layout,
                    'title' => Word::get('admin', 'page_form_field_url')
                );
                $form['fields']['delete'] = array(
                    'tag' => 'html',
                    'value' => '<a href="#" onclick="Admin.deletePage(event);">'.Word::get('admin', 'delete_page').'</a>',
                    'layout' => $layout
                );
            }
        }

        return $form;
    }

    public function editPage()
    {
        /* @var $pageService Page_Service*/
        $pageService = Core::getModule("Page_Service");
        if(!empty($_POST['ajax_key']) && $_POST['ajax_key'] === 'pageedit'){
            if(!empty($_POST['id'])){
                $page = $pageService->load($_POST['id']);
            } else {
                $page = new PersistPages();
            }
            $table = $pageService->getTable();
            foreach($_POST as $key =>$value){
                $method = "set".ucfirst($key);
                if(method_exists($page, $method)){
                    $page->$method($value);
                }
            }
            if(!empty($_POST['deletePage'])){
                /* @var $parent PersistPages */
                $parent = $page->getParentPage();
                if($parent == null){
                    $deleteText = Word::get('admin', 'home_page_delete');
                } else {
                    $deleteFilter = new ORM_Query_CustomFilter('','',"pages.parent = '".$page->getId()."'", true);

                    $childs = $pageService->loadAll($deleteFilter);
                    if(empty($childs[$pageService->getTable()->getName()])){
                        if(ORM::delete($table, $page)) {
                            $page = null;
                            $deleteText = "Page was deleted.";
                            $parentLink = $pageService->getPagePath($parent);
                        }else{
                            $deleteText = Word::get('admin', 'unknown_error');
                        }
                    } else {
                        $deleteText = Word::get('admin', 'page_contain_children');
                    }
                }
            } else {
                $url = $page->getUrl();

                if($url != '') {
                    $filters = array(
                        new ORM_Query_CustomFilter('', '', " pages.parent='".$page->getParent()."' AND pages.url='".$page->getUrl()."'", true)
                    );

                    $id = $page->getId();
                    if(!empty($id)){
                        $filters[] = new ORM_Query_CustomFilter('','', " pages.id != '".$id."'", true);
                        if($id == 1){
                            $page->setUrl('home');
                            $page->setParent(0);
                        }
                    }

                    $check = $pageService->loadOneWithFilters($filters);
                    if($check === null) {
                        ORM::saveData($table, $page);
                        if(empty($id)){
                            $saveText = Word::get('admin', 'page_created');
                            $savedStatus = true;
                        } else {
                            $saveText = Word::get('admin', 'page_modified');
                            $savedStatus = false;
                        }
                    } else {
                        $saveText = Word::get('admin','page_url_not_unique');
                        $savedStatus = false;
                    }
                }else {
                    $saveText = Word::get('admin', 'page_url_empty');
                    $savedStatus = false;
                }
            }


        }elseif(isset($_POST['href'])){

            $dispatcher = new Page_Dispatcher($_POST['href']);
            $dispatcher->handleRequest();
            $params = $dispatcher->getParams();
            $page = $pageService->findPage($params);

        }else{
            $page = null;
        }
        /* @var $pageModule Page */
        $pageModule = Core::getModule('Page');
        $form = $pageModule->getPageForm($page);

        if(!empty($deleteText)){
            $form['text'] = $deleteText;
            if(!empty($parentLink)){
                $form['parentLink'] = Config::get('url').$parentLink;
            }
        }
        if(!empty($saveText)){
            $form['text'] = $saveText;
            $savedStatus = empty($savedStatus) ? "" : $savedStatus;
            $form['savedStatus'] = $savedStatus;
        }
        return $form;
    }
}