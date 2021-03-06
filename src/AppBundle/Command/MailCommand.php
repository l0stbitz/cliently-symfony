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
        $mailService = $this->getContainer()->get('app.email_service');
        $mailService->searchAllImapIntegrations();
        /*$mailbox = 'INBOX';
        $this->mbox   = '{' . $this->imapHost . ':' . $this->imapPort . '/' . $this->imapTls . '}' . $mailbox;
        $this->stream = imap_open($this->mbox, 'clientlytest@hotmail.com', 'Cliently2017');
        $this->searchMessages();*/
        
    }
}
