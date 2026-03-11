<style>
    <?=
        implode(",\n", array_map(function( $section ){
            return "body[data-active-section=\"{$section['layer']}\"] .com_section:not([data-active=\"{$section['layer']}\"])";
        } , $INIT["sections"] ));
    ?>{
        display:none;
    }
</style>