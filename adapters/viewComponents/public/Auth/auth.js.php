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
    window.Auth.prototype.onRegister = function(e,response,error) {
        if(!auth.errorForm(e,response)){
            auth.setSessionCookies(response);
        }
    }
    window.Auth.prototype.onLogin = function(e,response,error) {
        if(!auth.errorForm(e,response)){
            auth.setSessionCookies(response);
            //window.reload();
        }
    }
    window.Auth.prototype.errorForm(e,response){
        const form = e.target;
        form.setAttribute('data-error','');
        if (response && response.errors && Array.isArray(response.errors) && response.errors.length > 0) {
            if (form && form.setAttribute) {
                form.setAttribute('data-error', response.errors.join('  -  '));
            }
            return true;
        }
        retunr false;
    }
    window.Auth.prototype.setSessionCookies(response){
        console.info(response);
    }
    window.Auth.prototype.onGoogleAuth = function(e,response,error) {
        console.log("JWT:", response.credential);
    }
    function handleCredentialResponse(response) {
        auth.onGoogleAuth(response);
    }

</script>