<?php

function smarty_function_debug($params=array(), &$smarty)
{
  foreach ($params as $_key => $_value) {
    switch ($_key) {
      case 'var':
        return d_arr($_value, false);
        break;
      default:
        # code...
        break;
    }
  }
}

?>