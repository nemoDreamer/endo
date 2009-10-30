<?php

function smarty_block_cdata($params, $content, &$smarty)
{
  return "<![CDATA[".$content."]]>";
}

?>