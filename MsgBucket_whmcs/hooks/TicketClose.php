<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");
    
add_hook("TicketClose", 1, function($vars) {
    $messageClass = new ZXPLGMessage();

    $template = $messageClass->getTemplate("TicketClose");

    if ($template["active"] == 0):
        return null;
    endif;

    $query = <<<SQL
    SELECT a.tid,b.id as userid, b.firstname, b.lastname, b.country, b.phonenumber as gsmnumber
    FROM tbltickets as a
    JOIN tblclients as b ON b.id = a.userid 
    WHERE a.id = '{$vars["ticketid"]}'
    LIMIT 1
    SQL;

    $result = mysql_query($query);
    $num_rows = mysql_num_rows($result);

    if ($num_rows == 1):
        $userInfo = mysql_fetch_assoc($result);

        $template["variables"] = str_replace(" ", "", $template["variables"]);
        $replacefrom = explode(",", $template["variables"]);
        $replaceto = [
            $userInfo["firstname"],
            $userInfo["lastname"],
            $userInfo["tid"]
        ];
        $message = str_replace($replacefrom, $replaceto, $template["template"]);

        $messageClass->setCountryCode($userInfo["country"]);
        $messageClass->setGsmnumber($userInfo["gsmnumber"]);
        $messageClass->setMessage($message);
        $messageClass->setUserid($userInfo["userid"]);
        $messageClass->send();
    endif;
});
