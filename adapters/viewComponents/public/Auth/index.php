<?
  include __DIR__."/fetch.php";
?>
<script src="https://accounts.google.com/gsi/client" async defer></script>

<div id="g_id_onload"
     data-client_id="995550628836-rqqkpkpaudfam1fusu1g4er22df0t2gf.apps.googleusercontent.com"
     data-callback="handleCredentialResponse"
     data-ux_mode="popup"
     data-auto_prompt="false">
</div>

<div style="display: flex; flex-direction: column; align-items: center; width: 100%; margin-bottom: 16px;">
  <form class="fetchform" method="post" style="margin: 8px 0; display: flex; flex-direction: column; align-items: center; width: 100%;">
    <input type="text" name="email" placeholder="email" required style="margin: 4px 0; width: 60%;">
    <input type="password" name="password" placeholder="Password" required style="margin: 4px 0; width: 60%;">
    <button type="submit" style="margin: 6px 0; width: 60%;">Login</button>
    <input type="hidden" name="adapter" value="userManager">
    <input type="hidden" name="endpoint" value="login">
  </form>
  <hr style="width: 60%; margin: 16px 0 0 0; border: 0; border-top: 1px solid #ccc;">
  <form class="fetchform" method="post" style="margin: 8px 0; display: flex; flex-direction: column; align-items: center; width: 100%;">
    <input type="text" name="email" placeholder="email" required style="margin: 4px 0; width: 60%;">
    <input type="password" name="password" placeholder="Password" required style="margin: 4px 0; width: 60%;">
    <input type="password" name="repass" placeholder="Repeat Password" required style="margin: 4px 0; width: 60%;">
    <button type="submit" style="margin: 6px 0; width: 60%;">Register</button>
    <input type="hidden" name="adapter" value="userManager">
    <input type="hidden" name="endpoint" value="new">
  </form>
  <hr style="width: 60%; margin: 16px 0 0 0; border: 0; border-top: 1px solid #ccc;">
  <span style="margin: 8px 0; color: #888;">or</span>
  <div class="g_id_signin"></div>
</div>

<script>
    function handleCredentialResponse(response) {
      console.log("JWT:", response.credential);
    }
  
    function signOut() {
      console.log("Logout = borrar sesión local");
    }
</script>