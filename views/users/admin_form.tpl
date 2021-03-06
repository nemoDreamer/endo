<div class="input text">
  <label for="first_name">Name:</label>
  <input type="text" name="first_name" value="{$item->first_name}" id="first_name" pre="First" class="span-4">
  <input type="text" name="last_name" value="{$item->last_name}" id="last_name" pre="Last" class="span-6">
</div>
<div class="input text">
  <label for="email">Email:</label>
  <input type="text" name="email" value="{$item->email}" id="email" class="span-6">
</div>
<div class="input select">
  <label for="class">Level:</label>
  {html_options name=class options=$this->form_levels() selected=$item->class id=class class='span-4'}
</div>

{include file='app_users/admin_class_options.tpl'}

<div class="show_more" more_label="change Password?">
  <h3>Password</h3>
  <div class="input text">
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" class="span-4">
  </div>
  <div class="input text">
    <label for="password_confirm">Confirm:</label>
    <input type="password" name="password_confirm" id="password_confirm" class="span-4">
    <p class="info">(re-type password)</p>
  </div>
</div>

{for_layout assign='head'}
<script src="/assets/javascripts/my/my.field_options.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
  {literal}
  $(document).ready(function () {
    $('#class').field_options();
    $('#password').val(''); // avoid auto-fill in browsers...
  });
  {/literal}
</script>
{/for_layout}