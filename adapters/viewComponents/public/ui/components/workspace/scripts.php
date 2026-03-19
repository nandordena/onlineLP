<script>
    <?=initJsClass("App","app")?>
    <?=initJsClass("Workspace","workspace")?>

    window.Workspace.prototype.addNewSpace = function(e,data = []) {
        console.table([e,data]);
    }
</script>

