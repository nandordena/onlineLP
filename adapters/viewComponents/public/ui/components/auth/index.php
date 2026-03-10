<div id="g_id_onload"
     data-client_id="<?=getenv('googleClientId')?>.apps.googleusercontent.com"
     data-callback="handleCredentialResponse"
     data-ux_mode="popup"
     data-auto_prompt="false">
</div>

<div class="auth">
  <form class="fetchform" method="post"   onEnd="auth.onLogin">
    <input type="text" name="email" placeholder="email" required>
    <input type="password" name="pass" placeholder="Password" required>
    <button type="submit" >Login</button>
    <input type="hidden" name="adapter" value="userManager">
    <input type="hidden" name="endpoint" value="login">
  </form>
  <hr>
  <form class="fetchform" method="post" onEnd="auth.onLogin">
    <input type="text" name="email" placeholder="email" required>
    <input type="password" name="pass" placeholder="Password" required>
    <input type="password" name="repass" placeholder="Repeat Password" required>
    <button type="submit" >Register</button>
    <input type="hidden" name="adapter" value="userManager">
    <input type="hidden" name="endpoint" value="new">
  </form>
  <hr>
  <span class="spacing fntc-4">or</span>
  <div class="g_id_signin"></div>
</div>
