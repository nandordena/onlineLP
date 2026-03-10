<?
    $sections = [
        "sources"=>[
            "name"=>"sources"
            ,"icon"=>"fa-solid fa-box-archive"
            ,"layer"=>"l_sources"
        ]
        ,"bible"=>[
            "name"=>"bible"
            ,"icon"=>"fa-solid fa-book-bible"
            ,"layer"=>"l_bible"
        ]
        ,"website"=>[
            "name"=>"website"
            ,"icon"=>"fa-solid fa-link"
            ,"layer"=>"website"
        ]
    ];
?>
<div class="com_sections">

<?
foreach ($sections as $name => $section) {
    echo loadComponent('sectionButton',$section);
}
?>

</div>
