<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");
    
add_hook("DailyCronJob", 1, function ($vars) {
    $messageClass = new ZXPLGMessage();

    $template = $messageClass->getTemplate("DomainRenewalNotice");

    if ($template["active"] == 0) {
        return null;
    }

    $extra = $template["extra"];

    $query = <<<SQL
    SELECT `userid`, `domain`, `expirydate`
    FROM `tbldomains`
    WHERE `status` = 'Active'
    SQL;

    $resultDomain = mysql_query($query);
    while ($data = mysql_fetch_array($resultDomain)):
        $tarih = explode("-", $data["expirydate"]);
        $yesterday = mktime(0, 0, 0, $tarih[1], $tarih[2] - $extra, $tarih[0]);
        $today = date("Y-m-d");

        if (date("Y-m-d", $yesterday) == $today):
            $result = $messageClass->getClientDetails($data["userid"]);
            $num_rows = mysql_num_rows($result);
            if ($num_rows == 1):
                $userInfo = mysql_fetch_assoc($result);

                $template["variables"] = str_replace(" ", "", $template["variables"]);
                $replacefrom = explode(",", $template["variables"]);
                $replaceto = [
                    $userInfo["firstname"],
                    $userInfo["lastname"],
                    $data["domain"],
                    $data["expirydate"],
                    $extra
                ];
                $message = str_replace($replacefrom, $replaceto, $template["template"]);

                $messageClass->setCountryCode($userInfo["country"]);
                $messageClass->setGsmnumber($userInfo["gsmnumber"]);
                $messageClass->setMessage($message);
                $messageClass->setUserid($data["userid"]);
                $messageClass->send();
            endif;
        endif;
    endwhile;
});
