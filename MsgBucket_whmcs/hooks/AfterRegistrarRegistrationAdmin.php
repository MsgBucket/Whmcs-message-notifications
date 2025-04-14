<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");
    
add_hook("AfterRegistrarRegistration", 1, function($vars) {
    $messageClass = new ZXPLGMessage();

    $template = $messageClass->getTemplate("AfterRegistrarRegistrationAdmin");

    if ($template["active"] === 0):
        return null;
    endif;

    $admingsm = explode(",", $template["admingsm"]);

    $template["variables"] = str_replace(" ", "", $template["variables"]);
    $replacefrom = explode(",", $template["variables"]);
    $replaceto = [
        "{$vars["params"]["sld"]}.{$vars["params"]["tld"]}"
    ];
    $message = str_replace($replacefrom, $replaceto, $template["template"]);

    foreach ($admingsm as $gsm):
        if (!empty($gsm)):
            $messageClass->setGsmnumber(trim($gsm));
            $messageClass->setUserid($vars["userid"]);
            $messageClass->setMessage($message);
            $messageClass->send();
        endif;
    endforeach;
});
