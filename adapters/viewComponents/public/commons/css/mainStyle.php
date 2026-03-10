<?
    $theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : "dark";
    $themeFile = $BASEDIR."/commons/css/theme-{$theme}.php";
    if (file_exists($themeFile)) {
        include $themeFile;
    } else {
        include $BASEDIR."/commons/css/theme-dark.php";
    }
    include $BASEDIR."/commons/css/clearcss.php";
?>
<style>
    body{
        display:flex;
        background: var(--bg-1);
        color: var(--fnt-1);
    }

    .space-1{margin:1em;}
    .space-2{margin:2em;}
    .space-3{margin:3em;}
    .space-4{margin:4em;}
    .space-5{margin:5em;}

    *{
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none; 
        user-select: none;
    }

    .pointer, input[type="submit"], button {
        cursor: pointer !important;
    }

    a, input{
        color: var(--fnt-2);
    }
    input{
        background: var(--bg-2);
        border: solid 1px var(--color-2);
        padding: var(--spacing);
        border-radius: var(--spacing);
        margin: var(--spacing);
        width: 100%;
    }
    hr{border: 1px solid var(--color-2);}

    .button, button, input[type="submit"]{
        border-radius: var(--spacing);
        padding: var(--spacing);
        margin: var(--spacing);
        border:solid 1px var(--color--2);
        width: 100%;
        background: var(--bg-5);
        color:var(--color-1);
    }
    .button:hover, button:hover, input[type="submit"]:hover{
        color:var(--color-2);
        background: var(--bg-4);
    }

    .spacing{
        margin:var(--spacing);
        padding:var(--spacing);
    }
    .center{
        text-aling:center;
    }

    .fntc-1{color:var(--fnt-1);}
    .fntc-2{color:var(--fnt-2);}
    .fntc-3{color:var(--fnt-3);}
    .fntc-4{color:var(--fnt-4);}
    .fntc-link{color:var(--fnt-link);}

</style>