<?php

	namespace App\Controller;

	class SMSController
	{
	    protected $logger;
	    protected $smsConf;
	    protected $smsReceiver;
	    protected $smsDesc;

	    public function __construct($logger, $smsConf)
	    {
	        $this->logger = $logger;
	        $this->smsConf = $smsConf;
	    }

		public function setSmsReceiver($smsReceiver){
			$this->smsReceiver = $smsReceiver;
		}	    

		public function setSmsDesc($smsDesc){
			$this->smsDesc = $smsDesc;
		}

	    public function sendSMS(){
	    	// Prepare params
	    	//print_r($this->smsConf);
	    	$url = $this->smsConf['url'];
			$params['method']	= 'send';
			$params['username']	= $this->smsConf['username'];
			$params['password']	= $this->smsConf['password'];

			$params['from']		= $this->smsConf['from'];
			$params['to']		= $this->smsReceiver;
			$params['message']	= $this->smsDesc;

			//$this->logger->info($params);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $params ));
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			$data = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			//echo $data;exit;
			$this->logger->info('Result Sent SMS : ' . $httpcode . ' to Tel. No. ' . $this->smsReceiver);
			return ($httpcode>=200 && $httpcode<300) ? $data : false;
		}

	}