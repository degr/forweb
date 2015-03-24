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
                    'title' => 'Page link'
                );
                $form['fields']['delete'] = array(
                    'tag' => 'html',
                    'value' => '<a href="#" onclick="Admin.deletePage(event);">To delete page, press here</a>',
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
                if($parent->getId() == 0){
                    $deleteText = "You can't delete home page.";
                } else {
                    $tail = "WHERE pages.parent = '".$page->getId()."'";

                    $childs = $pageService->loadAll($tail);
                    if(empty($childs[$pageService->getTable()->getName()])){
                        if(ORM::delete($table, $page)) {
                            $page = null;
                            $deleteText = "Page was deleted.";
                            $parentLink = $pageService->getPagePath($parent);
                        }else{
                            $deleteText = "Something goes wrong. We are sorry...";
                        }
                    } else {
                        $deleteText = "You can't delete page width child pages.";
                    }
                }
            } else {
                $url = $page->getUrl();
                if($url != '') {
                    $tail = "WHERE pages.parent='".$page->getParent()."' AND pages.url='".$page->getUrl()."'";
                    $id = $page->getId();
                    if(!empty($id)){
                        $tail .= " AND pages.id != '".$id."'";
                        if($id == 1){
                            $page->setUrl('home');
                            $page->setParent(0);
                        }
                    }

                    $check = $pageService->loadAll($tail);
                    if(empty($check[$pageService->getTable()->getName()])) {
                        $id = $page->getId();
                        ORM::saveData($table, $page);
                        if(empty($id)){
                            $saveText = 'Page created.';
                            $savedStatus = true;
                        } else {
                            $saveText = 'Page data modified.';
                            $savedStatus = false;
                        }
                    } else {
                        $saveText = 'Table with such url is already exist. Change url, or parent page.';
                        $savedStatus = false;
                    }
                }else {
                    $saveText = 'Page url can\'t be empty.';
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