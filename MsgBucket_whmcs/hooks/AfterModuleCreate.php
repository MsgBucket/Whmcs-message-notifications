<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");
    
add_hook("AfterModuleCreate", 1, function($vars) {
    $type = $vars["params"]["producttype"];

    if ($type === "hostingaccount"):
        $messageClass = new ZXPLGMessage();
        $template = $messageClass->getTemplate("AfterModuleCreate");
        if ($template["active"] === 0):
            return null;
        endif;
    else:
        return null;
    endif;

    $result = $messageClass->getClientDetails($vars["params"]["clientsdetails"]["userid"]);
    $num_rows = mysql_num_rows($result);
    if ($num_rows === 1):
        $userInfo = mysql_fetch_assoc($result);

        $template["variables"] = str_replace(" ", "", $template["variables"]);
        $replacefrom = explode(",", $template["variables"]);
        $replaceto = [
            $userInfo["firstname"],
            $userInfo["lastname"],
            $vars["params"]["domain"],
            $vars["params"]["username"],
            $vars["params"]["password"]
        ];
        $message = str_replace($replacefrom, $replaceto, $template["template"]);

        $messageClass->setCountryCode($userInfo["country"]);
        $messageClass->setGsmnumber($userInfo["gsmnumber"]);
        $messageClass->setUserid($vars["params"]["clientsdetails"]["userid"]);
        $messageClass->setMessage($message);
        $messageClass->send();
    endif;
});
