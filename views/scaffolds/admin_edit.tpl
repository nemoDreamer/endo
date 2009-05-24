<h2>Edit <em>&lsquo;{$item->display_field('name')}&rsquo;</em></h2>

<!--
  TODO replace $url (assigned at view and layout level... by a Registered Object)
-->

<form action="/{$ADMIN_ROUTE}/{$url.controller}/edit/{$item->id}{set_gets}" method="post" accept-charset="utf-8" enctype="multipart/form-data">

  {admin_relations object=`$item` wrap=true}

  <input type="hidden" name="id" value="{$item->id}" id="id">
  {include file="`$url.controller`/admin_form.tpl"}
  <div class="submit">
    <input type="submit" value="&raquo; Update!">
    or {admin_link controller=`$url.controller` text='Cancel' class='cancel' set_gets=true}
  </div>
</form>
