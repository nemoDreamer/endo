<div class="input text">
  <label for="name">Full name:</label>
  <input type="text" name="name" value="{$item->name}" id="name">
</div>
<div class="input text">
  <label for="username">Username:</label>
  <input type="text" name="username" value="{$item->username}" id="username" class="small">
</div>
<div class="show_more" more_label="change Password?">
  <h3>Password</h3>
  <div class="input text">
    <label for="password">Password:</label>
    <input type="password" name="password" id="password">
  </div>
  <div class="input text">
    <label for="password_confirm">Confirm:</label>
    <input type="password" name="password_confirm" id="password_confirm">
    <p class="info">(Re-type password)</p>
  </div>
</div>
