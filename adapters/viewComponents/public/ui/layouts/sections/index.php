<div class="com_sections">
<?
    foreach ($INIT['sections'] as $section) {
        echo loadLayout("sectionContent",$section);
    }
?>
</div>