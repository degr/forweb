<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 10/28/2015
 * Time: 4:21 PM
 */
class FilesDownloader
{
    private $url;
    private $length = 8192;
    private $pos = 0;
    private $timeout = 60;

    public function download($local)
    {
        if ($this->isSupportMultyPartDownload()) {
            return $this->runPartial($local);
        } else {
            return $this->runNormal($local);
        }
    }
    
    public function __construct($url)
    {
        $this->url = $url;
    }

    public function setLength($length)
    {
        $this->length = $length;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function setPos($pos)
    {
        $this->pos = $pos;
    }

    public function getPos()
    {
        return $this->pos;
    }
    public function getUrl()
    {
        return $this->url;
    }

    private function runNormal($local)
    {
        $in = fopen($this->getUrl(), "r");
        $out = fopen($local, 'w');
        while (($pos = ftell($in)) <= $this->pos) {
            $n = ($pos + $this->length) > $this->length ? $this->length : $this->pos;
            fread($in, $n);
        }
        $this->setPos(stream_copy_to_stream($in, $out));
        return $in !== false && $out !== false;
    }

    private function runPartial($local)
    {
        $i = $this->getPos();
        $fp = fopen($local, 'w');
        fseek($fp, $this->getPos());
        
        $ch = curl_init();
        $this->setOptions($ch);
        $out = true;
        while (true) {
            curl_setopt($ch, CURLOPT_RANGE, sprintf("%d-%d", $i, ($i + $this->length)));
            $result = $this->getPart($ch);
            $data = $result['data'];
            $i += strlen($data);
            fwrite($fp, $data);
            $this->pos = $i;
            if($result['code'] === 200) {
                $out = true;
                break;
            } else if($result['code'] === 206) {
                $out = true;
                if(empty($data)) {
                    break;
                }
            } else {
                $out = false;
                break;
            }
        }
        curl_close($ch);
        fclose($fp);
        return $out;
    }

    private function getPart($ch)
    {
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array('data' => $result, 'code' => $code);
    }
    
    private function isSupportMultyPartDownload(){
        $ch = curl_init();
        $this->setOptions($ch);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code != 206;
    }
    
    private function setOptions($ch){
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
    }
}