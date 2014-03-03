<?php

/**
 * AppBootstrap
 * Whatever you need to add...
 *
 * @author Philip Blyth
 */

// --------------------------------------------------
// ERRORS
// --------------------------------------------------

if (!LOCAL) {
  ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING);
}

// --------------------------------------------------
// FUNCTIONS
// --------------------------------------------------

function build_email($listing)
{
  $data = (array) $listing;
  if ($email = array_get($data, 'email')) {
    $name = trim("{$data['doctor_title']} {$data['doctor_first_name']} {$data['doctor_last_name']}").wrap($data['doctor_license_type'], ', ');
    return "$name <$email>";
  } else {
    return null;
  }
}




?>
