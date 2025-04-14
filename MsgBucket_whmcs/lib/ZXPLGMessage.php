<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

class ZXPLGMessage 
{
    public $gsmnumber;
    public $message;
    public $countrycode;
    public $userid;

    public function setCountryCode($country){
        $this->countrycode = $this->getCodeBy($country);
    }

    public function getCountryCode(){
        return $this->countrycode;
    }

    public function setGsmnumber($gsmnumber){
        $this->gsmnumber = $this->util_gsmnumber($gsmnumber);
    }

    public function getGsmnumber(){
        return $this->gsmnumber;
    }

    public function setMessage($message){
        $this->message = $message;
    }

    public function getMessage(){
        return $this->message;
    }

    public function setUserid($userid){
        $this->userid = $userid;
    }

    public function getUserid(){
        return $this->userid;
    }

    public function apiSettings(){
        $result = select_query("mod_zxmessaging_settings", "*","");

        return mysql_fetch_array($result);
    }

    public function send(){
        $settings = $this->apiSettings();
        $message = $this->message;

        if(empty($settings["service"]) || $settings["service"] < 2):
            if(!empty($settings["device"])):
                $mode = "devices";
            else:
                $mode = "credits";
            endif;

            if($mode == "devices"):
                $params = [
                    "secret" => $settings["apikey"],
                    "mode" => "devices",
                    "device" => $settings["device"],
                    "phone" => $this->getGsmnumber(),
                    "message" => $message,
                    "sim" => $sim < 2 ? 1 : 2
                ];
            else:
                $params = [
                    "secret" => $settings["apikey"],
                    "mode" => "credits",
                    "gateway" => $settings["gateway"],
                    "phone" => $this->getGsmnumber(),
                    "message" => $message
                ];
            endif;

            $apiurl = ZXPLGM_SITEURL . "/api/send/sms";
        else:
            $params = [
                "token" => $settings["apikey"],
                "receiver" => $this->getGsmnumber(),
                "msgtext" => $message
            ];

            $apiurl = "https://server.msgbucket.com" . "/send";
        endif;

        $send = $this->invokeApi($params, $apiurl); 

        if($send){
            return true;
        }

        return false;
    }

    private function invokeApi($params = [], $apiurl) {
		$rest_request = curl_init();

		// Build query string from params
		$query_string = http_build_query($params);

		curl_setopt($rest_request, CURLOPT_URL, $apiurl . '?' . $query_string);
		curl_setopt($rest_request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($rest_request, CURLOPT_SSL_VERIFYPEER, false);

		// Execute cURL request and capture response
		$rest_response = curl_exec($rest_request);

		// Check for cURL errors
		if ($rest_response === false) {
			$error_msg = 'cURL error: ' . curl_error($rest_request);
			curl_close($rest_request);
			throw new Exception($error_msg);
		}

		// Close cURL resource
		curl_close($rest_request);

		// Return the response
		return $rest_response;
	}

    function util_gsmnumber($number){
        $replacefrom = [
            '-', 
            '(',
            ')', 
            '.', 
            ',', 
            '+', 
            ' '
        ];

        $number = str_replace($replacefrom, '', $number);

        return $number;
    }

    function getTemplate($template){
        $result = select_query("mod_zxmessaging_templates", "*", [
            "name" => $template
        ]);

        $data = mysql_fetch_assoc($result);

        return $data;
    }

    function changeDateFormat($date = null){
        $settings = $this->apiSettings();
        $dateformat = $settings['dateformat'];
        if(!$dateformat){
            return $date;
        }

        $date = explode("-",$date);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];

        $dateformat = str_replace([
            "%d",
            "%m",
            "%y"
        ],
        [
            $day,
            $month,
            $year
        ],
        $dateformat);

        return $dateformat;
    }

    function getClientDetails($clientId){
        $query = <<<SQL
        SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `a`.`phonenumber` as `gsmnumber`, `a`.`country`
        FROM `tblclients` as `a` WHERE `a`.`id`  = '{$clientId}'
        LIMIT 1
        SQL;

        return mysql_query($query);
    }

