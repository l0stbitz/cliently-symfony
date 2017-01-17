<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
* 
* 
 * ClientCommand
 * Insert description here
 *
 * @category
 * @package
 * @author
 * @copyright
 * @license
 * @version
 * @link
 * @see
 * @since
 
*/
class ClientCommand extends ContainerAwareCommand
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
            ->setName('guff:archive')
            ->setDescription('Archives statisitical infomation the various components of a Facebook Ad Account')
            ->setHelp('TODO: Fill this in');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;


        $archiveService = $this->getContainer()->get('empire.archive_service');

        $archiveService->archiveAdStats();
        $archiveService->archiveAccountStats();
        $archiveService->archivePostInsights();
    }
}
