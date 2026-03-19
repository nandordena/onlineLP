<?
    include_once $BASEDIR."/core/php/curl.php";
?>
<div class="lay_workspace">
WORKSPACE
<?
    echo loadComponent("workspace",["type"=>"add"]);
    $workspaces = BerericCurl::stdPost("controlRoom","Workspace.getByUser");
    foreach ($workspaces['data'] as $ws) {
        echo loadComponent("workspace",$ws);
    }
?>
</div>