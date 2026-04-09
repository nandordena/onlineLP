<script>
    <?=initJsClass("App","app")?>
    <?=initJsClass("Workspace","workspace")?>

    window.Workspace.prototype.addNewSpace = function(e,data = []) {
        console.info([e,data]);
    }
    window.Workspace.prototype.remove = function(e,data = []) {
        console.info([e,data]);
        e.submitter.parentElement.parentElement.remove();
    }
</script>

