<form id="users_signup_form" action="/{$url.url}" method="post" accept-charset="utf-8" class="front centered">
  <h2>Log in</h2>
  <div id="validation_errors" class="errors">
    {endo_errors key=validation}
    {endo_errors}
  </div>
  <input type="hidden" name="redirect_to" value="{$redirect_to}" id="redirect_to">
  <div id="username_div" class="input text">
    <label for="username">Username:</label>
    <input type="text" name="username" value="{$username}" id="username">
  </div>
  <div id="password_div" class="input text">
    <label for="password">Password:</label>
    <input type="password" name="password" id="password">
  </div>
  <div id="submit_div" class="input submit">
    <input type="submit" value="Log in &rarr;">
  </div>
</form>