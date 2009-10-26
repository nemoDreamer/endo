<?php

function smarty_modifier_plain($string)
{
    return preg_replace('/<.*>/Ums', '', $string);
}

?>