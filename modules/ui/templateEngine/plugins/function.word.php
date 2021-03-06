<?php
/**
 * @param $params [module=>'module', term=>'term']
 * @param $template
 * @return string|string[]
 * @throws FwException if $params[module] or $params[term] not specified
 */
function smarty_function_word($params, $template)
{
    $plugin = new UiPluginWord();
    return $plugin->execute($params);
}
