<form id="users_login_form" action="login" method="post" accept-charset="utf-8" class="front centered">
  <h2>Log in</h2>
  <div id="validation_errors" class="errors">
    {errors key=validation}
    {errors}
  </div>
  <input type="hidden" name="redirect_to" value="{$redirect_to}" id="redirect_to">
  <input type="hidden" name="check_data" value="1" id="check_data">
  <div id="email_div" class="input text">
    <label for="email">Email:</label>
    <input type="text" name="email" value="{$email}" id="email">
  </div>
  <div id="password_div" class="input text">
    <label for="password">Password:</label>
    <input type="password" name="password" id="password">
  </div>
  <div id="remember_me_div" class="input checkbox">
    <label for="remember_me"><input type="checkbox" name="remember_me" id="remember_me" checked="checked"> remember me?</label>
  </div>
  <div id="submit_div" class="input submit">
    <input type="submit" value="Log in &rarr;">
  </div>
</form>