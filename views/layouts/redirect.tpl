<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<title>{$sitename_for_layout} Admin | {$title_for_layout}</title>
<link rel="stylesheet" href="/stylesheets/reset.css" type="text/css">
<link rel="stylesheet" href="/stylesheets/redirect.css" type="text/css">
<link rel="stylesheet" href="/stylesheets/debug.css" type="text/css">
<script src="/javascripts/jquery/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="/javascripts/my/my.debug.js" type="text/javascript" charset="utf-8"></script>
</head>
<body id="{$id}">
  <div id="wrapper">
    {if $has_errors}
      <div id="errors" class="errors">
        {endo_errors key=notice layout=false}
        {endo_errors key=fatal layout=false}
        {endo_errors layout=false}
      </div>
    {/if}
    <div id="content">
      <p>Waiting to redirect to <a href="{$redirect_for_layout}">{$redirect_for_layout}</a>...</p>
    </div>
    <div id="debug_dump">
      {$debug_dump}
    </div>
  </div>
</body>
</html>