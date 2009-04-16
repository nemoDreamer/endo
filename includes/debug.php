<?php

/**
 * debug.php
 * Collection of debug functions
 *
 * @author Philip Blyth
 */

require_once(PACKAGES_ROOT.'debuglib.php');

// --------------------------------------------------
// UTILITIES
// --------------------------------------------------

function is_executing ($__FILE__)
{
	return (realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'])==realpath($__FILE__));
}

function get_constants($key='user')
{
  $constants = get_defined_constants(true);
  return $constants[$key];
}

// --------------------------------------------------
// OUTPUT
// --------------------------------------------------

function d ($str, $print=TRUE, $class=NULL, $element='p')
{
	if (DEBUG) {
    if ($class != NULL) $class = " $class";
  	$str = "<$element class='debug$class'>$str</$element>";
  	if ($print) echo $str;
  	return $str;
	} else {
	  return NULL;
	}
}

function d_pre ($str, $print=TRUE, $class=NULL) { return d($str, $print, $class, 'pre'); }
function d_arr ($array, $print=TRUE, $class=NULL) { return d_pre(print_r($array, TRUE), $print, $class); }

function d_err ($str, $print=FALSE, $element='span') { return d($str, $print, 'error', $element); }
function d_ ($str, $print=FALSE, $element='span') { return d($str, $print, 'success', $element); }

?>