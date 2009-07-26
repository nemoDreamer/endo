<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<title>{$sitename_for_layout} Admin | {$title_for_layout}</title>
<link rel="stylesheet" href="/stylesheets/reset.css" type="text/css">
<link rel="stylesheet" href="/stylesheets/admin.css" type="text/css">
<link rel="stylesheet" href="/stylesheets/debug.css" type="text/css">
<!--[if lte IE 6]>
  <link rel="stylesheet" href="/stylesheets/admin_iefix.css" type="text/css" media="screen" title="style" charset="utf-8">
  <literal>
  <script type="text/javascript" charset="utf-8">
    if(!Array.indexOf){
      Array.prototype.indexOf = function(obj){
       for(var i=0; i<this.length; i++){
        if(this[i]==obj){
         return i;
        }
       }
       return -1;
      }
    }
  </script>
  </literal>
<![endif]-->

<script src="/javascripts/jquery.tools.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/javascripts/jquery.form.js" type="text/javascript" charset="utf-8"></script>
<script src="/javascripts/jquery.taconite.js" type="text/javascript" charset="utf-8"></script>
<script src="/javascripts/jquery.livequery.js" type="text/javascript" charset="utf-8"></script>

<script src="/javascripts/my.prevalue.js" type="text/javascript" charset="utf-8"></script>
<script src="/javascripts/my.debug.js" type="text/javascript" charset="utf-8"></script>

<script src="/javascripts/my.admin.js" type="text/javascript" charset="utf-8"></script>
<script src="/javascripts/my.input_tags.js" type="text/javascript" charset="utf-8"></script>
<script src="/javascripts/my.input_hours.js" type="text/javascript" charset="utf-8"></script>
</head>
<body id="{$id}" class="{$url.action}">
<div id="container">
  <div id="header">
    <h1 id="title">{$sitename_for_layout} <small>| {$title_for_layout}</small></h1>
    <div id="nav">
      <ul>
        {foreach from=$nav_for_layout key=label item=link}
        <li{if $label==$nav_active_for_layout} class="active"{/if}><a href="{$link}">{$label}</a></li>
        {/foreach}
      </ul>
    </div>
  </div>
  <div id="main" class="clearafter">
    <div id="content">
      {if $has_errors}
      <!--
        TODO rename class to 'messages' (also rename PHP class)
      -->
      <div id="errors" class="errors">
        {endo_errors key=notice}
        {endo_errors key=fatal}
        {endo_errors}
      </div>
      {/if}
      <div id="content_inner">
        {$content}
      </div>
    </div>
    {if $sidebar_for_layout!=''}
    <div id="sidebar">
      <div id="sidebar_inner">
        <div id="tools"></div>
        {$sidebar_for_layout}
      </div>
    </div>
    {/if}
  </div>
  <div id="footer">
    <p>{$footer_for_layout}</p>
    <ul>
      {foreach from=$nav_for_layout key=label item=link name=nav}
      <li><a href="{$link}">{$label}</a>{if !$smarty.foreach.nav.last}|{/if}</li>
      {/foreach}
    </ul>
  </div>
  <div id="debug_dump">
    {$debug_dump}
  </div>
</div>
</body>
</html>