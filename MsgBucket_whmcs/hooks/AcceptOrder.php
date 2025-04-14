<?php

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

add_hook("AcceptOrder", 1, function ($vars) {
    $messageClass = new ZXPLGMessage();

    $template = $messageClass->getTemplate("AcceptOrder");

    if ($template["active"] === 0)
        return null;

    $userSql = <<<SQL
    SELECT `a`.`id`, `a`.`firstname`, `a`.`lastname`, `a`.`phonenumber` as `gsmnumber`, `a`.`country`
    FROM `tblclients` as `a`
    WHERE `a`.`id` IN (SELECT userid FROM tblorders WHERE id = '{$vars["orderid"]}')
    LIMIT 1
    SQL;

    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);

    if ($num_rows === 1):
        $userInfo = mysql_fetch_assoc($result);

        $template["variables"] = str_replace(" ", "", $template["variables"]);
        $replacefrom = explode(",", $template["variables"]);
        $replaceto = [
            $userInfo["firstname"],
            $userInfo["lastname"],
            $vars["orderid"]
        ];
        $message = str_replace($replacefrom, $replaceto, $template["template"]);
        $messageClass->setCountryCode($userInfo["country"]);
        $messageClass->setGsmnumber($userInfo["gsmnumber"]);
        $messageClass->setUserid($userInfo["id"]);
        $messageClass->setMessage($message);
        $messageClass->send();
    endif;
});
