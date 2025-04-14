<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");
    
add_hook("UserLogin", 1, function($vars) {
    $messageClass = new ZXPLGMessage();

    $template = $messageClass->getTemplate("UserLogin");

    if ($template["active"] === 0):
        return null;
    endif;

    $admingsm = explode(",", $template["admingsm"]);

    $result = $messageClass->getClientDetails($vars["user"]["id"]);
    $num_rows = mysql_num_rows($result);

    if ($num_rows === 1):
        $userInfo = mysql_fetch_assoc($result);

        $template["variables"] = str_replace(" ", "", $template["variables"]);
        $replacefrom = explode(",", $template["variables"]);
        $replaceto = [
            $userInfo["firstname"],
            $userInfo["lastname"]
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
    endif;
});
