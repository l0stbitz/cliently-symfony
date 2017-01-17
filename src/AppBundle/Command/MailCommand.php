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
            ->setName('empire:social:acquire')
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
        $revenue = $this->acquireSocialStats();
        $this->output->writeln('<info>Command empire:social:acquire completed successfully.</info>');
    }

    /**
* 
* 
     * acquireSocialStats
     * Insert description here
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    protected function acquireSocialStats()
    {
        $this->em     = $this->getContainer()->get('doctrine')->getManager();
        $posts = $this->em->getRepository('EmpireBundle:Post')->createQueryBuilder('p')
            ->orderBy('p.socialDate', 'ASC')
            ->setFirstResult(0)
            ->setMaxResults(500)
            ->getQuery()
            ->getResult();
        foreach ($posts as $post) {
            $url = 'http://'.$post->getSite()->getSiteUrl().'/'.$post->getSlug();
            $fb = $this->getFacebookShares($url);
            echo 'FB:' .$fb.PHP_EOL;
            //$tw = $this->getTwitterStats($url);
            echo 'TW:' .$fb.PHP_EOL;
            $bs = $this->getBuzzSumoQuery($url);
            //echo 'TW:' .$fb.PHP_EOL;
            //$post->setFacebookShares($fb);
            //$post->setSocialDate(Carbon::now()->timestamp);
            //$this->em->persist($post);
            //$this->em->flush();
        }
    }
    //TODO: Finish this command!!

    /**
* 
* 
     * getFacebookShares
     * Insert description here
     *
     * @param $url
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    protected function getFacebookShares($url)
    {
        // Create a client
        $client        = new Client();
        echo $url.PHP_EOL;
        
        $url      = 'https://graph.facebook.com/?ids='.$url;
        $response = $client->get($url);
        $data = json_decode($response->getBody()->getContents());
        foreach ($data as $k => $v) {
            if (isset($v->shares)) {
                return $v->shares;
            }
        }
        return 0;
    }

    /**
* 
* 
     * getTwitterStats
     * Insert description here
     *
     * @param $url
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    protected function getTwitterStats($url)
    {
        // Create a client
        $client        = new Client(['cookies' => true]);
        $jar           = new CookieJar;

        //Authenticate
        $url      = 'https://tools.mmedia.com/login/user/authenticate';
        $param    = json_encode(
            [
            'username' => $adNetwork->getUsername(),
            'password' => $adNetwork->getPassword(),
            ]
        );
        $response = $client->post($url, ['body' => $param, 'cookies' => $jar]);
    }

    /**
* 
* 
     * getBuzzSumoQuery
     * Insert description here
     *
     * @param $q
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    protected function getBuzzSumoQuery($q)
    {
        // Create a client
        $client        = new Client();
        echo $q.PHP_EOL;
        $key = '';
        //Authenticate
        $url      = 'http://api.buzzsumo.com/search/influencers.json?q='.urlencode($q).'&'.
            'result_type=relevancy&page=0&ignore_broadcasters=0&'.
            'api_key='.$key;
        $response = $client->get($url);
        print_r(json_decode($response->getBody()->getContents()));
    }
}
