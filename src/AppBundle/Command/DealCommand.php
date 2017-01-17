<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\AdSet;
use Carbon\Carbon;
use FacebookAds\Object\Values\BidTypes;
use Guff\LegacyBundle\Service\AdCreativeService;

/**
* 
* 
 * DealCommand
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
class DealCommand extends ContainerAwareCommand
{
    /*
     * {@inheritdoc}
     */
    protected $fbAdService;
    protected $dryRun;

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
            ->setName('guff:customads')
            ->setDescription('Creates guided custom Facebook ads to test for optimal configuration')
            ->addOption(
                'access_token', 'a', InputOption::VALUE_OPTIONAL,
                'Facebook Access Token',
                'CAAVXMUdpUFwBAImdbsProLq08uGgOMdIFemPPm0pzIwpsiejXvUlUyFER78jNRhAoIQit8ZAeiNqBas7zgszuBxCAmVyXud4bZCuri5mZBpuWwZBpHVbBYQSvc89Qr73oS3O8QpBvTCmte9q3GZAhlrv3GZCIzmssqehAmiS0t5AShzFyi7MRPYtyN4Y1G4vUZD'
            )
            ->addOption(
                'app_id', null, InputOption::VALUE_OPTIONAL,
                'Facebook App ID', '1503244046651484'
            )
            ->addOption(
                'app_secret', null, InputOption::VALUE_OPTIONAL,
                'Facebook App Secret', 'bc8ebd4660fca31a428c3825ab626fc6'
            )
            ->addOption(
                'account_id', null, InputOption::VALUE_OPTIONAL,
                'Ad Account ID', '1012885225428018'
            )
            ->addOption(
                'graph_id', null, InputOption::VALUE_OPTIONAL,
                'Graph ID', '595467550503123'
            )
            ->addOption(
                'site_id', null, InputOption::VALUE_OPTIONAL, 'Site ID',
                null
            )
            ->addOption(
                'post_id', null, InputOption::VALUE_OPTIONAL, 'Post ID',
                null
            )
            ->addOption(
                'post_url', null, InputOption::VALUE_OPTIONAL,
                'Post URL', null
            )
            ->addOption(
                'ad_image', null, InputOption::VALUE_OPTIONAL,
                'Ad Image', null
            )
            ->addOption(
                'ad_title', null, InputOption::VALUE_OPTIONAL,
                'Ad Title', null
            )
            ->addOption(
                'ad_description', null, InputOption::VALUE_OPTIONAL,
                'Ad Description', null
            )
            ->addOption(
                'autoconfig1', null, InputOption::VALUE_NONE,
                'Automated Configuration 1'
            )
            ->addOption(
                'ad_bid', null, InputOption::VALUE_OPTIONAL,
                'Ad Bid Type', null
            )
            ->addOption(
                'ad_bid_value', null, InputOption::VALUE_OPTIONAL,
                'Ad Bid Value', null
            )
            ->addOption(
                'ad_budget_span', null, InputOption::VALUE_OPTIONAL,
                'Ad Budget Span', null
            )
            ->addOption(
                'ad_budget', null, InputOption::VALUE_OPTIONAL,
                'Ad Budget', null
            )
            ->addOption(
                'ad_target_gender', null, InputOption::VALUE_OPTIONAL,
                'Ad Targeting for Gender', null
            )
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry Run')
            ->addOption(
                'cross_reference', null, InputOption::VALUE_NONE,
                'Cross reference a json file with db entries'
            )
            ->addOption('data', null, InputOption::VALUE_OPTIONAL, 'JSON file')
            ->setHelp('Use the guided wizard to create a custom Facebook Ad or use the command line options to manually create the Ad');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input   = $input;
        $this->output  = $output;
        $dialog        = $this->getHelper('dialog');
        $appId         = $this->input->getOption('app_id');
        $appSecret     = $this->input->getOption('app_secret');
        $accessToken   = $this->input->getOption('access_token');
        $accountId     = 'act_'.$this->input->getOption('account_id');
        $graphId       = $this->input->getOption('graph_id');
        $siteId        = $this->input->getOption('site_id');
        $postId        = $this->input->getOption('post_id');
        $postUrl       = $this->input->getOption('post_url');
        $adImage       = $this->input->getOption('ad_image');
        $adTitle       = $this->input->getOption('ad_title');
        $adDescription = $this->input->getOption('ad_description');
        $bidType       = $this->input->getOption('ad_bid');
        $bidValue      = $this->input->getOption('ad_bid_value');
        $adBudget      = $this->input->getOption('ad_budget');
        $adBudgetSpan  = $this->input->getOption('ad_budget_span');
        $targetGender  = $this->input->getOption('ad_target_gender');
        $this->dryRun  = $this->input->getOption('dry-run');
        $autoConfig1   = $this->input->getOption('autoconfig1');
        $jsonFile      = $input->getOption('data');

        $this->fbAdService       = $this->getContainer()->get('empire.fb_ad_service');
        $this->fbManageService   = $this->getContainer()->get('empire.fb_manage_ad_service');
        $this->fbAdService->init($accountId, $graphId, $this->dryRun, 0);
        $this->fbAdService->initFacebookAdsApi($appId, $appSecret, $accessToken);
        $this->fbAdService->initFacebookGraphApi(
            $appId, $appSecret,
            $accessToken
        );
        $this->postService       = $this->getContainer()->get('guff_legacy.post_service');
        $this->siteService       = $this->getContainer()->get('guff_legacy.site_service');
        $this->adcreativeService = $this->getContainer()->get('guff_legacy.adcreative_service');
        $startTime               = Carbon::parse('Jun 27, 2015 12:01am')->minute(1);

        if ($input->getOption('cross_reference') && $jsonFile) {
            $content   = file_get_contents($jsonFile);
            $postsData = json_decode($content, true);
            $ids       = [];
            if ($postsData) {
                foreach ($postsData as $p) {
                    $ids[] = $p['id'];
                }
                $posts = $this->fbManageService->crossReferenceNotUsed($ids);
            }
            //$this->output->writeln(print_r($ids,1));
            $count = 0;
            foreach ($posts as $post) {
                if ($count == 50) {
                    break;
                }
                $this->output->writeln($count);
                $captions = $this->adcreativeService->getAdCreativesByPostId($post->getPost()->getId());
                $images   = $this->fbManageService->getPostImagesFromDatabase($post->getPost());
                if (empty($captions) || empty($images)) {
                    $this->output->writeln("Incomplete package");
                    continue;
                }
                $data                 = [];
                $data['account_id']   = $accountId;
                $data['site_id']      = 1;
                $data['post_id']      = $post->getPost()->getId();
                $data['post_slug']    = $post->getPost()->getSlug();
                $data['title']        = $captions[0]['caption'];
                $data['description']  = $captions[0]['description'];
                $data['image']        = $images[0];
                $data['admanager_id'] = 2;
                $data                 = $this->getAdProfile(1, $data);
                print_r($data);
                $this->customAdTest($data);
                $count++;
            }
            return;
        }

        if ($jsonFile) {
            $content = file_get_contents($jsonFile);
            $posts   = json_decode($content, true);
            //$this->output->writeln(print_r($ids,1));
            $count   = 0;
            foreach ($posts as $post) {
                print_r($post);
                foreach ($post['images'] as $image) {
                    $data                 = [];
                    $data['id']           = $post['id'];
                    $data['account_id']   = $accountId;
                    $data['site_id']      = 1;
                    $data['post_id']      = $post['id'];
                    $data['post_slug']    = $post['slug'].'/202';
                    $data['title']        = $post['titles'][0];
                    $data['description']  = $post['descriptions'][0];
                    $data['start_days']   = 1;
                    $data['end_days']     = 60;
                    $data['image']        = $image;
                    $data['admanager_id'] = 2;
                    $data                 = $this->getAdProfile(3, $data);
                    print_r($data);
                    $this->fbAdService->createAd($data);
                    sleep(45);
                }
            }
            return;
        }
        //Check Site Information
        if (!$siteId) {
            $site = $dialog->askAndValidate(
                $output, '<question>Please enter the Site Id</question>',
                /**
                * 
                * 
                 * $answer
                 * Insert description here
                 *
                 * @return
                 *
                 * @access
                 * @static
                 * @see
                 * @since
                 
                */
                function ($answer) {
                    $site = $this->siteService->getSiteById($answer);
                    if (!$site) {
                        throw new \Exception('<error>This doesn\'t appear to be a valid post</error>');
                    }
                    return $site;
                }, false, '1'
            );
        } elseif ($siteId) {
            $site = $this->siteService->getSiteById($siteId);
        }
        if (!$site) {
            throw new \Exception('<error>This doesn\'t appear to be a valid site</error>');
        }

        //Check Post Information
        if (!$postId && !$postUrl) {
            $post = $dialog->askAndValidate(
                $output,
                '<question>Please enter the id or url of the post</question>',
                /**
                * 
                * 
                 * $answer
                 * Insert description here
                 *
                 * @return
                 *
                 * @access
                 * @static
                 * @see
                 * @since
                 
                */
                function ($answer) {
                    $postCheck = false;
                    if (is_numeric($answer)) {
                        $postCheck = $this->postService->getPostById($answer, false);
                    } else {
                        $slug      = explode("/", $answer);
                        $postCheck = $this->postService->getPostBySlug(
                            $slug[3],
                            false
                        );
                    }
                    if (!$postCheck) {
                        throw new \Exception('<error>This doesn\'t appear to be a valid post</error>');
                    }
                    return $postCheck;
                }, false, '1234'
            );
        } elseif ($postId) {
            $post = $this->postService->getPostById($postId, false);
        } elseif ($postUrl) {
            $slug = explode("/", $postUrl);
            $post = $this->postService->getPostBySlug($slug[3], false);
        }
        if (!$post) {
            throw new \Exception('<error>This doesn\'t appear to be a valid post</error>');
        }
        $captions           = $this->adcreativeService->getAdCreativesByPostId($post->getPost()->getId());
        $ad                 = [];
        $ad['titles']       = [];
        $ad['descriptions'] = [];
        foreach ($captions as $caption) {
            $ad['titles'][]       = $caption[AdCreativeService::ADCREATIVE_TITLE];
            $ad['descriptions'][] = $caption[AdCreativeService::ADCREATIVE_DESCRIPTION];
        }
        //We have a valid post, lets validate image/title/description info
        if (!$adImage) {
            $images   = $this->fbManageService->getPostImagesFromDatabase($post->getPost());
            $response = $dialog->select(
                $output,
                '<question>Please choose an image from the list</question>',
                $images, 0
            );
            $adImage  = $images[$response];
        }
        if (!$adImage) {
            throw new \Exception('<error>This doesn\'t appear to be a valid image</error>');
        }
        if (!$adTitle) {
            if (empty($ad['titles'])) {
                $adTitle = $dialog->ask(
                    $output,
                    '<question>Please enter a title for the ad: </question>',
                    'Testing'
                );
            } else {
                $response = $dialog->select(
                    $output,
                    '<question>Please choose a title from the list</question>',
                    $ad['titles'], 0
                );
                $adTitle  = $ad['titles'][$response];
            }
        }
        if (!$adDescription) {
            if (empty($ad['descriptions'])) {
                $adDescription = $dialog->ask(
                    $output,
                    '<question>Please enter a description for the ad: </question>',
                    'Testing'
                );
            } else {
                $response      = $dialog->select(
                    $output,
                    '<question>Please choose a dscription from the list</question>',
                    $ad['descriptions'], 0
                );
                $adDescription = $ad['descriptions'][$response];
            }
        }
        if ($autoConfig1) {
            $data                = [];
            $data['account_id']  = $accountId;
            $data['site_id']     = $site->getId();
            $data['post_id']     = $post->getPost()->getId();
            $data['post_slug']   = $post->getPost()->getSlug();
            $data['title']       = $adTitle;
            $data['description'] = $adDescription;
            $data['image']       = $adImage;
            $this->createAutomatedCombo1($data);
            return;
        }
        $answers  = ['Automated combo 1', 'Manual'];
        $response = $dialog->select(
            $output, '<question>How would you like to creat the ad</question>',
            $answers, 0
        );
        $answer   = $answers [$response];
        if ($answer == 'Automated combo 1') {
            $data                = [];
            $data['account_id']  = $accountId;
            $data['site_id']     = $site->getId();
            $data['post_id']     = $post->getPost()->getId();
            $data['post_slug']   = $post->getPost()->getSlug();
            $data['title']       = $adTitle;
            $data['description'] = $adDescription;
            $data['image']       = $adImage;
            $this->createAutomatedCombo1($data);
            return;
        }
        if (!$bidType) {
            $bidTypes = ['CLICKS', 'ACTIONS', 'REACH', 'SOCIAL'];
            $response = $dialog->select(
                $output,
                '<question>Please choose a bid type from the list</question>',
                $bidTypes, 0
            );
            $bidType  = $bidTypes[$response];
        }

        if (!$bidValue) {
            $bidValue = $dialog->ask(
                $output,
                '<question>Please enter the bid value for '.$bidType.' of the ad: </question>',
                '10'
            );
        }
        if (!$adBudgetSpan) {
            $spanTypes    = ['LIFETIME', 'DAILY'];
            $response     = $dialog->select(
                $output,
                '<question>Please choose an ad budget span from the list</question>',
                $spanTypes, 0
            );
            $adBudgetSpan = $spanTypes[$response];
        }
        if (!$adBudget) {
            $adBudget = $dialog->ask(
                $output,
                '<question>Please enter the budget in cents for '.$adBudgetSpan.' of the ad: </question>',
                '10'
            );
        }
        if (!$targetGender) {
            $targetTypes = ['Male', 'Female', 'All'];
            $response    = $dialog->select(
                $output,
                '<question>Please choose a gender to target</question>',
                $targetTypes, 0
            );
            switch ($response) {
            case 'Male':
                $targetGender = 0;
                break;
            case 'Female':
                $targetGender = 1;
                break;
            }
        }
        $data                   = [];
        $data['account_id']     = $accountId;
        $data['site_id']        = $site->getId();
        $data['post_id']        = $post->getPost()->getId();
        $data['post_slug']      = $post->getPost()->getSlug();
        $data['title']          = $adTitle;
        $data['description']    = $adDescription;
        $data['image']          = $adImage;
        $data['bid_type']       = $bidType;
        $data['bid_value']      = $bidValue;
        $data['ad_budget_span'] = $adBudgetSpan;
        $data['ad_budget']      = $adBudget;
        $data['target_gender']  = $targetGender;

        $this->customAdTest($data);
    }

    /**
     *
     * @param type $adprofile_idUsed to provide a Ad Set profile for use in new ads
     * TODO: Use db
     */
    public function getAdProfile($adprofile_id, $data = [])
    {
        switch ($adprofile_id) {
        case 1:
            $data['bid_type']          = 'CLICKS';
            $data['bid_value']         = 4;
            $data['ad_budget_span']    = 'LIFETIME';
            $data['ad_budget']         = 5000 * 60;
            $data['target_gender']     = null;
            $data['campaign_category'] = 'Testing';
            break;
        case 2:
            $data['bid_type']          = 'ACTIONS';
            $data['bid_value']         = 4;
            $data['ad_budget_span']    = 'LIFETIME';
            $data['ad_budget']         = 5000 * 60;
            $data['target_gender']     = null;
            $data['campaign_category'] = 'Testing';
        case 3:
            $data['adprofile_id']      = 3;
            $data['bid_type']          = 'ACTIONS';
            $data['bid_value']         = 4;
            $data['ad_budget_span']    = 'LIFETIME';
            $data['budget']            = 2500 * 60;
            $data['target_gender']     = null;
            $data['campaign_type']     = 'managed';
            break;
        }
        return $data;
    }

    /**
* 
* 
     * createAutomatedCombo1
     * Insert description here
     *
     * @param $adData
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     
*/
    public function createAutomatedCombo1($adData)
    {
        $budgetTypes = ['DAILY' => 12500, 'LIFETIME' => 25000];
        $bidTypes    = ['CLICKS' => 5, 'SOCIAL' => 4];

        $targetGender = null;
        foreach ($budgetTypes as $adBudgetSpan => $adBudget) {
            foreach ($bidTypes as $bidType => $bidValue) {
                $data                   = [];
                $data['account_id']     = $adData['account_id'];
                $data['post_id']        = $adData['post_id'];
                $data['post_slug']      = $adData['post_slug'];
                $data['title']          = $adData['title'];
                $data['description']    = $adData['description'];
                $data['image']          = $adData['image'];
                $data['bid_type']       = $bidType;
                $data['bid_value']      = $bidValue;
                $data['ad_budget_span'] = $adBudgetSpan;
                $data['ad_budget']      = $adBudget;
                $data['target_gender']  = $targetGender;
                print_r($data);
                $this->customAdTest($data);
            }
        }
    }

    /**
     *
     * @param type $data
     * @param type $data['account_id']     Account Id to create the
     * @param type $data['post_id']        Post Id
     * @param type $data['post_slug']      Post Slug used for the ad link
     * @param type $data['title']          Title of the Ad
     * @param type $data['description']    Description for the ad
     * @param type $data['image']          Ad to be used in the creative
     * @param type $data['bid_type']       One of the four available bid types
     * @param type $data['bid_value']      Value applied for the bid type
     * @param type $data['ad_budget_span'] Lifetime/Daily
     * @param type $data['ad_budget']      Budget of the ad
     * @param type $data['target_gender']  Potential selected gender to target
     */
    public function customAdTest($data)
    {
        $timeZone = 'America/Los_Angeles';
        $this->fbAdService->enableLogger();
        //Create Ad Campaign
        if (isset($data['campaign_category'])) {
            switch ($data['campaign_category']) {
            case 'Testing':
            default:
                $startTime = Carbon::tomorrow($timeZone)->minute(1)->toIso8601String();
                $endTime   = Carbon::tomorrow($timeZone)->addDays(60)->minute(-1)->toIso8601String();

                $adSetName    = 'Guff Testing - '.$data['title'].' - '.$data['bid_type'].' - '.$data['ad_budget_span'];
                $campaignName = 'Guff Testing - '.$data['title'].' - '.$data['bid_type'].' - '.$data['ad_budget_span'];
                $newCampaign  = $this->fbAdService->createAdCampaign(
                    $campaignName,
                    'managed'
                );
                break;
            }
        } else {
            $startTime = Carbon::tomorrow($timeZone)->minute(1)->toIso8601String();
            $endTime   = Carbon::tomorrow($timeZone)->addDays(2)->minute(-1)->toIso8601String();

            $adSetName    = 'Guff R&D - '.$data['title'].' - '.$data['bid_type'].' - '.$data ['ad_budget_span'];
            $campaignName = 'Guff R&D - '.$data['title'].' - '.$data['bid_type'].' - '.$data['ad_budget_span'];
            $newCampaign  = $this->fbAdService->createAdCampaign(
                $campaignName,
                'research'
            );
        }

        $campaignId = $newCampaign->getData()[CampaignFields::ID];

        //Create Ad Set
        $adsetData = [
            AdSetFields::NAME => $adSetName,
            AdSetFields::BID_TYPE => BidTypes::BID_TYPE_ABSOLUTE_OCPM,
            AdSetFields::CAMPAIGN_STATUS => AdSet::STATUS_ACTIVE,
            AdSetFields::CAMPAIGN_GROUP_ID => $campaignId,
            AdSetFields::TARGETING => [
                'age_min' => 18,
                'age_max' => 65,
                'geo_locations' => [
                    'countries' => [
                        'US',
                    ],
                    'location_types' => ['recent', 'home'],
                ],
                'page_types' => ['feed'],
            ],
            AdSetFields::START_TIME => $startTime,
            AdSetFields::END_TIME => $endTime,
        ];

        if ($data['target_gender']) {
            $adsetData [AdSetFields::TARGETING]['gender'] = $data['target_gender'];
        }

        if ($data['ad_budget_span'] == 'DAILY') {
            $adsetData[AdSetFields::DAILY_BUDGET] = $data['ad_budget'];
        } else {
            $adsetData[AdSetFields::LIFETIME_BUDGET] = $data['ad_budget'];
        }

        $adsetData[AdSetFields::BID_INFO] = [
            $data['bid_type'] => (int) $data['bid_value'],
        ];

        $adSet = new AdSet(null, $data['account_id']);

        $adSet->create($adsetData);
        $adSetId              = $adSet->getData()[AdSetFields::ID];
        $adsetData['site_id'] = $data['site_id'];
        $adsetData['post_id'] = $data['post_id'];
        $adsetData['id']      = $adSetId;
        $this->fbAdService->storeAdSet($adsetData);
        //Create Ad Group Objects
        $this->output->writeln(PHP_EOL.'Creating Ad Group for '.$data['image'].'. Ad Set: '.$adSetName);
        $postRoute            = 'guff_post_slug_catch';
        $link                 = 'http://'.$this->getContainer()->getParameter('site_domain').$this->getContainer()->get('router')->generate(
            $postRoute,
            ['slug' => $data['post_slug']]
        );

        $adGroupData = [
            'title' => $data['title'],
            'description' => $data['description'],
            'link' => $link,
            'image' => $data['image'], 'name_data' => $adSetName,
        ];

        $adGroup = $this->fbAdService->createAdGroupObjects(
            $adGroupData,
            $adSetId, $campaignId
        );
    }
}
