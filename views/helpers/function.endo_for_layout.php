<?php

function smarty_function_endo_for_layout($params, $content, &$smarty)
{
  foreach ($params as $_key => $_value) {
    switch ($_key) {
      case 'variable':
        $$_key = $_value;
        break;

      default:
        $smarty->trigger_error("endo_for_layout: unknown attribute '$_key'");
        break;
    }
  }

  return Globe::for_layout($assign);
}

?>