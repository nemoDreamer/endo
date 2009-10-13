<?php

Globe::load('Setting', 'model');

class Email {

  var $from, $to, $subject, $message;
  var $parts = array();

  function __construct($from, $to, $include_admin=false)
  {
    $admin = Setting::Get('admin', 'email', true);
    if ($include_admin) {
      $to = $admin.wrap($to,', ');
    }
    $this->from = $from ? $from : $admin;
    $this->to = $to ? $to : $admin;
  }

  function send_data($data, $subject='[Data Send]')
  {
    return $this->send($this->build_message($data), $subject, true);
  }

  function send($message, $subject='[Send]', $is_html = false)
  {
    $this->set_part('message', $message);
    $this->message = $this->get_part('prepends').$this->get_part('message').$this->get_part('appends');
    $this->subject = $subject;
    $this->build_headers(true);

    return mail($this->to, $this->subject, $this->message, $this->headers);
  }

  private function build_headers($is_html = true)
  {
    $this->headers = '';

    // html?
    if ($is_html) {
      $this->headers  = 'MIME-Version: 1.0' . "\r\n";
      $this->headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    }

    // $this->headers .= "To: $this->to\r\n"; // handled by mail()...
    $this->headers .= "From: $this->from\r\n";
    $this->headers .= "Reply-To: $this->from\r\n";

    $this->headers .= 'X-Mailer: PHP/'.phpversion();
  }

  function build_message($data)
  {
    $message = '<ul>'."\n";
    foreach ($data as $key => $value) {
      $key = ucwords(str_replace('_', ' ', $key));
      $value = !is_array($value) ? htmlentities($value) : $this->build_message($value);
      $message .= "\t<li><strong>$key:</strong> $value</li>\n";
    }
    $message .= '</ul>';
    return $message;
  }

  function set_part($part, $string='')
  {
    if (!array_key_exists($part, $this->parts)) {
      $this->parts[$part] = array();
    }
    array_push($this->parts[$part], $string);
  }

  function get_part($part)
  {
    return implode("\n", array_get($this->parts, $part, array()))."\n";
  }

  function prepend($string='') { $this->set_part('prepends', $string); }
  function append($string='') { $this->set_part('appends', $string); }

}

/*
  UPDATE `setting` SET  `variable` = 'admin', `group` = 'emails', `label` = 'Administrator Emails' WHERE `setting`.`group` = 'admin' LIMIT 1 ;
*/

?>