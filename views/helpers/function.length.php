<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {length} function plugin
 *
 * Type:     function<br>
 * Name:     length<br>
 * Purpose:  return length of array<br>
 * @author   Philip Blyth <philip dot blyth at gmail dot com>
 * @param array
 * @param Smarty
 * @return integer
 */
function smarty_function_length($params, &$smarty)
{
    // be sure parameter is present
    if (empty($params['array'])) {
        $smarty->trigger_error("length: missing array parameter");
        return;
    }

    return count($params['array']);
}

/* vim: set expandtab: */

?>
