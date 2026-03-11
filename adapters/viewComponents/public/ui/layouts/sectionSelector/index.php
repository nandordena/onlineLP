<div class="com_sectionSelector">

<?
foreach ($INIT['sections'] as $name => $sectionButton) {
    echo loadComponent('sectionButton',$sectionButton);
}
?>

</div>
