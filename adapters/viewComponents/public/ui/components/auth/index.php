<?
session_start();
$isSessionValid = false;

// Only check session if the required fields exist
if (
    (isset($_SESSION['sessionId']) || isset($_COOKIE['sessionId'])) &&
    (isset($_SESSION['sessionKey']) || isset($_COOKIE['sessionKey'])) &&
    (isset($_SESSION['user']) || isset($_COOKIE['user']))
) {
    
    include __DIR__."/../core/php/curl.php";

    $params = [
        'sessionId'   => $_SESSION['sessionId']   ?? $_COOKIE['sessionId']   ?? null,
        'sessionKey'  => $_SESSION['sessionKey']  ?? $_COOKIE['sessionKey']  ?? null,
        'user'        => $_SESSION['user']        ?? $_COOKIE['user']        ?? null
    ];

    $response = BerericCurl::post('userManager', 'session.validate', $params);
    if (
        isset($response['data']) 
        && (
            $response['data'] === true
            || (is_array($response['data']) && isset($response['data']['valid']) && $response['data']['valid'])
        )
        && empty($response['errors'])
    ) {
        $isSessionValid = true;
    }
}

?>

<script src="https://accounts.google.com/gsi/client" async defer></script>
<?
  include __DIR__."/../core/js/fetch.php";
  include __DIR__."/../core/js/cookies.php";
  include __DIR__."/auth.js.php";
  include __DIR__."/auth.css.php";
?>

<?  
if ($isSessionValid) {
    include __DIR__."/profile.php";
    return;
}
?>

<div id="g_id_onload"
     data-client_id="<?=getenv('googleClientId')?>.apps.googleusercontent.com"
     data-callback="handleCredentialResponse"
     data-ux_mode="popup"
     data-auto_prompt="false">
</div>

<div class="auth">
  <form class="fetchform" method="post" style=""  onEnd="auth.onLogin">
    <input type="text" name="email" placeholder="email" required style="margin: 4px 0; width: 60%;">
    <input type="password" name="pass" placeholder="Password" required style="margin: 4px 0; width: 60%;">
    <button type="submit" style="margin: 6px 0; width: 60%;">Login</button>
    <input type="hidden" name="adapter" value="userManager">
    <input type="hidden" name="endpoint" value="login">
  </form>
  <hr style="width: 60%; margin: 16px 0 0 0; border: 0; border-top: 1px solid #ccc;">
  <form class="fetchform" method="post" style="margin: 8px 0; display: flex; flex-direction: column; align-items: center; width: 100%;"  onEnd="auth.onLogin">
    <input type="text" name="email" placeholder="email" required style="margin: 4px 0; width: 60%;">
    <input type="password" name="pass" placeholder="Password" required style="margin: 4px 0; width: 60%;">
    <input type="password" name="repass" placeholder="Repeat Password" required style="margin: 4px 0; width: 60%;">
    <button type="submit" style="margin: 6px 0; width: 60%;">Register</button>
    <input type="hidden" name="adapter" value="userManager">
    <input type="hidden" name="endpoint" value="new">
  </form>
  <hr style="width: 60%; margin: 16px 0 0 0; border: 0; border-top: 1px solid #ccc;">
  <span style="margin: 8px 0; color: #888;">or</span>
  <div class="g_id_signin"></div>
</div>
