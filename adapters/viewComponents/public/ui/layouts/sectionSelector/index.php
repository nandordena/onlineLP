<div class="lay_sectionSelector">

<?
foreach ($INIT['sections'] as $name => $sectionButton) {
    echo loadComponent('sectionButton',$sectionButton);
}
?>

</div>
