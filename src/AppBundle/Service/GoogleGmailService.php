<?php
namespace AppBundle\Service;

use Google_Client;
use Google_Service_Gmail;
use Google_Service_Oauth2;
use Google_Service_Gmail_Message;
use AppBundle\Entity\Integration;

//use AppBundle\Service\RedisService as CS;

/**
 * Description of GoogleGmail
 *
 */
class GoogleGmailService
{

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function initClient(Integration $integration)
    {
        $int = $integration->toArray();
        $client = new Google_Client();
        //$client->setDeveloperKey('f0d3bf83c658962df0cdd7903955f5ab2880443a');
        $client->setClientId('920431362941-ksi7mm22mmr6q3i1t20o2749i13jokp8.apps.googleusercontent.com');
        $client->setClientSecret('wYZreZfgqn_g2m808JWI5KMu');

        $client->addScope('https://mail.google.com');
        $client->addScope("https://www.googleapis.com/auth/gmail.send");
        $client->addScope("https://www.googleapis.com/auth/gmail.compose");
        $client->addScope("https://www.googleapis.com/auth/gmail.modify");
        $client->addScope("https://www.googleapis.com/auth/gmail.readonly");
        $client->addScope('https://www.googleapis.com/auth/userinfo.email');
        $client->addScope('https://www.googleapis.com/auth/userinfo.profile');
        $client->setConfig('subject', 'you@email.com');
        $int['values']->expires_in = time() + 60*60*24*7;
        print_r($int);
        $client->setAccessToken(json_encode($int['values'], false));
        $client->setRedirectUri('http://dev.cliently.ca');
        //$client->setAccessType('offline');
        //$client->setApprovalPrompt('force');
        $this->service = new Google_Service_Gmail($client);
        $this->oauth2 = new Google_Service_Oauth2($client);
    }

    /**
     * @param array $data
     * $data must contain array('from', 'to', 'message', 'subject', 'Cc', 'Bcc', 'In-Reply-To', 'files' => array('filename'=>'somename', 'attachment'), array('filename'=>'somename', 'attachment'))
     * @return mixed
     */
    public function sendMessage($data = array())
    {

        $data['dmy'] = date("d-M-Y H:i:s");
        $boundary = "------=" . md5(uniqid(rand()));
        $fileStatement = '';

        if (isset($data['files'])) {
            foreach ($data['files'] as $file) {
                $fileStatement .= "Content-Type: application/octet-stream; name=\"" . $file['filename'] . "\"\n";
                $fileStatement .= "Content-Description: " . $file['filename'] . "\n";
                $fileStatement .= "Content-Disposition: attachment;\n" . " filename=\"" . $file['filename'] . "\";\n";
                $fileStatement .= "Content-Transfer-Encoding: base64\n\n" . $file['attachment'] . "\n\n";
                $fileStatement .= "\r\n\r\n\r\n" . "--$boundary" . "\r\n";
            }
        }

        $subject_preferences = array('input-charset' => 'UTF-8', 'output-charset' => 'UTF-8');
        $encoded_subject = iconv_mime_encode('Subject', $data['subject'], $subject_preferences);
        $encoded_subject = substr($encoded_subject, strlen('Subject: '));

        $strRawMessage = "From: {$data['from']}\r\n";
        $strRawMessage .= "To: {$data['to']}\r\n";

        // TODO: Better reply implementation. Check http://stackoverflow.com/questions/32589476/how-to-send-a-reply-with-gmail-api
        if (isset($data['References'])) {
            $strRawMessage .= 'References: ' . $data['References'] . "\r\n";
        }
        if (isset($data['In-Reply-To'])) {
            $strRawMessage .= 'In-Reply-To: ' . $data['In-Reply-To'] . "\r\n";
        }
        if (isset($data['Cc'])) {
            $strRawMessage .= 'Cc: ' . implode(',', $data['Cc']) . "\r\n";
        }
        if (isset($data['Bcc'])) {
            $strRawMessage .= 'Bcc: ' . implode(',', $data['Bcc']) . "\r\n";
        }

        $strRawMessage .= "X-Mailer: Cliently Mailer\r\n";
        $strRawMessage .= "Date: {$data['dmy']}\r\n";
        $strRawMessage .= "Subject: {$encoded_subject}\r\n";
        $strRawMessage .= "MIME-Version: 1.0\r\n";
        $strRawMessage .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
        $strRawMessage .= "\r\n\r\n";
        $strRawMessage .= "--$boundary\r\n";
        $strRawMessage .= "Content-Type: text/html;\r\n\tcharset=\"UTF-8\"\r\n";
        $strRawMessage .= "Content-Transfer-Encoding: 8bit \r\n";
        $strRawMessage .= "\r\n\r\n";
        $strRawMessage .= "{$data['message']}\r\n";
        $strRawMessage .= "\r\n\r\n";
        $strRawMessage .= "--$boundary\r\n";
        $strRawMessage .= $fileStatement;
        $mime = rtrim(strtr(base64_encode($strRawMessage), '+/', '-_'), '=');
        echo $mime;
        $msg = new Google_Service_Gmail_Message();
        $msg->setRaw($mime);
        print_r($msg);

        $message = $this->service->users_messages->send('me', $msg);
        print 'Message with ID: ' . $message->getId() . ' sent.';
        return $message;
    }
    
    public function checkMessages(){
        
    }
}
