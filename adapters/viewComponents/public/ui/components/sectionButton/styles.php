<style>
    .com_sectionButton{
        padding:calc(var(--spacing)*4);
        padding-bottom: var(--spacing);
        margin:calc(var(--spacing)*2);
        background:var(--bg-2);
        border-radius:var(--spacing);
        font-size:2em;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .com_sectionButton:hover{
        filter:brightness(1.1);
    }
    .com_sectionButton span{
        font-size:12px;
        display: block;
        margin-top: var(--spacing);
        color:var(--fnt-4);
    }
    <?=
        implode(",\n", array_map(function( $section ){
            return "body[data-active-section=\"{$section['layer']}\"] .com_sectionButton[data-active=\"{$section['layer']}\"]";
        } , $INIT["sections"] ));
    ?>{
        outline: solid var(--spacing) var(--bg-2);
        background: transparent;
    }

</style>