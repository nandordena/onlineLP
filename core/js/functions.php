<script>
    <?=initJsClass("App","app")?>
    window.App.prototype.globalState = function(stateName,stateValue) {
        document.body.setAttribute(stateName, stateValue);
    }
</script>