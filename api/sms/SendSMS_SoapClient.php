<?php
  	ini_set("soap.wsdl_cache_enabled", "0");
	function SendSMS($text, $receiver) {
		$client = new SoapClient('http://api.payamak-panel.com/post/send.asmx?wsdl', array('encoding'=>'UTF-8'));
		$parameters['username'] = "instagramplan";
		$parameters['password'] = "fs@BHJ@VH#8";
		$parameters['from'] = "10007720";
		$parameters['to'] = $receiver;
		$parameters['text'] = $text;
		$parameters['isflash'] = false;


		
		if ($client->SendSimpleSMS2($parameters)->SendSimpleSMS2Result) {
			return 1;
		}
		else { 
			return 0;
		}
	}
?>
