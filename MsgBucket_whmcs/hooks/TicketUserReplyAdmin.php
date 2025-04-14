<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");
    
add_hook("TicketUserReply", 1, function($vars) {
    $messageClass = new ZXPLGMessage();

    $template = $messageClass->getTemplate("TicketUserReplyAdmin");

    if ($template["active"] == 0):
        return null;
    endif;

    $admingsm = explode(",", $template["admingsm"]);

    $template["variables"] = str_replace(" ", "", $template["variables"]);
    $replacefrom = explode(",", $template["variables"]);
    $replaceto = [
        $vars["subject"]
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