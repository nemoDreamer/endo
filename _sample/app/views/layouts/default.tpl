<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
  <title>My Awesome App | {$title_for_layout}</title>

  <meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <meta http-equiv="X-UA-Compatible" content="chrome=1">

  {if isset($head_for_layout)}{$head_for_layout}{/if}

  {if isset($script_for_layout)}
  <script type="text/javascript" charset="utf-8">
  {$script_for_layout}
  </script>
  {/if}
</head>
<body id="{$id}">

  <div id="nav">
    <ul>
      {foreach from=$nav_for_layout key=label item=link}
        <li{if $label==$nav_active_for_layout} class="active"{/if}><a href="{$link}">{$label}</a></li>
      {/foreach}
    </ul>
  </div>

  <div id="content">
    {if $has_errors}
      <!--
        TODO rename class to 'messages' (also rename PHP class) or create flash class, with session support.
      -->
      <div id="errors" class="errors">
        {errors key=notice}
        {errors key=fatal}
        {errors}
      </div>
    {/if}

    {$content}
  </div>

  <div id="footer">
    <p>&copy; {$time_for_layout|date_format:'%Y'} My Awesome App, Inc.</p>

    <p>{$footer_for_layout}</p>

    <ul>
      {foreach from=$nav_for_layout key=label item=link name=nav}
        <li{if $smarty.foreach.nav.index==0} class="first"{/if}>{if $smarty.foreach.nav.index!=0}|{/if}<a href="{$link}">{$label}</a></li>
      {/foreach}
      <li class="last">|<a href="/pages/terms">Terms of Use</a></li>
    </ul>
  </div>

  {if DEBUG}
    <div id="debug_dump">
      {if isset($debug_for_layout)}
        <pre class="debug">{$debug_for_layout}</pre>
      {/if}
      {$debug_dump}
    </div>
  {/if}

  {if isset($body_for_layout)}{$body_for_layout}{/if}
</body>
</html>
