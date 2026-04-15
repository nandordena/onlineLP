
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
                ?><i class="enter fa-solid fa-arrow-right-to-bracket pointer" data-event-click="workspace.changespace" data-event-data="<?=$data['id']?>"></i><?
            }
        ?>
        <?switch ($data['access_type']) {
            case 'owner':
                ?><i class="fa-solid fa-trash pointer" data-event-click="workspace.remove" data-event-data="<?=$data['id']?>"></i><?
                break;
            case 'staff':
            case 'viewer':
                ?><i class="fa-solid fa-link-slash pointer" data-event-click="workspace.unlink" data-event-data="<?=$data['id']?>"></i><?
                break;
            default:break;
        }?>
    </div>
<?}?>