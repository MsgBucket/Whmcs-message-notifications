<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

if(!defined("ZXPLGM_PREFIX"))
    define("ZXPLGM_PREFIX", "MsgBucket_whmcs");

if(!defined("ZXPLGM_NAME"))
    define("ZXPLGM_NAME", "MsgBucket Whmcs Module");

if(!defined("ZXPLGM_SITENAME"))
    define("ZXPLGM_SITENAME", "MsgBucket");

if(!defined("ZXPLGM_DESC"))
    define("ZXPLGM_DESC", "Intergrate WHMCS to MsgBucket");

if(!defined("ZXPLGM_VERSION"))
    define("ZXPLGM_VERSION", "2.0");

if(!defined("ZXPLGM_AUTHOR"))
    define("ZXPLGM_AUTHOR", "MsgBucket");

if(!defined("ZXPLGM_SITEURL"))
    define("ZXPLGM_SITEURL", "https://wa.msgbucket.com");

function MsgBucket_whmcs_config() {
    $configarray = [
            "name" => ZXPLGM_NAME,
            "description" => ZXPLGM_DESC,
            "version" => ZXPLGM_VERSION,
            "author" => ZXPLGM_AUTHOR,
            "language" => "english"
        ];
        
    return $configarray;
}

function MsgBucket_whmcs_activate() {
    
    $query = <<<SQL
    CREATE TABLE IF NOT EXISTS `mod_zxmessaging_settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `apikey` varchar(100) CHARACTER SET utf8 NOT NULL,
        `service` int(11) DEFAULT NULL,
        `whatsapp` varchar(500) CHARACTER SET utf8 NULL,
        `device` varchar(500) CHARACTER SET utf8 NULL,
        `gateway` varchar(500) CHARACTER SET utf8 NULL,
        `sim` int(11) DEFAULT NULL,
        `dateformat` varchar(12) CHARACTER SET utf8,
        PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
    SQL;

    mysql_query($query);
    
    $query = <<<SQL
    INSERT INTO `mod_zxmessaging_settings` (
        `apikey`, `service`, `whatsapp`, `device`, `gateway`, `sim`, `dateformat`
    ) VALUES (
        '', 1, '', '', '', 0, '%d.%m.%y'
    );
    SQL;

    mysql_query($query);    

    $query = <<<SQL
    CREATE TABLE IF NOT EXISTS `mod_zxmessaging_templates` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) CHARACTER SET utf8 NOT NULL,
        `type` enum('client','admin') CHARACTER SET utf8 NOT NULL,
        `admingsm` varchar(255) CHARACTER SET utf8 NOT NULL,
        `template` varchar(240) CHARACTER SET utf8 NOT NULL,
        `variables` varchar(500) CHARACTER SET utf8 NOT NULL,
        `active` tinyint(1) NOT NULL,
        `extra` varchar(3) CHARACTER SET utf8 NOT NULL,
        `description` text CHARACTER SET utf8,
        PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
    SQL;

    mysql_query($query);
    
    $query = <<<SQL
    INSERT INTO mod_zxmessaging_templates (name, type, admingsm, template, variables, active, extra, description) VALUES
    ('InvoicePaymentReminder', 'client', '', 'Greetings {firstname} {lastname}, The payment deadline is {duedate}. Please make the payment to ensure continuous services.', '{firstname}, {lastname}, {duedate}', 1, '', 'Invoice payment reminder before first due.'),
    ('TicketUserReplyAdmin', 'admin', '', 'A user has responded to the ticket with the subject ({subject})', '{subject}', 1, '', 'When user has replied on the ticket.'),
    ('UserLogin', 'admin', '', 'Client named ({firstname} {lastname}) has logged into the site.', '{firstname},{lastname}', 1, '', 'When a client has logged in.'),
    ('TicketAdminReply', 'client', '', 'Dear {firstname} {lastname}, admin has replied to the ticket ({ticketsubject}).', '{firstname}, {lastname}, {ticketsubject}', 1, '', 'When an admin replies to ticket.'),
    ('AfterModuleChangePassword', 'client', '', 'Hi {firstname} {lastname}, password for the {domain} has been updated successfully. Account details - Username: {username} Password: {password}', '{firstname}, {lastname}, {domain}, {username}, {password}', 1, '', 'When a module password has changed.'),
    ('TicketClose', 'client', '', 'Hello {firstname} {lastname}, The ticket number ({ticketno}) has been successfully resolved and closed. If you have any issues, please contact us.', '{firstname}, {lastname}, {ticketno}', 1, '', 'When a ticket is closed.'),
    ('DomainRenewalNotice', 'client', '', 'Hi {firstname} {lastname}, your domain {domain} will expire in {x} days, specifically on {expirydate}. Please visit our site to renew it. Thank you!', '{firstname}, {lastname}, {domain},{expirydate},{x}', 1, '15', 'Domain renewal Notice before specified days.'),
    ('AfterRegistrarRegistrationAdmin', 'admin', '', 'A new domain called {domain} has been registered.', '{domain}', 1, '', 'When a new domain is registered.'),
    ('AfterRegistrarRegistrationFailed', 'client', '', 'Hi {firstname} {lastname}, we could not register your domain name.', '{firstname},{lastname},{domain}', 1, '', 'When domain registration has failed.'),
    ('AcceptOrder', 'client', '', 'Dear {firstname} {lastname}, your order with ID {orderid} has been accepted.', '{firstname},{lastname},{orderid}', 1, '', 'When client order has been accepted.'),
    ('InvoicePaymentReminderSecond', 'client', '', 'Hi {firstname} {lastname}, your payment for {duedate} is still due. Please make the payment promptly to continue enjoying our services.', '{firstname}, {lastname}, {duedate}', 1, '', 'Invoice payment reminder for second overdue.'),
    ('ClientChangePassword', 'client', '', 'Hi {firstname} {lastname}, your password has been successfully updated.', '{firstname},{lastname}', 1, '', 'When a client has changed password.'),
    ('AfterRegistrarRenewal', 'client', '', 'Dear {firstname} {lastname}, your domain {domain} has been successfully renewed.', '{firstname},{lastname},{domain}', 1, '', 'When a domain has been renewed.'),
    ('AfterRegistrarRegistrationFailedAdmin', 'admin', '', 'An error occurred while registering the domain {domain}', '{domain}', 1, '', 'When domain registration has failed.'),
    ('InvoiceCreated', 'client', '', 'Hello {firstname} {lastname}, an invoice with ID {invoiceid} has been created. The total amount is {total}, and the due date is {duedate}. Please pay your bill before the deadline to avoid service disruption.', '{firstname}, {lastname}, {duedate}, {total}, {invoiceid}', 1, '', 'When a new invoice is created.'),
    ('ClientAdd', 'client', '', 'Hi {firstname} {lastname}, thank you for registering with us. Your account details are as follows - Email: {email} Password: {password}', '{firstname},{lastname},{email},{password}', 1, '', 'When a new client is added or registered.'),
    ('AfterRegistrarRenewalFailedAdmin', 'admin', '', 'An error occurred while renewing the domain {domain}', '{domain}', 1, '', 'When a domain registration failed.'),
    ('AfterModuleSuspend', 'client', '', 'Hi {firstname} {lastname}, the service associated with the domain ({domain}) has been suspended. Please contact us for more information.', '{firstname},{lastname},{domain}', 1, '', 'When a module is suspended.'),
    ('AdminLogin', 'admin', '', 'A user with the username {username} has accessed the admin panel.', '{username}', 1, '', 'When an admin logged in to control panel.'),
    ('InvoicePaid', 'client', '', 'Dear {firstname} {lastname}, your payment for the due date {duedate} has been received! Thank you.', '{firstname}, {lastname}, {duedate},{invoiceid}', 1, '', 'When an invoice has been paid.'),
    ('AfterRegistrarRegistration', 'client', '', 'Hi {firstname} {lastname}, the domain name ({domain}) has been successfully registered.', '{firstname},{lastname},{domain}', 1, '', 'When a domain has been registered.'),
    ('TicketOpenAdmin', 'admin', '', 'A new ticket with the subject ({subject}) has been opened.', '{subject}', 1, '', 'When new ticket is created.'),
    ('InvoicePaymentReminderFirst', 'client', '', 'Hi {firstname} {lastname}, your payment for the due date {duedate} is still pending. Please make the payment as soon as possible to continue using our services.', '{firstname}, {lastname}, {duedate}', 1, '', 'Invoice payment reminder for first overdue.'),
    ('AfterModuleUnsuspend', 'client', '', 'Greetings! The services for the domain ({domain}) have been reactivated.', '{firstname},{lastname},{domain}', 1, '', 'When a module is unsuspended.'),
    ('AfterRegistrarRenewalAdmin', 'admin', '', 'The domain {domain} has been successfully renewed.', '{domain}', 1, '', 'When a domain is renewed.'),
    ('AfterModuleCreate', 'client', '', 'Greetings! The services for the domain ({domain}) are now active. Account login details - Username:{username} Password: {password}', '{firstname}, {lastname}, {domain}, {username}, {password}', 1, '', 'When a module is created.'),
    ('InvoicePaymentReminderThird', 'client', '', 'Hi {firstname} {lastname}, the payment for the due date {duedate} remains outstanding. Please make the payment immediately to continue enjoying our services.', '{firstname}, {lastname}, {duedate}', 1, '', 'Invoice payment reminder for third overdue.'),
    ('AfterModuleChangePackage', 'client', '', 'Hello {firstname} {lastname}, the product/service package for your domain {domain} has been modified. Please contact us for further details.', '{firstname},{lastname},{domain}', 1, '', 'When a module is changed or updated.');
    SQL;

    mysql_query($query);

    return [
        "status" => "success",
        "description" => ZXPLGM_NAME . " addon successfully activated!"
    ];
    
}

function MsgBucket_whmcs_deactivate() {
    $query = <<<SQL
    DROP TABLE `mod_zxmessaging_templates`;
    SQL;

    mysql_query($query);

    $query = <<<SQL
    DROP TABLE `mod_zxmessaging_settings`;
    SQL;

    mysql_query($query);

    return [
        "status" => "success",
        "description" => ZXPLGM_NAME . " addon successfully deactivated!"
    ];
}

function MsgBucket_whmcs_output($vars){
    
    $api = new ZXPLGMessage();

    $addonPrefix = ZXPLGM_PREFIX;
    $tab = isset($_GET['tab']) ? $_GET['tab'] : false;
    $type = isset($_GET['type']) ? $_GET['type'] : false;

    $settingsTabClass = (($tab == "settings" || ($type && $tab)) ? "tabselected" : "tab");
    $clientSmsTemplatesTabClass = (($type == "client") ? "tabselected" : "tab");
    $adminSmsTemplatesTabClass = (($type == "admin") ? "tabselected" : "tab");
    $sendBulkTabClass = (($tab == "sendbulk") ? "tabselected" : "tab");

    $tabsHtml = <<<HTML
<style>
    .contentarea {
        background: #f5f5f5 !important;
    }
    
    #clienttabs * {
        margin: inherit;
        padding: inherit;
        border: inherit;
        color: inherit;
        background: inherit;
        background-color: inherit;
    }
    
    #clienttabs {
        position: relative;
        z-index: 99;
    }
    
    #clienttabs ul li {
        display: inline-block;
        margin-right: 3px;
        border: 1px solid #ddd;
        border-bottom: 0px;
        padding: 12px;
        margin-bottom: -1px;
    }
    
    #clienttabs ul a {
        border: 0px;
    }
    
    #clienttabs ul {
        float: left;
        margin-bottom: 0px;
    }
    
    #clienttabs {
        float: left;
    }
</style>

<div id="clienttabs">
    <ul>
        <li class="{$settingsTabClass}"><a href="addonmodules.php?module={$addonPrefix}&tab=settings">Settings</a></li>
        <li class="{$clientSmsTemplatesTabClass}"><a href="addonmodules.php?module={$addonPrefix}&tab=templates&type=client">Client Templates</a></li>
        <li class="{$adminSmsTemplatesTabClass}"><a href="addonmodules.php?module={$addonPrefix}&tab=templates&type=admin">Admin Templates</a></li>
        <li class="{$sendBulkTabClass}"><a href="addonmodules.php?module={$addonPrefix}&tab=sendbulk">Send Message</a></li>
    </ul>
</div>

<div style="clear: both;"></div>
HTML;

    echo $tabsHtml;
   
    if (!$tab || $tab == "settings"):
        if (isset($_POST["apikey"])) {
            $update = [
                "apikey" => $_POST["apikey"],
                "service" => $_POST["service"],
                "whatsapp" => $_POST["whatsapp"],
                "device" => $_POST["device"],
                "gateway" => $_POST["gateway"],
                "sim" => $_POST["sim"],
                "dateformat" => $_POST["dateformat"]
            ];

            update_query("mod_zxmessaging_settings", $update, "");
        }

        $settings = $api->apiSettings();

        $serviceSms = $settings["service"] < 2 ? "selected" : false;
        $serviceWa = $settings["service"] > 1 ? "selected" : false;
        $simOne = $settings["sim"] < 2 ? "selected" : false;
        $simTwo = $settings["sim"] > 1 ? "selected" : false;
        
        $settingsHtml = <<<HTML
<style>
    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        transition: 0.3s;
        width: 100%;
        background: #fff;
    }
    
    .container {
        padding: 20px;
        display: flex;
        justify-content: center;
        flex-direction: row;
        max-width: 700px;
    }
</style>

<br>

<div class="card">
    <div class="container">
        <form method="POST">
            <td class="fieldlabel" width="30%">API Token</td>
            <div class="input-group">
                <input type="text" name="apikey" class="form-control" size="40" value="{$settings["apikey"]}">
               
                <p>Your MsgBucket API key (<a href="https://wa.msgbucket.com" target="blank">Create API Token</a>).</p>

            </div> 
            
            <td class="fieldlabel" width="30%">Sending Service</td>
            <div class="input-group">
                <select name="service" class="form-control">
                    <option value="1" {$serviceWa}>MsgBucket WhatsApp</option>
                </select>

                
            </div>

            <td class="fieldlabel" width="30%">Date Format</td>
            <div class="input-group">
                <input type="text" name="dateformat" class="form-control" size="40" value="{$settings['dateformat']}">
            </div> e.g: %d.%m.%y (27.01.2014)

            <div class="btn-container">
                <input type="submit" value="Save" class="btn btn-primary" />
            </div>
        </form>
    </div>
</div>
HTML;

        echo $settingsHtml;
    endif;
    
    if ($tab == "templates"):
        if (isset($_POST["action"])):
            $where = [
                "type" => [
                    "sqltype" => "LIKE", 
                    "value" => $_GET['type']
                ]
            ];

            $result = select_query("mod_zxmessaging_templates", "*", $where);

            while ($data = mysql_fetch_array($result)):
                $tmp_active = $_POST[$data['id'] . '_active'] == "on" ? 1 : 0;

                $update = [
                    "template" => $_POST[$data['id'] . '_template'],
                    "active" => $tmp_active
                ];
    
                if (isset($_POST[$data['id'] . '_extra'])):
                    $update['extra'] = trim($_POST[$data['id'] . '_extra']);
                endif;

                if (isset($_POST[$data['id'] . '_admingsm'])):
                    $update['admingsm'] = str_replace(" ", "", $_POST[$data['id'] . '_admingsm']);
                endif;

                update_query("mod_zxmessaging_templates", $update, "id = " . $data['id']);
            endwhile;
        endif;
        
        echo '<style>
            table {
                padding: 20px !important;
            }

            table.form td.fieldarea {
                background-color: transparent !important;
            }

            table.form {
                box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2) !important;
            }
        </style>';

        echo '<br>';
        
        echo '<form method="POST">
                <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3" style="margin:0px;border: 0px;">
                    <tbody>';

        $where = [
            "type" => [
                "sqltype" => "LIKE", 
                "value" => $_GET['type']
            ]
        ];

        $result = select_query("mod_zxmessaging_templates", "*", $where);
    
        while ($data = mysql_fetch_array($result)):
            $active = $data['active'] == 1 ? 'checked = "checked"' : false;

            echo '<tr>
                    <td class="fieldlabel" width="30%">' . $data['name'] . ':</td>
                    <td class="fieldarea">
                        <p>' . $data['description'] . '</p>
                        <textarea cols="50" name="' . $data['id'] . '_template">' . $data['template'] . '</textarea>
                    </td>
                </tr>';

            echo '<tr>
                <td class="fieldlabel" style="float:right;">Shortcodes:</td>
                <td>' . $data['variables'] . '</td>
            </tr>';

            if (!empty($data['extra'])) {
                echo '<tr>
                    <td class="fieldlabel" width="30%">Number of Days:</td>
                    <td class="fieldarea">
                        <input type="text" name="' . $data['id'] . '_extra" value="' . $data['extra'] . '">
                    </td>
                </tr>';
            }
    
            if($_GET['type'] == "admin"){
                echo '<tr>
                    <td class="fieldlabel" width="30%">Admin Phone Numbers:</td>
                    <td class="fieldarea">
                        <input type="text" class="extraField" name="'.$data['id'].'_admingsm" placeholder="Eg. +0123456789, +0123456788" value="'.$data['admingsm'].'">
                    </td>
                </tr>';
            }

            echo ' <tr>
                <td class="fieldlabel" width="30%" style="float:right;">Enable:</td>
                <td><input type="checkbox" value="on" name="' . $data['id'] . '_active" ' . $active . '></td>
            </tr>';

            echo '<tr>
                <td colspan="2"><hr></td>
            </tr>';
        endwhile;

        echo '</tbody>
                </table>

            <div class="btn-container">
                <input type="hidden" name="action" value="save">
                <input type="submit" value="Save" class="btn btn-primary" />
            </div>
        </form>';
    endif;
    
    if($tab == "sendbulk"):
        if(!empty($_POST['action'])):
            $userinf = explode("_",$_POST['client']);
            $userid = $userinf[0];
            $gsmnumber = $userinf[1];
            $country = $userinf[4];

            $replacefrom = [
                "{firstname}",
                "{lastname}"
            ];
            $replaceto = [
                $userinf[2],
                $userinf[3]
            ];
            $message = str_replace($replacefrom,$replaceto,$_POST['message']);

            $api->setCountryCode($api->getCodeBy($country));
            $api->setGsmnumber($gsmnumber);
            $api->setMessage($message);
            $api->setUserid($userid);

            $result = $api->send();

            $cleanNumber = str_replace([".", " "], "", $gsmnumber);

            if($result):
                $sendResult = "<div class=\"notification\">Message has been sent to {$cleanNumber}</div>";
            else:
                $sendResult = "<div class=\"notification\">Failed to send message to {$cleanNumber}</div>";
            endif;
        endif;

        $query = <<<SQL
        SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `a`.`country`, `a`.`phonenumber` as `gsmnumber`
        FROM `tblclients` as `a` order by `a`.`firstname`
        SQL;

        $clients = '';
        $result = mysql_query($query);

        while ($data = mysql_fetch_array($result)):
            $clients .= '<option value="'.$data['id'].'_'.$data['gsmnumber'].'_'.$data['firstname'].'_'.$data['lastname'].'_'.$data['country'].'">'.$data['firstname'].' '.$data['lastname'].' (#'.$data['id'].')</option>';
        endwhile;

        echo '<style>
            table {
                padding: 20px !important;
            }

            table.form td.fieldarea {
                background-color: transparent !important;
            }

            table.form {
                box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2) !important;
            }

            .notification {
                background-color: #ffffff;
                padding: 15px;
                margin-bottom: 10px;
                border: 1px solid #e9e9e9;
                border-radius: 4px;
                font-size: 18px;
                font-weight: bold;
            }
        </style>';

        echo '<br>';

        echo '<script>
            jQuery.fn.filterByText = function(textbox, selectSingleMatch) {
                return this.each(function() {
                    var select = this;
                    var options = [];
                    $(select).find("option").each(function() {
                    options.push({value: $(this).val(), text: $(this).text()});
                    });
                    $(select).data("options", options);
                    $(textbox).bind("change keyup", function() {
                    var options = $(select).empty().scrollTop(0).data("options");
                    var search = $.trim($(this).val());
                    var regex = new RegExp(search,"gi");

                    $.each(options, function(i) {
                        var option = options[i];
                        if(option.text.match(regex) !== null) {
                        $(select).append(
                            $("<option>").text(option.text).val(option.value)
                        );
                        }
                    });
                    if (selectSingleMatch === true && 
                        $(select).children().length === 1) {
                        $(select).children().get(0).selected = true;
                    }
                    });
                });
            };

            $(function() {
                $("#clientdrop").filterByText($("#textbox"), true);
            });  
        </script>';

        echo '<form method="POST">
            ' . $sendResult . '
            <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3" style="margin:0px;border: 0px;">
                <tbody>
                    <tr>
                        <td class="fieldlabel" width="30%">Clients:</td>
                        <td class="fieldarea">
                            <input id="textbox" type="text" placeholder="Type client name" style="width:498px;padding:5px"><br>
                            <br>
                            <select name="client" class="sel" multiple id="clientdrop" style="padding:5px">
                                <option value="">Select Client/s</option>
                                ' . $clients . '
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td class="fieldlabel" width="30%">Message:</td>
                        <td class="fieldarea">
                            <textarea cols="70" rows="5" name="message" style="width:498px;padding:5px"></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td class="fieldlabel" width="30%">Shortcodes:</td>
                        <td class="fieldarea">
                            {firstname},{lastname}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="btn-container">
                <input type="hidden" name="action" value="save" />
                <input type="submit" value="Send" class="btn btn-primary" />
            </div>
        </form>';
    endif;

	echo "<br><center><a href=\"" . ZXPLGM_SITEURL . "\" target='_blank'>Created by " . ZXPLGM_SITENAME . "</center></a>";
    
}