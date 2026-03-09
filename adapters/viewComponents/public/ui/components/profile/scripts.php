<script>
    <?=initJsClass("Profile","profile")?>
    window.Profile.prototype.onLogout = function(e,response,error){
        app.removeCookie('sessionId');
        app.removeCookie('sessionKey');
        app.removeCookie('user');
        window.location.reload();
    }
</script>