<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 08.04.2015
 * Time: 23:23
 */
class Files_System{
    protected $basePath;
    protected $relativePath;

    const UI_MANAGER = 'files/manager.tpl';
    const UI_FILES_LIST = 'files/list.tpl';
    const UI_CONTENT = 'content';

    public function __construct($basePath){
        $this->basePath =  preg_replace('/\/$/','', $basePath)."/";
    }

    public function setRelativePath($relativePath){
        if(is_file($this->basePath.$relativePath)) {
            $relativePath = str_replace(basename($relativePath), "", $relativePath);
        }
        $this->relativePath = $this->removeUpFolders($relativePath);
    }

    private function removeUpFolders($path)
    {
        $patterns = array('(\.)$', '(\/)$', '(\.\.\/)', '(\.\/)');
        $pattern = '/'.implode('|', $patterns).'/';
        while(preg_match($pattern, $path)) {
            $path = preg_replace($pattern, "", $path);
        }
        if(!empty($path)){
            $path .= '/';
        }
        return $path;
    }

    public function getFolderContent(){
        $fileNames = glob($this->basePath.$this->relativePath."*");
        $out = array();
        if(!empty($this->relativePath)){
            $parts = explode('/', $this->relativePath);
            array_pop($parts);
            array_pop($parts);
            $out[] = array(
                'path' => $this->basePath.implode('/',$parts),
                'type' => 'upfolder',
                'name' => '..'
            );
        }
        $files = array();
        $folders = array();
        foreach($fileNames as $filename){
            $item = array(
                'path' => $filename,
                'name' => basename($filename)
            );
            if(is_file($filename)) {
                $item['type'] = 'file';
                $files[] = $item;
            } else {
                $item['type'] = 'folder';
                $folders[] = $item;
            }
        }
        return array_merge($out, $folders, $files);
    }

    public function deleteFile($file)
    {
        $file = $this->removeUpFolders($file);
        $file = self::removeFirstAndLastSlashes($file);
        $path = $this->basePath.$this->relativePath.$file;
        if(!is_file($path)) {
            if(!is_dir($path)) {
                return false;
            } else {
                return $this->removeFolder($path);
            }
        } else {
            return unlink($path);
        }
    }

    private function removeFolder($path) {
        $path = $this->removeLastSlash($path);
        $files = glob($path."/*");
        foreach($files as $file) {
            if(is_file($file)) {
                $out = unlink($file);
                if(!$out) {
                    return false;
                }
            } else {
                $out = $this->removeFolder($file);
                if(!$out) {
                    return false;
                }
            }
        }
        return rmdir($path);
    }

    public static function removeFirstSlash($path){
        while(strpos($path, '/') === 0) {
            $path = preg_replace('/^\//', '', $path);
        }
        return $path;
    }
    public static function removeLastSlash($path){
        while(strpos($path, '/') === strlen($path) - 1) {
            $path = preg_replace('/\/$/', '', $path);
        }
        return $path;
    }
    public static function removeFirstAndLastSlashes($path){
        $path = self::removeFirstSlash($path);
        return self::removeLastSlash($path);
    }

    public function createFile($name, $content)
    {
        $file = $this->removeUpFolders($name);
        $file = self::removeFirstAndLastSlashes($file);
        $path = $this->basePath.$this->relativePath.$file;
        $foldersPath = explode('/', $path);
        array_pop($name);
        $this->createFoldersPath($foldersPath);
        return file_put_contents($path, $content);
    }

    public function createFolder($name)
    {
        $this->createFoldersPath($this->basePath.$this->relativePath);
        $file = $this->removeUpFolders($name);
        $file = self::removeFirstAndLastSlashes($file);
        $path = $this->basePath.$this->relativePath.$file;
        mkdir($path);
    }

    private function createFoldersPath($path){
        mkdir($path, 0777, true);
    }

}