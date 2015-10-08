<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 9/15/2015
 * Time: 4:03 PM
 */
class UiPluginWord{

    /**
     * Execute plugin logic
     * @param $params array
     * @return mixed
     * @throws FwException
     */
    public function execute($params)
    {
        if(empty($params['module'])) {
            throw new FwException("Module must be specified for 'smarty_function_word' plugin.");
        }
        if(empty($params['term'])) {
            throw new FwException("Term must be specified for 'smarty_function_word' plugin.");
        }
        $word = Word::get($params['module'], $params['term']);

        return $word;
    }
}