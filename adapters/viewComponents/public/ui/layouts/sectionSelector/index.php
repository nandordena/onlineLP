<div class="com_sectionSelector">

<?
foreach ($INIT['sectionButtons'] as $name => $sectionButton) {
    echo loadComponent('sectionButton',$sectionButton);
}
?>

</div>
