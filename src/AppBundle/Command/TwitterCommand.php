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

/**
  =
 *
 * @author Josh Murphy
 *
 * @todo Add configure help
 * @todo Document each request
 */
class TwitterCommand extends ContainerAwareCommand
{

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
            ->setName('cliently:twitter')
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
        //get all twitter integrations
        $this->getContainer()->get('app.twitter_service')->scan();
    }

}
