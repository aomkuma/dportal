<?php

namespace App\Controller;

use PHPMailer;

class Mailer
{
    protected $logger;
    protected $mailer;
    
    public function __construct($logger, $mailer)
    {
        
        include '../src/settings_var.php';

        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->mailer = new PHPMailer;

        $this->mailer->CharSet = 'UTF-8';
        # gmail
        $this->mailer->IsSMTP();
        //$mail -> SMTPSecure = "ssl";
        // $MAIL_HOST = "smtp.gmail.com";
        // $MAIL_PORT = "465";
        // $MAIL_USERNAME = "dpo.portal@gmail.com";
        // $MAIL_PASSWORD = "Dportal@123";
        // $MAIL_FROM = "dpo.portal@gmail.com";
        // $MAIL_FROMNAME = "d-Portal";

        if($MAIL_PORT == '465'){
            
            $this->mailer->SMTPAuth   = true;     
            $this->mailer->SMTPSecure = "ssl";     
        }else{
            $this->mailer->SMTPDebug = 2;
            $this->mailer->SMTPSecure = false;
            $this->mailer->SMTPAuth = false; 
            $this->mailer->SMTPAutoTLS = false;

            // $this->mailer->SMTPAuth = false;
            // $this->mailer->SMTPSecure = false;
            $this->mailer->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
        }
        $this->mailer->Host = $MAIL_HOST;//"smtp.gmail.com";
        $this->mailer->Port = $MAIL_PORT;//465;
        $this->mailer->Username = $MAIL_USERNAME;//"korapotu@gmail.com";
        $this->mailer->Password = $MAIL_PASSWORD;

        $this->mailer->From = $MAIL_FROM;//"korapotu@gmail.com";
        $this->mailer->FromName = $MAIL_FROMNAME;//"Aom";
        // $this->logger->info('mail host : '.$MAIL_HOST);

    }

    public function setSubject($subject){
        $this->mailer->Subject = $subject;
    }

    public function setReceiver($receiver){
        $this->mailer->AddAddress($receiver);
    }

    public function isHtml($status){
        $this->mailer->IsHTML($status); 
    }

    public function setHTMLContent($htmlContent){
        $this->mailer->MsgHTML($htmlContent); 
    }
    
    public function sendMail(){
        
        return $this->mailer->send();
        
    }
    
}