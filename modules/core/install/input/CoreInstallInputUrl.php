<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 9/4/2015
 * Time: 9:34 AM
 */
class CoreInstallInputUrl implements ModuleInput{

    const APPLICATION_URL = 'applicationUrl';
    /**
     * Return input identifier for input.
     * Each identifier must be unique for each module
     * @return int
     */
    public function getIdentifier()
    {
        return self::APPLICATION_URL;
    }

    /**
     * Process user response
     * @return boolean
     */
    public function process($userInput)
    {
        $query = "select * from config where module = 'core' and name='url'";
        $row = DB::getRow($query);
        if(empty($userInput)) {
            return;
        }
        $userInput = preg_replace('/\/$/', '', $userInput).'/';
        
        $userInput = DB::escape($userInput);
        if(empty($row)) {
            $query = "insert into config (module, name, value) values ('core', 'url', '{$userInput}')";
        } else {
            $query = "update config set value = '{$userInput}' where module = 'core' and name='url'";
        }
        DB::query($query);
    }

    /**
     * Ask question for user on module install
     * @return string
     */
    public function getQuestion()
    {
        return "Please, enter base application url in format\n".
            "http://yoursite.com/\n".
            "(with protocol, port (if it not equal to 80), and ending slash)";
    }
}