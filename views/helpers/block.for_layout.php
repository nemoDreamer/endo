<?php

function smarty_block_for_layout($params, $content, &$smarty)
{
  foreach ($params as $_key => $_value) {
    switch ($_key) {
      case 'assign':
        $$_key = $_value;
        break;

      default:
        $smarty->trigger_error("for_layout: unknown attribute '$_key'");
        break;
    }
  }

  Globe::for_layout($assign, $content);
}

?>