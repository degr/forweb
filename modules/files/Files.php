<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 14:13
 */
class Files extends Module{
    /**
     * Get module ajax handlers
     * @return ModuleAjaxHandler[]
     */
    public function getAjaxHandlers()
    {
        if($this->ajaxHandlers == null) {
            $this->ajaxHandlers = array(
                'getAjaxUserMedia' => new ModuleAjaxHandler('getAjaxUserMedia', ModuleAjaxHandler::TEXT),
                'updateTextFile' => new ModuleAjaxHandler('updateTextFile', ModuleAjaxHandler::TEXT),
                'showFileContent' => new ModuleAjaxHandler('showFileContent', ModuleAjaxHandler::TEXT),
                'adminDeleteFile' => new ModuleAjaxHandler('adminDeleteFile', ModuleAjaxHandler::TEXT),
                'adminNewFile' => new ModuleAjaxHandler('adminNewFile', ModuleAjaxHandler::TEXT),
                'adminUploadFile' => new ModuleAjaxHandler('adminUploadFile', ModuleAjaxHandler::JSON)//@TODO fix this. In past it was FormHandler
            );
        }
        return $this->ajaxHandlers;
    }

    /**
     * download file from url to selected location
     * @param $url string
     * @param $path string
     * @return string file path
     */
    public static function download($url, $path){
        $fs = new FilesSystem("");
        $fs->createFolder(dirname($path));
        $downloader = new FilesDownloader($url);
        return $downloader->download($path);
    }

    /**
     * Extract archive, and put all content into selected folder
     * @param $archive string - path to archive
     * @param $folder - path to folder
     * @return boolean;
     */
    public static function extract($archive, $folder)
    {
        $zip = new ZipArchive;
        @$zip->open($archive);
        @$zip->extractTo($folder);
        $out = ZipArchive::ER_OK === $zip->status;
        @$zip->close();
        return $out;
    }

    public function getAjaxUserMedia(){
        Access::denied("can_modify_files");
        $provider = new FilesAdmin();
        return $provider->getAjaxUserMedia();
    }
    public function showFileContent(){
        Access::denied("can_modify_files");
        $provider = new FilesAdmin();
        return $provider->showFileContent();
    }
    public function updateTextFile(){
        Access::denied("can_modify_files");
        $provider = new FilesAdmin();
        return $provider->updateTextFile();
    }

    public function adminUploadFile(){
        Access::denied('can_modify_files');
        $provider = new FilesAdmin();
        $provider->adminUploadFile($handler);//todo fix from from handler to ajax handler
    }
    public function adminDeleteFile(){
        Access::denied('can_modify_files');
        $provider = new FilesAdmin();
        return $provider->adminDeleteFile();
    }
    public function adminNewFile(){
        Access::denied('can_modify_files');
        $provider = new FilesAdmin();
        return $provider->adminNewFile();
    }

    /**
     * Get module event handlers
     * @return ModuleEventHandler[]
     */
    public function getEventHandlers()
    {
        // TODO: Implement getEventHandlers() method.
    }
}