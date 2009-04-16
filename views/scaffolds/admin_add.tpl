<h2>Add a <em>{$url.modelName}</em></h2>

<!--
  TODO replace $url (assigned at view and layout level... by a Registered Object)
-->

<form action="/{$ADMIN_ROUTE}/{$url.controller}/add" method="post" accept-charset="utf-8">

  {include file="`$url.controller`/admin_form.tpl"}
  <div class="submit">
    <input type="submit" value="&raquo; Create!">
    or {admin_link controller=`$url.controller` text='Cancel' class='cancel'}
  </div>
</form>
