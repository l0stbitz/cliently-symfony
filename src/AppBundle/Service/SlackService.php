<?php

namespace AppBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of SlackService
 *
 * @author Josh Murphy
 */
class SlackService
{

    /**
     *
     *
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->tokenUrl = 'https://hooks.slack.com/services/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';
    }

    /**
* 
* 
     * sendMessage
     * Insert description here
     *
     * @param $channel
     * @param $message
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    public function sendMessage($channel, $message)
    {
        $client   = new Client(['cookies' => true]);
        $jar      = new CookieJar;

        $message = '*'.gethostname().'* '.$message;
        $url      = $this->tokenUrl;
        echo $url.PHP_EOL;
        $response = $client->request('POST', $url, ['body'=>'{"channel": "#dev-alerts", "username": "SymfonyBot", "text": "Muhahahahah", "icon_emoji": ":ghost:"}']);
        //print_r($response->getBody()->getContents());
    }
}
