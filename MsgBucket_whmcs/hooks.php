<?php

if (!defined("WHMCS")):
    die("This file cannot be accessed directly");
endif;

if(!defined("ZXPLGM_SITEURL"))
    define("ZXPLGM_SITEURL", "https://wa.msgbucket.com");


if ($handle = opendir(dirname(__FILE__) . "/hooks")):
    require __DIR__ . "/lib/ZXPLGMessage.php";
    
    while (false !== ($entry = readdir($handle))):
        if(substr($entry, strlen($entry) - 4, strlen($entry)) == ".php"):
            require "hooks/{$entry}";
        endif;
    endwhile;

    closedir($handle);
endif;
