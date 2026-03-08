<script>
    if (typeof window.App === "undefined" || typeof window.App !== "function") {
        window.App = class App {};
    }
    if (typeof window.app === "undefined" || !(window.app instanceof window.App)) {
        window.app = new window.App();
    }
    if (typeof window.Auth === "undefined" || typeof window.Auth !== "function") {
        window.Auth = class Auth {};
    }
    if (typeof window.auth === "undefined" || !(window.auth instanceof window.Auth)) {
        window.auth = new window.Auth();
    }
    window.Auth.prototype.onLogin = function(e,response,error) {
        const form = e.target;
        if(!auth.errorForm(e,response)){
            if(auth.setSessionCookies(response)){
                window.location.reload();
            }else{
                form.setAttribute('data-error', 'Session error: unable to set session cookies. Pleace try login');
            }
        }
    }
    window.Auth.prototype.onLogout = function(e,response,error){
        const form = e.target;
        if(!auth.errorForm(e,response)){
            app.removeCookie('sessionId');
            app.removeCookie('sessionKey');
            app.removeCookie('user');
            window.location.reload();
        }
    }
    window.Auth.prototype.errorForm = function(e,response) {
        const form = e.target;
        form.setAttribute('data-error','');
        if (response && response.errors && Array.isArray(response.errors) && response.errors.length > 0) {
            if (form && form.setAttribute) {
                form.setAttribute('data-error', response.errors.join('  -  '));
            }
            return true;
        }
        return false;
    }
    window.Auth.prototype.setSessionCookies = function(response){
        if (!response || !response.data || !response.data.sessionId || !response.data.sessionKey || !response.data.user) {
            return false;
        }
        app.setCookie('sessionId',response.data.sessionId,7);
        app.setCookie('sessionKey',response.data.sessionKey,7);
        app.setCookie('user',response.data.user,7);
        return true;
    }
    window.Auth.prototype.onGoogleAuth = function(response) {
        const e = {target: document.getElementById('g_id_onload')};
        app.fetch('userManager','googleAuth',{
                credential: response.credential
                ,adapter: 'userManager'
                ,endpoint: 'googleAuth'
            }
        ).then(data => {
            if(!auth.errorForm(e,data)){
                if(auth.setSessionCookies(data)){
                    window.location.reload();
                }else{
                    e.target.setAttribute('data-error', 'Session error: unable to set session cookies. Pleace try login');
                }
            }
        }).catch(err => {
            e.target.setAttribute('data-error', 'Network error: ' + err.message);
        });
    }
    function handleCredentialResponse(response) {
        auth.onGoogleAuth(response);
    }

</script>