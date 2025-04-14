<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");
    
add_hook("InvoiceCreated", 1, function ($vars) {
    $messageClass = new ZXPLGMessage();

    $template = $messageClass->getTemplate("InvoiceCreated");

    if ($template["active"] == 0):
        return null;
    endif;

    $query = <<<SQL
    SELECT a.total, a.duedate, b.id as userid, b.firstname, b.lastname, b.currency, b.country, b.phonenumber as gsmnumber 
    FROM tblinvoices as a
    JOIN tblclients as b ON b.id = a.userid
    WHERE a.id = '{$vars["invoiceid"]}'
    LIMIT 1
    SQL;

    $result = mysql_query($query);
    $num_rows = mysql_num_rows($result);

    if ($num_rows == 1):
        $userInfo = mysql_fetch_assoc($result);

        $currency_sql = mysql_query("SELECT code FROM tblcurrencies WHERE id={$userInfo["currency"]}");
        $replace_currency = "";
        
        if (mysql_num_rows($currency_sql) > 0):
            $currency_result = mysql_fetch_assoc($currency_sql);
            $replace_currency = $currency_result["code"];
        endif;

        $template["variables"] = str_replace(" ", "", $template["variables"]);
        $replacefrom = explode(",", $template["variables"]);
        $replaceto = [
            $userInfo["firstname"],
            $userInfo["lastname"],
            $messageClass->changeDateFormat($userInfo["duedate"]),
            $userInfo["total"],
            $vars["invoiceid"],
            $replace_currency
        ];
        $message = str_replace($replacefrom, $replaceto, $template["template"]);

        $messageClass->setCountryCode($userInfo["country"]);
        $messageClass->setGsmnumber($userInfo["gsmnumber"]);
        $messageClass->setMessage($message);
        $messageClass->setUserid($userInfo["userid"]);
        $messageClass->send();
    endif;
});