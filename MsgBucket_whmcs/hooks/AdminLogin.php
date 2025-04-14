<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");
    
add_hook("AdminLogin", 1, function($vars) {
    $messageClass = new ZXPLGMessage();

    $template = $messageClass->getTemplate("AdminLogin");

    if ($template["active"] === 0)
        return null;

    $admingsm = explode(",", $template["admingsm"]);
    $template["variables"] = str_replace(" ", "", $template["variables"]);
    $replacefrom = explode(",", $template["variables"]);
    $replaceto = [
        $vars["username"]
    ];
    $message = str_replace($replacefrom, $replaceto, $template["template"]);

    foreach ($admingsm as $gsm):
        if (!empty($gsm)):
            $messageClass->setGsmnumber(trim($gsm));
            $messageClass->setUserid($vars["adminid"]);
            $messageClass->setMessage($message);
            $messageClass->send();
        endif;
    endforeach;
});
