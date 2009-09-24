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

function d ($str, $print=TRUE, $class=NULL, $element='p', $encode=TRUE)
{
	if (DEBUG || get_debug()) {
    if ($class != NULL) $class = " $class";
  	$str = "<$element class='debug$class'>".($encode ? htmlentities($str) : $str)."</$element>";
  	if ($print) echo $str;
  	return $str;
	} else {
	  return NULL;
	}
}

function d_pre ($str, $print=TRUE, $class=NULL, $encode=TRUE) { return d($str, $print, $class, 'pre', $encode); }
function d_arr ($array, $print=TRUE, $class=NULL, $encode=TRUE) { return d_pre(print_r($array, TRUE), $print, $class, $encode); }

function d_err ($str, $print=FALSE, $element='span', $encode=FALSE) { return d($str, $print, 'error', $element, $encode); }
function d_ ($str, $print=FALSE, $element='span', $encode=FALSE) { return d($str, $print, 'success', $element, $encode); }

function get_debug()
{
  if(array_key_exists('debug', $_REQUEST)) {
    if ($_REQUEST['debug']=='false') {
      unset($_SESSION['debug']);
      return false;
    } else {
      return $_SESSION['debug'] = true;
    }
  } elseif(isset($_SESSION) && array_key_exists('debug', $_SESSION)) {
    return true;
  }
  return false;
}

?>