    function getClientAndInvoiceDetails($clientId){
        $query = <<<SQL
        SELECT a.total,a.duedate,b.id as userid,b.firstname,b.lastname,`b`.`country`,`b`.`phonenumber` as `gsmnumber` FROM `tblinvoices` as `a`
        JOIN tblclients as b ON b.id = a.userid
        WHERE a.id = '{$clientId}'
        LIMIT 1
        SQL;

        return mysql_query($query);
    }

    function getCodeBy($country){
        $countries = [];
        $countries["AF"]="+93";
        $countries["AF"]="+93";
        $countries["AL"]="+355";
        $countries["DZ"]="+213";
        $countries["AS"]="+1";
        $countries["AD"]="+376";
        $countries["AO"]="+244";
        $countries["AI"]="+1";
        $countries["AG"]="+1";
        $countries["AR"]="+54";
        $countries["AM"]="+374";
        $countries["AW"]="+297";
        $countries["AU"]="+61";
        $countries["AT"]="+43";
        $countries["AZ"]="+994";
        $countries["BH"]="+973";
        $countries["BD"]="+880";
        $countries["BB"]="+1";
        $countries["BY"]="+375";
        $countries["BE"]="+32";
        $countries["BZ"]="+501";
        $countries["BJ"]="+229";
        $countries["BM"]="+1";
        $countries["BT"]="+975";
        $countries["BO"]="+591";
        $countries["BA"]="+387";
        $countries["BW"]="+267";
        $countries["BR"]="+55";
        $countries["IO"]="+246";
        $countries["VG"]="+1";
        $countries["BN"]="+673";
        $countries["BG"]="+359";
        $countries["BF"]="+226";
        $countries["MM"]="+95";
        $countries["BI"]="+257";
        $countries["KH"]="+855";
        $countries["CM"]="+237";
        $countries["CA"]="+1";
        $countries["CV"]="+238";
        $countries["KY"]="+1";
        $countries["CF"]="+236";
        $countries["TD"]="+235";
        $countries["CL"]="+56";
        $countries["CN"]="+86";
        $countries["CO"]="+57";
        $countries["KM"]="+269";
        $countries["CK"]="+682";
        $countries["CR"]="+506";
        $countries["CI"]="+225";
        $countries["HR"]="+385";
        $countries["CU"]="+53";
        $countries["CY"]="+357";
        $countries["CZ"]="+420";
        $countries["CD"]="+243";
        $countries["DK"]="+45";
        $countries["DJ"]="+253";
        $countries["DM"]="+1";
        $countries["DO"]="+1";
        $countries["EC"]="+593";
        $countries["EG"]="+20";
        $countries["SV"]="+503";
        $countries["GQ"]="+240";
        $countries["ER"]="+291";
        $countries["EE"]="+372";
        $countries["ET"]="+251";
        $countries["FK"]="+500";
        $countries["FO"]="+298";
        $countries["FM"]="+691";
        $countries["FJ"]="+679";
        $countries["FI"]="+358";
        $countries["FR"]="+33";
        $countries["GF"]="+594";
        $countries["PF"]="+689";
        $countries["GA"]="+241";
        $countries["GE"]="+995";
        $countries["DE"]="+49";
        $countries["GH"]="+233";
        $countries["GI"]="+350";
        $countries["GR"]="+30";
        $countries["GL"]="+299";
        $countries["GD"]="+1";
        $countries["GP"]="+590";
        $countries["GU"]="+1";
        $countries["GT"]="+502";
        $countries["GN"]="+224";
        $countries["GW"]="+245";
        $countries["GY"]="+592";
        $countries["HT"]="+509";
        $countries["HN"]="+504";
        $countries["HK"]="+852";
        $countries["HU"]="+36";
        $countries["IS"]="+354";
        $countries["IN"]="+91";
        $countries["ID"]="+62";
        $countries["IR"]="+98";
        $countries["IQ"]="+964";
        $countries["IE"]="+353";
        $countries["IL"]="+972";
        $countries["IT"]="+39";
        $countries["JM"]="+1";
        $countries["JP"]="+81";
        $countries["JO"]="+962";
        $countries["KZ"]="+7";
        $countries["KE"]="+254";
        $countries["KI"]="+686";
        $countries["XK"]="+381";
        $countries["KW"]="+965";
        $countries["KG"]="+996";
        $countries["LA"]="+856";
        $countries["LV"]="+371";
        $countries["LB"]="+961";
        $countries["LS"]="+266";
        $countries["LR"]="+231";
        $countries["LY"]="+218";
        $countries["LI"]="+423";
        $countries["LT"]="+370";
        $countries["LU"]="+352";
        $countries["MO"]="+853";
        $countries["MK"]="+389";
        $countries["MG"]="+261";
        $countries["MW"]="+265";
        $countries["MY"]="+60";
        $countries["MV"]="+960";
        $countries["ML"]="+223";
        $countries["MT"]="+356";
        $countries["MH"]="+692";
        $countries["MQ"]="+596";
        $countries["MR"]="+222";
        $countries["MU"]="+230";
        $countries["YT"]="+262";
        $countries["MX"]="+52";
        $countries["MD"]="+373";
        $countries["MC"]="+377";
        $countries["MN"]="+976";
        $countries["ME"]="+382";
        $countries["MS"]="+1";
        $countries["MA"]="+212";
        $countries["MZ"]="+258";
        $countries["NA"]="+264";
        $countries["NR"]="+674";
        $countries["NP"]="+977";
        $countries["NL"]="+31";
        $countries["AN"]="+599";
        $countries["NC"]="+687";
        $countries["NZ"]="+64";
        $countries["NI"]="+505";
        $countries["NE"]="+227";
        $countries["NG"]="+234";
        $countries["NU"]="+683";
        $countries["NF"]="+672";
        $countries["KP"]="+850";
        $countries["MP"]="+1";
        $countries["NO"]="+47";
        $countries["OM"]="+968";
        $countries["PK"]="+92";
        $countries["PW"]="+680";
        $countries["PS"]="+970";
        $countries["PA"]="+507";
        $countries["PG"]="+675";
        $countries["PY"]="+595";
        $countries["PE"]="+51";
        $countries["PH"]="+63";
        $countries["PL"]="+48";
        $countries["PT"]="+351";
        $countries["PR"]="+1";
        $countries["QA"]="+974";
        $countries["CG"]="+242";
        $countries["RE"]="+262";
        $countries["RO"]="+40";
        $countries["RU"]="+7";
        $countries["RW"]="+250";
        $countries["BL"]="+590";
        $countries["SH"]="+290";
        $countries["KN"]="+1";
        $countries["MF"]="+590";
        $countries["PM"]="+508";
        $countries["VC"]="+1";
        $countries["WS"]="+685";
        $countries["SM"]="+378";
        $countries["ST"]="+239";
        $countries["SA"]="+966";
        $countries["SN"]="+221";
        $countries["RS"]="+381";
        $countries["SC"]="+248";
        $countries["SL"]="+232";
        $countries["SG"]="+65";
        $countries["SK"]="+421";
        $countries["SI"]="+386";
        $countries["SB"]="+677";
        $countries["SO"]="+252";
        $countries["ZA"]="+27";
        $countries["KR"]="+82";
        $countries["ES"]="+34";
        $countries["LK"]="+94";
        $countries["LC"]="+1";
        $countries["SD"]="+249";
        $countries["SR"]="+597";
        $countries["SZ"]="+268";
        $countries["SE"]="+46";
        $countries["CH"]="+41";
        $countries["SY"]="+963";
        $countries["TW"]="+886";
        $countries["TJ"]="+992";
        $countries["TZ"]="+255";
        $countries["TH"]="+66";
        $countries["BS"]="+1";
        $countries["GM"]="+220";
        $countries["TL"]="+670";
        $countries["TG"]="+228";
        $countries["TK"]="+690";
        $countries["TO"]="+676";
        $countries["TT"]="+1";
        $countries["TN"]="+216";
        $countries["TR"]="+90";
        $countries["TM"]="+993";
        $countries["TC"]="+1";
        $countries["TV"]="+688";
        $countries["UG"]="+256";
        $countries["UA"]="+380";
        $countries["AE"]="+971";
        $countries["GB"]="+44";
        $countries["US"]="+1";
        $countries["UY"]="+598";
        $countries["VI"]="+1";
        $countries["UZ"]="+998";
        $countries["VU"]="+678";
        $countries["VA"]="+39";
        $countries["VE"]="+58";
        $countries["VN"]="+84";
        $countries["WF"]="+681";
        $countries["YE"]="+967";
        $countries["ZM"]="+260";
        $countries["ZW"]="+263";

        return $countries[$country];
    }
}