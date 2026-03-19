
<?
$type="add";
if($type=="add"){
    ?>
    <div class="com_workspace add" data-event-click="workspaces.add">
        <form class="fetchform" method="post" onEnd="onend_method">
            <input type="text" name="name">
            <input type="hidden" name="adapter" value="adapter">
            <input type="hidden" name="endpoint" value="endpoint">
            <button type="submit" class="btn add-btn">+</button>
        </form>
    </div>
<?}else{?>
    <div class="com_workspace" ></div>
<?}?>