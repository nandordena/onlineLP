<?
    $sectionButtons = [
        "list"=>[
            "name"=>"list"
            ,"icon"=>"fa-regular fa-clipboard"
            ,"layer"=>"l_list"
        ]
        ,"sources"=>[
            "name"=>"sources"
            ,"icon"=>"fa-regular fa-circle-up"
            ,"layer"=>"l_sources"
        ]
        ,"bible"=>[
            "name"=>"bible"
            ,"icon"=>"fa-solid fa-book-bible"
            ,"layer"=>"l_bible"
        ]
        ,"website"=>[
            "name"=>"website"
            ,"icon"=>"fa-regular fa-window-restore"
            ,"layer"=>"website"
        ]
        ,"config"=>[
            "name"=>"config"
            ,"icon"=>"fa-solid fa-gear"
            ,"layer"=>"config"
        ]
        ,"space"=>[
            "name"=>"space"
            ,"icon"=>"fa-regular fa-building"
            ,"layer"=>"space"
        ]
    ];
?>
<div class="com_sectionSelector">

<?
foreach ($sectionButtons as $name => $sectionButton) {
    echo loadComponent('sectionButton',$sectionButton);
}
?>

</div>
