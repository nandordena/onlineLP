<script>
    <?=initJsClass("App","app")?>
    <?=initJsClass("Workspace","workspace")?>

    window.Workspace.prototype.addNewSpace = function(e,data = []) {
        
        if (data && data.errors && data.errors.length > 0) return;

        app.fetch("viewComponents", "components.workspace", data.data)
            .then(response => {
                console.info(response);
                if(!response.errors){
                    document.querySelector('.lay_workspace').insertAdjacentHTML('beforeend',response.content);
                }
            })
            .catch(error => {
                // handle error
            });
    }
    window.Workspace.prototype.remove = function(e,data = []) {
        console.info([e,data]);
        e.submitter.parentElement.parentElement.remove();
    }
</script>

