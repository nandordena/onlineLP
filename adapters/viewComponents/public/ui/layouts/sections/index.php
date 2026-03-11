<div class="com_sections">
    <?
    foreach ($INIT['sections'] as $section) {
        echo loadLayout($section['uiLayout']);
    }
    ?>
</div>