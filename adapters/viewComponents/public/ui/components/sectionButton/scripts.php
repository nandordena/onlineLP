<script>
    <?=initJsClass("Sections","sections")?>
    window.Sections.prototype.activeSection = function(e,section) {
        app.globalState('data-active-section',section);
    }
</script>