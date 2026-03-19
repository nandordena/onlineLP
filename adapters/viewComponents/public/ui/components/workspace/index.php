
<?
$type="add";
if($type=="add"){
    ?>
    <div class="com_workspace add">
        <form class="fetchform" method="post" onEnd="workspace.addNewSpace">
            <input type="text" name="name" placeholder="new workspace name">
            <input type="hidden" name="adapter" value="controlRoom">
            <input type="hidden" name="endpoint" value="Workspace.new">
            <button type="submit" class="btn add-btn">+</button>
        </form>
    </div>
<?}else{?>
    <div class="com_workspace" ></div>
<?}?>