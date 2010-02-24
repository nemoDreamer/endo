<?php

function smarty_modifier_more($string, $method='excerpt', $href=false, $label='more', $ellipsis='[...] ')
{
  $more_tag = '[--more--]';
  $more_link = '<span class="ellipsis">%s<a href="%s">&rarr; <span>%s</span></a></span>';

  switch ($method) {
    case 'excerpt':
      $pos = strpos($string, $more_tag);
      if ($pos!==false) {
        $string = substr($string, 0, strpos($string, $more_tag));
        if ($href) {
          $string .= sprintf($more_link, $ellipsis, $href, $label);
        }
      }
      break;
  }

  return str_replace($more_tag, '', $string);
}

?>