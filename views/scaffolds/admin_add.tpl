<h2>Add a <em>{$url.modelName}</em></h2>

<form action="/{$ADMIN_ROUTE}/{$url.controller}/add{set_gets}" method="post" accept-charset="utf-8" enctype="multipart/form-data">

  {include file="`$this->name`/admin_form.tpl"}
  <div class="submit clearfix">
    <input type="submit" value="&raquo; Create!">
    or {admin_link controller=`$url.controller` text='Cancel' class='cancel' set_gets=true}
  </div>
</form>
