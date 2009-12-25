<div class="input text">
  <label for="email">Email:</label>
  <input type="text" name="email" value="{$item->email}" id="email" class="small">
</div>
<div class="input select">
  <label for="class">Level:</label>
  {html_options name=class options=$levels selected=$class}
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
