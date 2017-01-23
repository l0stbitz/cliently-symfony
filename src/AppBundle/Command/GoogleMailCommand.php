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
use Aws\S3\S3Client;
use EmpireBundle\Utility\MimeType;
use EmpireBundle\Entity\ImageMedia;
use EmpireBundle\Entity\VideoMedia;
use EmpireBundle\Entity\ListPostMember;

/**
 * =
 *
 * @author Josh Murphy
 *
 * @todo Add configure help
 * @todo Document each request
 */
class GoogleMailCommand extends ContainerAwareCommand
{
    /*
     * {@inheritdoc}
     */

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
            ->setName('cliently:google:mail')
            ->setDescription('')
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
        $this->input = $input;
        $this->output = $output;
        $this->dryRun = $this->input->getOption('dry-run');
        if ($this->dryRun) {
            $this->output->writeln('<info>Dryrun Enabled</info>');
        }     
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $gService = $this->getContainer()->get('app.gmail_service');
        $integration = $em->getRepository('AppBundle:Integration')->find(4);
        $gService->initClient($integration);
        $message = $gService->sendMessage(['subject'=>'subject as;dlfkjasd;klfjas;klfjasd;lkfj','from'=>'josh@cliently.com','to'=>'test@lostbitz.com','message'=>'alksdjfa;lksdjflk;asdjf ;lkasdjf;lkasdjf;lkasjdf']);
        print_r($message);
    }

  
}
