<?php
/**
 * @param $params [module=>'module', term=>'term']
 * @param $template
 * @return string|string[]
 * @throws FwException if $params[module] or $params[term] not specified
 */
function smarty_function_word($params, $template)
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
