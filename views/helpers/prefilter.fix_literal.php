<?php
function smarty_prefilter_fix_literal($tpl_source, &$smarty)
{
  return preg_replace('/<(\/?)literal>/Ui', '{\\1literal}', $tpl_source);
}
?>