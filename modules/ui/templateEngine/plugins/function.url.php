<?php
/**
 * @param $params [module=>'module', term=>'term']
 * @param $template
 * @return string|string[]
 * @throws FwException if $params[module] or $params[term] not specified
 */
function smarty_function_url($params, $template)
{
    $plugin = new UiPluginPageurl();
    return $plugin->execute($params);
}
