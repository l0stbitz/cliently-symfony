<?php
namespace AppBundle\Service;

use Google_Client;
use Google_Service_Analytics;
use Google_Auth_AssertionCredentials;
use Carbon\Carbon;

//use AppBundle\Service\RedisService as CS;

/**
 * Description of GoogleAnalytics
 *
 */
class GoogleAnalyticsService
{

    private $gaSiteId;
    private $profileId;
    private $analytics;

    public function __construct($container)
    {
        $this->container = $container;
    }

    private function initClient()
    {
        $client = new Google_Client();
        $client->setDeveloperKey('f0d3bf83c658962df0cdd7903955f5ab2880443a');
        $this->analytics = new Google_Service_Analytics($client);
        $service_account_email = 'cliently-analytics@cliently-analytics.iam.gserviceaccount.com';
        //service id: f0d3bf83c658962df0cdd7903955f5ab2880443a
        // password: notasecret

        $keyPath = $this->container->get('kernel')->getRootDir() . '/config/google_service_account.json';
        $client->setAuthConfig($keyPath);
        $client->setScopes([Google_Service_Analytics::ANALYTICS_READONLY]);

    }

    public function getEvents($start = null, $end = null)
    {
        $this->initClient();
        //$redis = $this->container->get('empire.redis_service');
        if ($start == null) {
            $start = '7daysAgo';
        }

        if ($end == null) {
            $end = 'today';
        }

        /* $key = CS::PREFIX . CS::SP . CS::GACMPPVPSPD .  CS::SP . $this->profileId .
          CS::SP . $campaignInfo['campaign'] . CS::SP . $start . CS::SP . $end;
          $data = json_decode($redis->get($key));
          $timestamp = $redis->getScore($key);
          if (!$data || time() - $timestamp > 86400) { */
        $data = $this->analytics->data_ga->get('ga:115719727', $start, $end, 'ga:uniqueEvents', [
            'filters' => 'ga:eventCategory==email',
            'sort' => '-ga:date',
            'dimensions' => 'ga:eventCategory, ga:eventLabel, ga:eventAction, ga:date',
            'max-results' => 100
            ]
        );

        $data = $data->toSimpleObject();
        print_r($data);
        
    }

    public function getCampaignGraph($campaignInfo, $start = null, $end = null, $site, $highchart = true)
    {
        $this->initClient($site);
        $redis = $this->container->get('empire.redis_service');

        if ($start == null) {
            $start = '7daysAgo';
        }

        if ($end == null) {
            $end = 'today';
        }

        $key = CS::PREFIX . CS::SP . CS::GACMP . CS::SP . $this->profileId .
            CS::SP . $campaignInfo['campaign'] . CS::SP . $start . CS::SP . $end;
        $data = json_decode($redis->get($key));
        $timestamp = $redis->getScore($key);
        if (!$data || time() - $timestamp > 86400) {
            $data = $this->analytics->data_ga->get('ga:' . $this->profileId, $start, $end, 'ga:sessions,ga:avgSessionDuration,ga:bounces,ga:pageviewsPerSession,ga:percentNewSessions', [
                'filters' => 'ga:campaign==dm_f_' . $campaignInfo['campaign'],
                'dimensions' => 'ga:date',
                //'output' => 'dataTable',
                'max-results' => 100
            ]);

            $data = $data->toSimpleObject();
            $redis->sortedSetAdd(CS::PREFIX . CS::SP . CS::TIMESTAMP, time(), $key);
            $redis->set($key, json_encode($data));
        }
        //print_r($data);exit;
        if ($highchart) {
            $graphData = [];
            $cols = $data->columnHeaders;
            foreach ($data->rows as $r) {
                $time = strtotime($r[0]) * 1000;
                for ($x = 1; $x < count($cols); $x++) {
                    $val = $r[$x];
                    $type = isset($cols[$x]->dataType) ? $cols[$x]->dataType : $cols[$x]['dataType'];
                    $name = isset($cols[$x]->name) ? $cols[$x]->name : $cols[$x]['name'];
                    switch ($type) {
                        case 'INTEGER':
                            $val = (int) $r[$x];
                        case 'FLOAT':
                            $val = (float) $r[$x];
                        case 'TIME':
                            $val = (float) $r[$x];
                        case 'PERCENT':
                            $val = (float) $r[$x];
                    }
                    $graphData[substr($name, 3)][] = [$time, $val];
                }
            }

            return $graphData;
        }
        return $data;
    }

    public function getCampaignPageViewsPerSessionPerDesigns($campaignInfo, $start = null, $end = null, $site)
    {
        $this->initClient($site);
        $redis = $this->container->get('empire.redis_service');
        if ($start == null) {
            $start = '7daysAgo';
        }

        if ($end == null) {
            $end = 'today';
        }

        $key = CS::PREFIX . CS::SP . CS::GACMPPVPSPD . CS::SP . $this->profileId .
            CS::SP . $campaignInfo['campaign'] . CS::SP . $start . CS::SP . $end;
        $data = json_decode($redis->get($key));
        $timestamp = $redis->getScore($key);
        if (!$data || time() - $timestamp > 86400) {
            $data = $this->analytics->data_ga->get('ga:' . $this->profileId, $start, $end, 'ga:pageviewsPerSession, ga:avgSessionDuration', [
                'filters' => 'ga:campaign==dm_f_' . $campaignInfo['campaign'],
                'dimensions' => 'ga:customVarValue2',
                'max-results' => 100
                ]
            );

            $data = $data->toSimpleObject();

            $redis->sortedSetAdd(CS::PREFIX . CS::SP . CS::TIMESTAMP, time(), $key);
            $redis->set($key, json_encode($data));
        }

        $pvps = [];

        if ($data->totalResults > 0) {
            foreach ($data->rows as $r) {
                $pvps[$r[0]] = [(float) $r[1], (float) $r[2]];
            }
        }

        return $pvps;
    }
}
