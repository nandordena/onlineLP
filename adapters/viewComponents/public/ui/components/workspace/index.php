
<?
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
    <div class="com_workspace" data-workspaceid="<?=$data['id']?>">
        <?=$data['name']?>
        <?
            if($_COOKIE['currentWorkspace']==$data['id']){
                ?><i class="active fa-regular fa-building"></i><?
            }else{
                ?><i class="enter fa-solid fa-arrow-right-to-bracket" data-onclick="workspace.changespace" data-value="<?=$data['id']?>"></i><?
            }
        ?>
        <form class="fetchform confirm" method="post" onEnd="workspace.remove">
            <input type="hidden" name="adapter" value="controlRoom">
            <input type="hidden" name="endpoint" value="Workspace.remove">
            <input type="hidden" name="id" value="<?=$data['id']?>">
            <button type="submit" class="btn add-btn">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
<?}?>