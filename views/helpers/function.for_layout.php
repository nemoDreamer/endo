<?php

function smarty_function_for_layout($params, $content, &$smarty)
{
  foreach ($params as $_key => $_value) {
    switch ($_key) {
      case 'variable':
        $$_key = $_value;
        break;

      default:
        $smarty->trigger_error("for_layout: unknown attribute '$_key'");
        break;
    }
  }

  return Globe::ForLayout($assign);
}

?>