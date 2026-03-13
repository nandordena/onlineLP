<div class="com_workspaces">
    <?
        $result = stdPost("controlRoom","space.getSpaces");
        echo json_encode($result,true);
    ?>
</div>