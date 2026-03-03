<script>
    if (typeof window.App === "undefined" || typeof window.App !== "function") {
        window.App = class App {};
    }
    if (typeof window.app === "undefined" || !(window.app instanceof window.App)) {
        window.app = new window.App();
    }
    window.App.prototype.setCookie = function(name, value, days = null) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
    }
    window.App.prototype.removeCookie = function(name) {
        document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    };
</script>