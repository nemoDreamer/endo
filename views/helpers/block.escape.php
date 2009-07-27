<?php

function smarty_block_escape($params, $content, &$smarty)
{
  require_once $smarty->_get_plugin_filepath('modifier','escape');

  $esc_type = 'javascript';

  foreach ($params as $_key => $_val) {
    switch ($_key) {
      case 'esc_type':
        $$_key = (string)$_val;
        break;
    }
  }

  return smarty_modifier_escape($content, $esc_type);
}

?>