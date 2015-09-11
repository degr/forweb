<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 08.04.2015
 * Time: 21:30
 */
class FilesAdmin{

    const JS_FILES_OBJECT = 'FilesForm';
    const JS_FILES_NEWFILE = 'newFile';
    const JS_FILES_NEWFOLDER = 'newFolder';
    const JS_FILES_UPLOAD = 'upload';



    public function getAjaxUserMedia()
    {
        $mainPath = $this->filterRoot($_POST['type']);
        if(empty($mainPath)) {
            $mainPath = 'img';
        }

        $sidebar = $this->getUiSidebar($mainPath);
        $content = $this->getUiContent($mainPath);
        $header = $this->getUiHeader();
        $ui = new UI();
        $ui->setLayout('files/admin/file.manager.tpl');
        $ui->addVariable('sidebar', $sidebar);
        $ui->addVariable('content', $content);
        $ui->addVariable('header', $header);
        return $ui->process();
    }


    private function getUiSidebar($mainPath){
        $ui = new UI();
        $fileSystem = new FilesSystem($mainPath);
        $ui->setLayout(FilesSystem::UI_FILES_LIST);
        if(!empty($_POST['path'])){
            $fileSystem->setRelativePath($_POST['path']);
        }
        $ui->addVariable('deleteFile', 'FilesForm.deleteFile');
        $ui->addVariable('path', $mainPath.'/'.$_POST['path']);
        $ui->addVariable(FilesSystem::UI_CONTENT, $fileSystem->getFolderContent());
        return $ui->process();
    }
    private function getUiContent($mainPath){
        $ui = new UI();
        $file = $mainPath.'/'.$_POST['path'];
        if($mainPath == 'img') {
            $key = 'image';
        } else {
            $key = 'text';
        }
        $ui->setLayout('files/admin/'.$key.'.tpl');
        if(!empty($_POST['path']) && is_file($file)) {
            $ui->addVariable($key, file_get_contents($file));
        }
        return $ui->process();
    }
    private function getUiHeader()
    {
        $ui = new UI();
        $ui->setLayout(FilesSystem::UI_MANAGER);
        $ui->addVariable('object', self::JS_FILES_OBJECT);
        $ui->addVariable('newFolder', self::JS_FILES_NEWFOLDER);
        $ui->addVariable('newFile', self::JS_FILES_NEWFILE);
        $ui->addVariable('upload', self::JS_FILES_UPLOAD);
        $ui->addVariable('uploadTarget', CoreConfig::getUrl()."form/files/adminUploadFile");
        $ui->addVariable('uploadIframeId', 'admin_upload_iframe');
        $ui->addVariable('uploadIframeOnload', 'onUpload');

        return $ui->process();
    }


    public function showFileContent()
    {
        $mainPath = $this->filterRoot($_POST['type']);
        if(empty($mainPath)) {
            return '';
        }

        $path = preg_replace('/^\//', '', $_POST['path']);
        $parts = explode('/', $path);
        array_shift($parts);
        $path = $mainPath."/".implode('/', $parts);
        if(is_file($path)) {
            return file_get_contents($path);
        } else {
            return '';
        }
    }

    public function updateTextFile()
    {
        $content = $_POST['content'];
        $path = preg_replace("/^\//", '', $_POST['path']);
        $parts = explode('/', $path);
        $main = $this->filterRoot(array_shift($parts));
        if(empty($main) || $main == 'img') {
            return Word::get("files", "dir_write_forbidden");
        }
        file_put_contents($main.'/'.implode('/', $parts), $content);
        return Word::get('files', 'file_save');
    }

    public function adminUploadFile($formHandler)
    {
        $key = self::JS_FILES_OBJECT."_".self::JS_FILES_UPLOAD;
        $path = preg_replace('/\/$/', '', $_POST['path']);
        if(empty($path)) {
            echo 0;
            exit;
        }
        $parts = explode('/', $path);
        $root = $this->filterRoot(array_shift($parts));
        if(empty($root)){
            echo 0;
            exit;
        }
        if(empty($_FILES[$key])){
            echo 0;
            exit;
        }
        if($_FILES[$key]['error'] == 0) {
            move_uploaded_file($_FILES[$key]['tmp_name'], $path."/".$_FILES[$key]['name']);
            echo 1;
        } else {
            echo 0;
        }
        exit;
    }


    private function filterRoot($root){
        switch($root) {
            case 'js':
            case 'css':
            case 'img':
                return $root;
            case 'templates':
                return UI::TEMPLATES_DIR;
            default:
                return '';
        }
    }

    public function adminDeleteFile()
    {
        if(empty($_POST['path'])) {
            return Word::get("admin", 'file_path_empty', true);
        }
        $path = preg_replace('/^\/|\/$/', '', $_POST['path']);
        $parts = explode('/', $path);
        $root = $this->filterRoot(array_shift($parts));
        if(empty($root)) {
            return Word::get("admin", 'file_path_empty', true);
        }
        $fileSystem = new FilesSystem($root);
        $file = array_pop($parts);
        if(count($parts) > 0) {
            $fileSystem->setRelativePath(implode('/', $parts));
        }
        $out = $fileSystem->deleteFile($file);
        if($out) {
            return Word::get('admin', 'file_deleted', true);
        } else {
            return Word::get('admin', 'file_not_deleted', true);
        }
    }

    public function adminNewFile()
    {
        $path = $_POST['path'];
        $parts = explode('/',$path);
        $root = $this->filterRoot(array_shift($parts));
        if(empty($root)) {
            return Word::get("admin", 'file_path_empty', true);
        }
        $name = $_POST['name'];
        if(empty($name)) {
            return Word::get("admin", 'file_path_empty', true);
        }
        $fs = new FilesSystem($root);
        $fs->setRelativePath(implode('/', $parts));
        $type = $_POST['type'];
        switch($type) {
            case 'file':
                $out = $fs->createFile($name, '');
                break;
            case 'folder':
                $out = $fs->createFolder($name);
                break;
            default:
                return Word::get("admin", 'file_type_undefined', true);
        }
        if($out) {
            return Word::get('admin', 'new_file_created');
        } else {
            return Word::get('admin', 'new_file_creation_fail');
        }
    }
}