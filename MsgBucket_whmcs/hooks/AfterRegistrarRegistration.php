<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");
    
add_hook("AfterRegistrarRegistration", 1, function($vars) {
    $messageClass = new ZXPLGMessage();

    $template = $messageClass->getTemplate("AfterRegistrarRegistration");

    if ($template["active"] === 0):
        return null;
    endif;

    $result = $messageClass->getClientDetails($vars["params"]["userid"]);
    $num_rows = mysql_num_rows($result);

    if ($num_rows === 1):
        $userInfo = mysql_fetch_assoc($result);

        $template["variables"] = str_replace(" ", "", $template["variables"]);
        $replacefrom = explode(",", $template["variables"]);
        $replaceto = [
            $userInfo["firstname"],
            $userInfo["lastname"],
            "{$vars["params"]["sld"]}.{$vars["params"]["tld"]}"
        ];
        $message = str_replace($replacefrom, $replaceto, $template["template"]);

        $messageClass->setCountryCode($userInfo["country"]);
        $messageClass->setGsmnumber($userInfo["gsmnumber"]);
        $messageClass->setUserid($vars["params"]["userid"]);
        $messageClass->setMessage($message);
        $messageClass->send();
    endif;
});
