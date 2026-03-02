<script>
    if (typeof window.App === "undefined" || typeof window.App !== "function") {
        window.App = class App {};
    }
    if (typeof window.app === "undefined" || !(window.app instanceof window.App)) {
        window.app = new window.App();
    }
</script>