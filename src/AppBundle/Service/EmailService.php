<?php
namespace AppBundle\Service;

//use Carbon\Carbon;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use AppBundle\Entity\Integration;

class EmailService
{

    /**
     * Constructor
     *
     * @param mixed $container
     */
    private $mailer;
    private $templating;

    public function __construct($container, TokenStorage $tokenStorage)
    {
        $this->container = $container;
        $this->mailer = $container->get('mailer');
        $this->templating = $container->get('templating');
        $this->user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     *
     * @param type $reportstats
     * @param type $emails
     */
    public function mailTest($dryRun = false, $emails = [])
    {
        $this->output = new ConsoleOutput();
        /* $params['report_date'] = Carbon::now()->format('Y-m-d');
          $params['report_date_yesterday'] = Carbon::yesterday()->format('Y-m-d');
          $params['current_year'] = Carbon::now()->year; */
        $params['company'] = 'Cliently';
        $params['title'] = 'Test ';
        $params['dry_run'] = $dryRun;
        if ($dryRun == false) {
            $this->output->writeln('<comment>Emails : ' . print_r($emails, 1) . '</comment>');
            $message = \Swift_Message::newInstance()
                ->setSubject($params['title'])
                ->setFrom($this->from)
                ->setTo($emails)
                ->setBody($this->templating->render(
                    'AppBundle:Email:invite.html.twig', $params
                ), 'text/html');
            $this->mailer->send($message);
        } else {
            $this->output->writeln('<comment>****DRYRUN***</comment>');
            $this->output->writeln('<comment>Email Ad Report.</comment>');
            $this->output->writeln('<comment>Emails : ' . print_r($emails, 1) . '</comment>');
            $fs = new Filesystem();
            $fs->dumpFile('web/reports/invite.html', $this->templating->render(
                    'AppBundle:Email:invite.html.twig', $params
            ));
        }
        $startTime = time();
        $this->output->writeln('<info>Report Sent ' . $startTime . '</info>');
    }

    /**
     * 
     * @param type $email
     * @param type $description
     * @param type $name
     * @param type $client_name
     * @param type $cc
     * @param type $bcc
     * @param type $in_reply
     * @param type $references
     * @return boolean
     */
    public function deliverGmail($email, $description, $name, $client_name, $cc = [], $bcc = [], $in_reply = '', $references = '', $dry_run = false)
    {
        $this->load->model(array('User_model'));
        $integration = $this->User_model->get_user_google_integration($_SESSION['user_id']);
        if (!$integration) {
            return false;
        }
        $integration_type = Integration_model::TYPE_BY_CLASS['google']['id'];

        $google_values = json_decode($integration['values'], TRUE);
        $this->load->library('google', array('initial_auth' => FALSE));
        $this->google->auth($google_values['access_token']);
        $tracking_code = MD5(uniqid());
        $msg_vals = array(
            'AddressName' => $client_name,
            'from' => $google_values['google_name'] . ' <' . $google_values['google_screen_name'] . '>',
            'to' => $client_name . ' <' . $email . '>',
            'message' => $this->load->view('responsive_email.phtml', ['subject' => $name,
                'description' => $description,
                'tracking_code' => $tracking_code], true),
            'subject' => $name,
            'Cc' => $cc !== '' ? [$cc] : [],
            'Bcc' => $bcc !== '' ? [$bcc] : [],
        );
        if ($in_reply != '') {
            $msg_vals['In-Reply-To'] = $in_reply;
        }
        if ($references != '') {
            $msg_vals['References'] = $references;
        }
        $msg = $this->google->sendMessage($msg_vals);
        $code = $msg->getId();
        $meta = $this->google->getMessagesInfo([$code]);
        $uid = isset($meta[0]['message-id']) ? $meta[0]['message-id'] : '';
        $thread_code = $msg->getThreadId();
        if (isset($integration['handle'])) {
            $handle = $integration['handle'];
        } else {
            $handle = '';
        }
        $row = [
            'name' => $name,
            'description' => $description,
            'cc' => $cc,
            'bcc' => $bcc,
            'uid' => $uid,
            'references' => $references,
            'code' => $code,
            'thread_code' => $thread_code,
            'tracking_code' => $tracking_code,
            'email' => $email,
            'handle' => $handle,
            'integration_type' => $integration_type
        ];
        return $row;
    }

    /**
     * 
     * @param type $email
     * @param type $description
     * @param type $client_name
     * @param type $name
     * @param type $cc
     * @param type $bcc
     * @param type $in_reply
     * @param type $references
     * @return boolean
     */
    public function deliverImap($email, $description, $client_name, $name, $cc = [], $bcc = [], $in_reply = '', $references = '', $dryRun = false)
    {

        $integrations = $this->user->getIntegrations();
        $integration = false;
        foreach ($integrations as $i) {
            if ($i->getType() == 1) {
                $integration = $i->toArray();
            }
        }
        if (!$integration) {
            return false;
        }

        $integration_type = Integration::TYPE_BY_CLASS['mail']['id'];
        $tracking_code = MD5(uniqid());

        if ($dryRun) {
            $fs = new Filesystem();
            $fs->dumpFile('web/reports/invite.html', $this->templating->render(
                    'AppBundle:Email:responsive_email.html.twig', ['subject' => $name,
                    'description' => $description,
                    'tracking_code' => $tracking_code]
            ));
            return true;
        }
        $message = \Swift_Message::newInstance()
            ->setSubject($name)
            ->setFrom($integration['handle'])
            ->setTo($email)
            ->setBody($this->templating->render(
                'AppBundle:Email:responsive_email.html.twig', ['subject' => $name,
                'description' => $description,
                'tracking_code' => $tracking_code]
            ), 'text/html');
        $headers = $message->getHeaders();
        if (is_array($cc) && count($cc) > 0) {
            $message->setCc($cc);
        }
        if (is_array($bcc) && count($bcc) > 0) {
            $message->setBcc($bcc);
        }
        if ($in_reply != '') {
            $headers->addTextHeader('In-Reply-To', $in_reply);
        }
        if ($references != '') {
            $headers->addTextHeader('References', $references);
        }

        // Create the Transport
        $transport = \Swift_SmtpTransport::newInstance($integration['values']->smtp_server, $integration['values']->smtp_port)
            ->setEncryption('tls')
            ->setUsername($integration['handle'])
            ->setPassword($integration['values']->password)
        ;
        $mailer = \Swift_Mailer::newInstance($transport);
        $messageId = $message->getId();
        $mailer->send($message);

        if (isset($integration['handle'])) {
            $handle = $integration['handle'];
        } else {
            $handle = '';
        }
        $headers = $message->getHeaders();
        echo $messageId . ' ' . $message->getId();
        print_r($headers);exit;
        $row = [
            'name' => $name,
            'description' => $description,
            'cc' => $cc,
            'bcc' => $bcc,
            'uid' => $message->getId(),
            'references' => $references,
            'code' => md5($message->getId()),
            'thread_code' => '',
            'tracking_code' => $tracking_code,
            'email' => $email,
            'handle' => $handle,
            'integration_type' => $integration_type
        ];
        return $row;
    }

    public function searchAllImapIntegrations()
    {
        $integrations = $this->em->getRepository('AppBundle:Integration')->findBy(['type' => 1]);
        foreach ($integrations as $i) {
            $integration = $i->toArray();
            //print_r($integration);
            $msgs = $this->searchMessages($integration, 'SENT');
            //print_r($msgs);
        }
    }

    /**
     * @param string $criteria
     * @see http://php.net/manual/ru/function.imap-search.php
     * @return array|bool
     */
    public function searchMessages($integration, $mailbox = 'INBOX', $criteria = '', $emails = [], $is_incoming = TRUE)
    {
        if ($emails) {
            if ($is_incoming)
                $email_criteria_word = 'FROM';
            else
                $email_criteria_word = 'TO';
            $email_criteria = str_repeat('OR ', count($emails) - 1);
            foreach ($emails as $email) {
                $email_criteria .= $email_criteria_word . ' "' . $email . '" ';
            }

            $criteria .= ' (' . trim($email_criteria) . ')';
        }

        try {
            $this->server = '{' . $integration['values']->imap_server . ':' . $integration['values']->imap_port . '/ssl}';
            $this->mbox = $this->server . $mailbox;
            $this->stream = imap_open($this->mbox, $integration['values']->email, $integration['values']->password);
            $try_novalidate = FALSE;
        } catch (Exception $e) {
            $try_novalidate = TRUE;
        }

        if ($try_novalidate) {
            $this->server = '{' . $integration['values']->imap_server . ':' . $integration['values']->imap_port . '/ssl/novalidate-cert' . '}';
            $this->mbox = $this->server . $mailbox;
            $this->stream = imap_open($this->mbox, $integration['values']->email, $integration['values']->password);
        }
        if (!$this->stream) {
            return false;
        }
        $this->is_connected = 1;

       /* $boxes = imap_list($this->stream, $this->server, '%');
        if (is_array($boxes)) {
            foreach ($boxes as $box) {
                $box = str_replace($this->server, '', $box);
                if (!in_array(strtolower($box), ['inbox', 'trash', 'unwanted'])) {
                    $this->out_folders[] = $box;
                }
            }
        }*/

        $status = imap_status($this->stream, $this->mbox, SA_UIDVALIDITY);
        $ids = imap_search($this->stream, trim($criteria), SE_UID);
        $stack = array();

        if (!$ids)
            return FALSE;

        $headers = imap_fetch_overview($this->stream, implode($ids, ','), FT_UID);
        foreach ($headers as $header) {
            print_r($header);
            $msg = $this->buildEmailHeaderArray($header);
            if(!$msg){
                continue;
            }
           // print_r($msg);
            //$msg['body'] = imap_fetchbody($this->stream, $msg['msgno'], FT_UID);
            //$msg['full_uid'] = isset($status->uidvalidity) ? $status->uidvalidity : '' . ':' . $mailbox . ':' . $msg['uid'];
            $stack[] = $msg;
        }

        return ( empty($stack) ) ? false : $stack;
    }

    /**
     * 
     * @param type $head
     * @return type
     */
    public function buildEmailHeaderArray($head)
    {
        if(!isset($head->from)){
            return false;
        }
        $array = [];
        $array['uid'] = $head->uid;
        $array['msgno'] = $head->msgno;
        $sender_time = date_timestamp_get(date_create_from_format(\DateTime::RFC2822, $head->date == false ? new DateTime() : $head->date));
        $current_time = time();
        $array['date'] = $sender_time > $current_time ? $current_time : $sender_time;
        $array['subject'] = self::decodeInfo($head->subject);
        if (isset($head->message_id))
            $array['message_id'] = $head->message_id;
        if (isset($head->references))
            $array['references'] = $head->references;
        if (isset($head->cc)) {
            $cc = array();
            foreach ($head->cc as $val) {
                $cc[] .= $val->mailbox . '@' . $val->host;
            }
            $array['cc'] = implode(', ', $cc);
        }
        $array['from'] = $head->from;
        $array['sender'] = $head->from;
        $array['reply_toaddress'] = $head->to;
        $array['to'][] = $head->to;
        return $array;
    }

    /**
     * @param $string
     * @return string
     */
    public function decodeInfo($string)
    {
        $parts = imap_mime_header_decode($string);
        $str = '';

        for ($p = 0; $p < count($parts); $p++) {
            $ch = $parts[$p]->charset;
            $part = $parts[$p]->text;
            if ($ch !== 'default')
                $str .= mb_convert_encoding($part, 'UTF-8', $ch);
            else
                $str .= $part;
        }

        return $str;
    }
}
