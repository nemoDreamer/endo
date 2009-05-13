<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {call} function plugin
 *
 * Type:     function<br>
 * Name:     call<br>
 * Purpose:  call function on object<br>
 * @author   Philip Blyth <philip dot blyth at gmail dot com>
 * @param array
 * @param Smarty
 * @return integer
 */
function smarty_function_call($params, &$smarty)
{
    // be sure parameters are present
    if (empty($params['o'])) {
        $smarty->trigger_error("call: missing o (object) parameter");
        return;
    }
    if (empty($params['f'])) {
        $smarty->trigger_error("call: missing f (function) parameter");
        return;
    }

    return $params['o']->$params['f']();
}

/* vim: set expandtab: */

?>
