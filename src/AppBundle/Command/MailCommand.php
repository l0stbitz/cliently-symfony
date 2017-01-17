<?php
/**
 *
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

/**
  =
 *
 * @author Josh Murphy
 *
 * @todo Add configure help
 * @todo Document each request
 */
class MailCommand extends ContainerAwareCommand
{

    /*
     * {@inheritdoc}
     */
    private $stream;
    private $mbox;
    private $imapHost     = 'imap-mail.outlook.com';
    private $imapPort     = 993;
    private $imapTls      = 'ssl';
    private $smtpHost     = 'smtp-mail.outlook.com';
    private $smtpPort     = '587';
    private $smtpTls      = 'ssl';

    /**
* 
* 
     * configure
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    protected function configure()
    {
        $this
            ->setName('cliently:mail')
            ->setDescription('Acquire social stats for content')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry Run')
            ->setHelp('TODO: Fill this in');
    }

    /**
     * {@inheritdoc}
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;
        $this->dryRun = $this->input->getOption('dry-run');
        if ($this->dryRun) {
            $this->output->writeln('<info>Dryrun Enabled</info>');
        }
        $mailbox = 'INBOX';
        $this->mbox   = '{' . $this->imapHost . ':' . $this->imapPort . '/' . $this->imapTls . '}' . $mailbox;
        $this->stream = imap_open($this->mbox, 'clientlytest@hotmail.com', 'Cliently2017');
        $this->searchMessages();
    }

    
    /**
     * @param string $criteria
     * @see http://php.net/manual/ru/function.imap-search.php
     * @return array|bool
     */
    public function searchMessages($folder = 'INBOX', $criteria = '', $emails = [], $is_incoming = TRUE)
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

        $status = imap_status($this->stream, $this->mbox, SA_UIDVALIDITY);
        $ids = imap_search($this->stream, trim($criteria), SE_UID);
        $stack = array();

        if (!$ids)
            return FALSE;

        $headers = imap_fetch_overview($this->stream, implode($ids, ','), FT_UID);
        foreach ($headers as $header) {
            $msg = $this->buildEmailHeaderArray($header);
            $msg['body'] = imap_fetchbody($this->stream, $msg['msgno'], FT_UID);
            $msg['full_uid'] = isset($status->uidvalidity) ? $status->uidvalidity  : '' .':' . $folder . ':' . $msg['uid'];
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
        print_r($head);
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
        $str   = '';

        for ( $p = 0 ; $p < count($parts) ; $p++ ) {
            $ch   = $parts[ $p ]->charset;
            $part = $parts[ $p ]->text;
            if ( $ch !== 'default' ) $str .= mb_convert_encoding($part, 'UTF-8', $ch);
            else $str .= $part;
        }

        return $str;
    }
}
