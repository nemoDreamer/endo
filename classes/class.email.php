<?php

Globe::load('Setting', 'model');

class Email {

  var $from, $to, $subject, $message;

  function __construct($from, $to)
  {
    $this->from = $from ? $from : Setting::Get('email', 'admin', true);
    $this->to = $to ? $to : Setting::Get('email', 'admin', true);

  }

  function send_data($data, $subject='[Data Send]')
  {
    $message = '<ul>'."\n";
    foreach ($data as $key => $value) {
      $key = ucwords(str_replace('_', ' ', $key));
      $message .= "\t<li><strong>$key:</strong> <pre>$value</pre></li>\n";
    }
    $message .= '</ul>';

    return $this->send($message, $subject, true);
  }

  function send($message, $subject='[Send]', $is_html = false)
  {
    $this->message = $message;
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

}

?>