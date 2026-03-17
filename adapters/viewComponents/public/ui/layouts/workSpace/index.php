<?
include_once $BASEDIR."/core/php/curl.php";
?>
<div class="com_workspace">
    <?
        $result = BerericCurl::stdPost("controlRoom","Workspace.getByUser");
        echo json_encode($result,true);
    ?>
</div>