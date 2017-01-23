<?php
namespace AppBundle\Service;

use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Client;
use AppBundle\Entity\Msg;

/**
 * Description of TwitterService
 *
 * @author Josh Murphy
 */
class TwitterService
{

    protected $connection;

    /**
     *
     *
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->key = '7F0IX0juiVE8BfThmLB4vA';
        $this->secret = 'FCrFk2Zcg8q0g5X4kI8HLJDtkYkGNDsqsxomT6TaU';
        $this->em = $this->container->get('doctrine')->getManager();
    }

    public function authUser(User $user)
    {
        //TODO: Use constant for type
        $integration = $this->em->getRepository('AppBundle:Integration')->findOneBy(['userId' => $user, 'type' => 2]);
        if (!$integration) {
            return false;
        }
        //$token = '553966583-vhOVD5lsYAfX5MQ39vS09jQqFyf6X9GPusEsFmMF';
        //$secret = 'CQWSM8v28YIiUgm7AFxkBuRzihgBBPjMqHXo9XazI';
        $values = json_decode($integration->getValues());
        $this->connection = new TwitterOAuth($this->key, $this->secret, $values->access_token, $values->access_token_secret);
        $content = $this->connection->get("account/verify_credentials");
    }

    public function getUserInfo(Client $client)
    {
        $social = json_decode($client->getSocial());
        if ($social && isset($social->twitter)) {
            return $this->connection->get('users/show', array('screen_name' => $social->twitter));
        }
        return false;
    }

    public function twitterFollow($client_id)
    {
        $result = $this->connection->post('friendships/create', array('user_id' => $client_id, 'follow' => TRUE));
        if (isset($result->errors)) {
            return FALSE;
        }

        $result_refined = [];
        $result_refined['status_id'] = $client_id . '~' . Msg::TYPE_TWITTER_FOLLOW . '~1~' . time();
        $result_refined['text'] = '';
        $result_refined['created_at'] = time();
        $result_refined['is_own'] = TRUE;
        $result_refined['status_type'] = 'twitter_follow';
        return $result_refined;
    }

    public function twitterUnfollow($client_id)
    {
        $result = $this->connection->post('friendships/destroy', array('user_id' => $client_id, 'follow' => TRUE));
        if (isset($result->errors)) {
            return FALSE;
        }

        $result_refined = [];
        $result_refined['status_id'] = $client_id . '~' . Msg::TYPE_TWITTER_UNFOLLOW . '~0~' . time();
        $result_refined['text'] = '';
        $result_refined['created_at'] = time();
        $result_refined['is_own'] = TRUE;
        $result_refined['status_type'] = 'twitter_unfollow';
        return $result_refined;
    }

    public function handleAction($user, $client, $type = 1, $description = '', $source_id = 0)
    {
        $integration = $this->em->getRepository('AppBundle:Integration')->findOneBy(['userId' => $user->getId(), 'type' => 2]);
        if (!$integration) {
            return false;
        }
        $values = json_decode($integration->getValues());
        $source = $client->getSource();
        $extra = json_decode($source->getExtra());
        $msg_data = [];
        $this->authUser($user);
        switch ($type) {
            case 'twitter_tweet':
                break;
            case 'twitter_retweet':
                break;  
            case 'twitter_favorite':
                break;     
            case 'twitter_direct':
                break;      
            case 'twitter_quote':
                break;            
            case 'twitter_follow':
                $msg_data = $this->twitterFollow($client->getSource()->getCode());
                break;
            case 'twitter_unfollow':
                $msg_data = $this->twitterUnfollow($client->getSource()->getCode());
                break;
        }
        return $msg_data;
        exit;
        $twitter_values = json_decode($integration['values'], TRUE);
        $source_extra = json_decode($client_twitter['source_extra'], TRUE);
        $user_source_extra = json_decode($integration['source_extra'], TRUE);
        $user_source_code = $integration['source_code'];

        $types = array(
            'twitter_tweet',
            'twitter_retweet',
            'twitter_follow',
            'twitter_unfollow',
            'twitter_favorite',
            'twitter_direct',
            'twitter_quote'
        );

        if ($type === 'twitter_retweet' OR $type === 'twitter_follow' OR $type === 'twitter_unfollow' OR $type === 'twitter_favorite')
            $description = FALSE;

        if ($type === NULL OR $description === NULL) {
            return ajax_code(App_core::CODE_BAD_REQUEST, TRUE);
        } else {
            if (!in_array($type, $types)) {
                return ajax_code(App_core::CODE_BAD_INPUT, TRUE);
            } else {
                $authed = $this->twitter->auth($twitter_values['access_token'], $twitter_values['access_token_secret']);
                if (!$authed) {
                    $this->app_core->log(App_core::CODE_MAIL_ERROR, 'twitter failed (' . $integration['code'] . '), client_id = ' . $client_id);
                } else {

                    $own_username = $user_source_extra['username'];
                    $own_fullname = $user_source_extra['fullname'];
                    $own_id = $user_source_code;
                    $own_ava = PROTOCOL . '://' . $user_source_extra['avatar'];
                    $lead_username = $source_extra['username'];
                    $lead_fullname = $source_extra['fullname'];
                    $lead_id = $client_twitter['source_code'];
                    $lead_ava = PROTOCOL . '://' . $source_extra['avatar'];

                    $own = array(
                        'id' => $own_id,
                        'username' => $own_username,
                        'fullname' => $own_fullname,
                        'avatar' => $own_ava
                    );

                    $lead = array(
                        'id' => $lead_id,
                        'username' => $lead_username,
                        'fullname' => $lead_fullname,
                        'avatar' => $lead_ava
                    );

                    $recipient = $lead;
                    $sender = $own;

                    if ($type === 'twitter_tweet') {
                        $msg_info = $this->Msg_model->get_msg_info($id);
                        $msg_data = $this->twitter->create('@' . $source_extra['username'] . ' ' . $description, $msg_info['code']);
                    } elseif ($type === 'twitter_retweet') {
                        $msg_info = $this->Msg_model->get_msg_info($id);

                        $msg_data = $this->twitter->retweet($msg_info['code']);
                    } elseif ($type === 'twitter_follow') {
                        $msg_data = $this->twitter->follow($lead_id, $client_id);
                    } elseif ($type === 'twitter_unfollow') {
                        $msg_data = $this->twitter->unfollow($lead_id, $client_id);
                    } elseif ($type === 'twitter_favorite') {
                        // $this->twitter->create('@' . $source_extra['username'] . ' ' . $description);
                    } elseif ($type === 'twitter_direct') {
                        $msg_data = $this->twitter->direct_create($lead_id, $description);
                    } elseif ($type === 'twitter_quote') {
                        $msg_info = $this->Msg_model->get_msg_info($id);
                        // $msg_data = $this->twitter->create($description . ' https://twitter.com/statuses/' . $msg_info['code']);
                        $msg_data = $this->twitter->create($description . ' https://twitter.com/' . $msg_info['sender_source_code'] . '/status/' . $msg_info['code']);
                    }

                    if (!$msg_data)
                        return ajax_code(App_core::CODE_INTERNAL_ERROR, TRUE);
                    else {


                        $type_code = Msg_model::$types[$type];
                        $row = array(
                            'description' => $msg_data['text'],
                            'code' => $msg_data['status_id'],
                            'email' => $client_twitter['source_id'],
                            'handle' => $integration['handle'],
                            'integration_type' => 2,
                            'created_at' => $msg_data['created_at'],
                            'sender_source_id' => $integration['source_id'],
                            'recipient_source_id' => $client_twitter['source_id'],
                            'is_own' => 1,
                            'type' => $type_code
                        );

                        if ($type === 'twitter_retweet' OR $type === 'twitter_quote') {
                            $row['attachments'] = $msg_data['attachments'];
                        }

                        $msg_id = $this->Msg_model->create_msg($_SESSION['user_id'], $client_id, 0, $row);
                        if (!$msg_id) {
                            return ajax_code(App_core::CODE_DB_ERROR, 'Msg_model->create_msg');
                        } else {
                            if ($type === 'twitter_follow') {
                                $this->Client_twitter_model->update_by_client_id($client_id, array('is_followed' => 1));
                            } elseif ($type === 'twitter_unfollow') {
                                $this->Client_twitter_model->update_by_client_id($client_id, array('is_followed' => 0));
                            }

                            $msg = array(
                                'id' => $msg_id,
                                'description' => $msg_data['text'],
                                'is_own' => TRUE,
                                'type' => $type,
                                'recipient' => $recipient,
                                'sender' => $sender,
                                'created_at' => $msg_data['created_at']
                            );
                            if ($type === 'twitter_retweet' OR $type === 'twitter_quote') {
                                $msg['attachments'] = json_decode($msg_data['attachments'], TRUE);
                                $msg['attachments']['retweet']['sender']['avatar'] = PROTOCOL . '://' . $msg['attachments']['retweet']['sender']['avatar'];
                            }

                            return ajax_output(App_core::CODE_CREATED, $msg, TRUE);
                        }
                    }
                }
            }
        }
    }

    public function scan()
    {
        $token = '553966583-vhOVD5lsYAfX5MQ39vS09jQqFyf6X9GPusEsFmMF';
        $secret = 'CQWSM8v28YIiUgm7AFxkBuRzihgBBPjMqHXo9XazI';
        $this->connection = new TwitterOAuth($this->key, $this->secret, $token, $secret);
        $content = $this->connection->get("account/verify_credentials");
        $twitter_ids = $this->em->getRepository('AppBundle:ClientTwitter')->findAll();
        print_r($twitter_ids);
        exit;
        print_r($content);
        $user_id = 553966583;
        $data = array(
            'user_id' => $user_id,
            'count' => 200,
            'stringify_ids' => TRUE
        );
        $friends = $this->connection->get('friends/ids', $data);
        print_r($friends);
        $followers = $result = $this->connection->get('followers/ids', $data);
        print_r($followers);
        $data = array(
            'count' => 200,
            'include_rts' => TRUE // this actually doesn't provide any retweeted tweets, that belong to the authenticated user. ps: and even if belong to others
        );
        $clients1 = $this->connection->get('statuses/mentions_timeline', $data); //$this->twitter->mentions_timeline($collected_ids_ready, $client_twitter['twitter_code']); // TODO: need check this for FALSE
        print_r($clients1);
        $data = array(
            'user_id' => $user_id,
            'count' => 200
        );
        $clients2 = $this->connection->get('statuses/user_timeline', $data); //$this->twitter->user_timeline($collected_ids_ready, $client_twitter['twitter_code'], $retrieve_retweets); // TODO: need check this for FALSE
        print_r($clients2);
        $data = array(
            'count' => 200,
        );
        $direct_messages = $this->connection->get('direct_messages', $data); //$this->twitter->direct_messages($collected_ids_ready); // TODO: need check this for FALSE
        print_r($direct_messages);
        $direct_messages_sent = $this->connection->get('direct_messages/sent', $data);
        print_r($direct_messages_sent);
        ; //$this->twitter->direct_messages_sent($collected_ids_ready); // TODO: need check this for FALSE
    }

    public function scanTwitter()
    {
        //get last 50 twitter accounts that hasn't been updated recently

        $this->load->model(array('Client_twitter_model', 'Msg_model'));
        $start_time = time();

        $client_twitters = $this->Client_twitter_model->get_client_twitters_ids();

        if (!is_array($client_twitters)) {
            return ajax_code(App_core::CODE_FAILURE, TRUE);
        } else {
            $msg_count = 0;
            $msg_added_count = 0;
            $twitter_scan_count = 0;
            $last_twitter_code = FALSE;
            $collected_ids = array();
            $collected_ids_ready = array();
            $collected_clients = array();
            $collected_clients_ready = array();
            foreach ($client_twitters as $key => $client_twitter) {
                if ($client_twitter['twitter_values']) {
                    $twitter_values = json_decode($client_twitter['twitter_values'], TRUE);
                    $this->load->library('twitter', array('initial_auth' => FALSE));

                    if ($last_twitter_code !== $client_twitter['twitter_code']) {
                        $authed = $this->twitter->auth($twitter_values['access_token'], $twitter_values['access_token_secret']);
                    } else
                        $authed = TRUE;

                    $collected_ids[$client_twitter['source_id']] = $client_twitter['source_code'];
                    $collected_clients[$client_twitter['source_id']] = $client_twitter;

                    if (!isset($client_twitters[$key + 1]) OR $client_twitters[$key + 1]['twitter_code'] !== $client_twitter['twitter_code']) {
                        $collected_ids_ready = $collected_ids;
                        $collected_ids = array();
                        $collected_clients_ready = $collected_clients;
                        $collected_clients = array();
                    } else {
                        $last_twitter_code = $client_twitter['twitter_code'];
                        continue;
                    }

                    if (!$authed) {
                        $this->app_core->log(App_core::CODE_MAIL_ERROR, 'twitter failed (' . $client_twitter['twitter_code'] . ')');
                    } else {
                        if ($collected_ids_ready) {
                            $this->load->model(array('Workspace_member_model', 'Source_model', 'Deal_model', 'Pipeline_model', 'Stage_model', 'Company_model', 'Client_model', 'Client_twitter_model', 'Deal_workflow_model'));

                            $twitter_scan_count++;

                            $friends = $this->twitter->friends_ids($collected_ids_ready, $client_twitter['twitter_code']); // TODO: need check this for FALSE
                            $followers = $this->twitter->followers_ids($collected_ids_ready, $client_twitter['twitter_code']); // TODO: need check this for FALSE

                            $clients1 = $this->twitter->mentions_timeline($collected_ids_ready, $client_twitter['twitter_code']); // TODO: need check this for FALSE
                            $retrieve_retweets = TRUE;
                            $clients2 = $this->twitter->user_timeline($collected_ids_ready, $client_twitter['twitter_code'], $retrieve_retweets); // TODO: need check this for FALSE

                            $direct_messages = $this->twitter->direct_messages($collected_ids_ready); // TODO: need check this for FALSE
                            $direct_messages_sent = $this->twitter->direct_messages_sent($collected_ids_ready); // TODO: need check this for FALSE

                            $current_time = time();


                            foreach ($followers as $client_key => $client) {
                                $client_source_id = array_search($client_key, $collected_ids_ready);
                                if ((bool) $collected_clients_ready[$client_source_id]['is_follower'] !== $client) {
                                    $type = $client ? Msg_model::$types['twitter_follow'] : Msg_model::$types['twitter_unfollow'];
                                    $row = array(
                                        'code' => $collected_clients_ready[$client_source_id]['client_id'] . '~' . $type . '~' . 0 . '~' . $current_time,
                                        'email' => $client_source_id,
                                        'handle' => $client_twitter['twitter_handle'],
                                        'integration_type' => 2,
                                        'created_at' => $current_time,
                                        'sender_source_id' => $client_source_id,
                                        'recipient_source_id' => $client_twitter['twitter_source'],
                                        'is_own' => 0,
                                        'type' => $type
                                    );

                                    $msg_id = $this->Msg_model->create_msg($client_twitter['owner_id'], $collected_clients_ready[$client_source_id]['client_id'], 0, $row, FALSE);
                                    if ($msg_id === NULL) {
                                        return ajax_code(App_core::CODE_DB_ERROR);
                                    } elseif ($msg_id === TRUE) {
                                        $is_follower = (int) $client;
                                        $this->Client_twitter_model->update_by_client_id($collected_clients_ready[$client_source_id]['client_id'], array('is_follower' => $is_follower));
                                    } elseif ($msg_id === FALSE) {
                                        
                                    } else {
                                        $is_follower = (int) $client;
                                        $this->Client_twitter_model->update_by_client_id($collected_clients_ready[$client_source_id]['client_id'], array('is_follower' => $is_follower));

                                        $msg_added_count++;
                                        $this->Client_twitter_model->increment_counter($collected_clients_ready[$client_source_id]['client_id']);
                                        $stop_on_respond = json_decode($collected_clients_ready[$client_source_id]['stop_on_respond'], TRUE);
                                        if ((in_array('all', $stop_on_respond) OR in_array('twitter_follow', $stop_on_respond)) && $type === Msg_model::$types['twitter_follow']) {
                                            $this->Deal_workflow_model->stop_by_client_id($collected_clients_ready[$client_source_id]['client_id']);
                                        }
                                    }
                                    $msg_count++;
                                }
                            }

                            foreach ($friends as $client_key => $client) {
                                $client_source_id = array_search($client_key, $collected_ids_ready);
                                if ((bool) $collected_clients_ready[$client_source_id]['is_followed'] !== $client) {
                                    $type = $client ? Msg_model::$types['twitter_follow'] : Msg_model::$types['twitter_unfollow'];
                                    $row = array(
                                        'code' => $collected_clients_ready[$client_source_id]['client_id'] . '~' . $type . '~' . 1 . '~' . $current_time,
                                        'email' => $client_source_id,
                                        'handle' => $client_twitter['twitter_handle'],
                                        'integration_type' => 2,
                                        'created_at' => $current_time,
                                        'sender_source_id' => $client_twitter['twitter_source'],
                                        'recipient_source_id' => $client_source_id,
                                        'is_own' => 1,
                                        'type' => $type
                                    );

                                    $msg_id = $this->Msg_model->create_msg($client_twitter['owner_id'], $collected_clients_ready[$client_source_id]['client_id'], 0, $row, FALSE);
                                    if ($msg_id === NULL) {
                                        return ajax_code(App_core::CODE_DB_ERROR);
                                    } elseif ($msg_id === TRUE) {
                                        $is_followed = (int) $client;
                                        $this->Client_twitter_model->update_by_client_id($collected_clients_ready[$client_source_id]['client_id'], array('is_followed' => $is_followed));
                                    } elseif ($msg_id === FALSE) {
                                        
                                    } else {
                                        $is_followed = (int) $client;
                                        $this->Client_twitter_model->update_by_client_id($collected_clients_ready[$client_source_id]['client_id'], array('is_followed' => $is_followed));

                                        $msg_added_count++;
                                        // Not needed, cause its always own event
                                        //$this->Client_twitter_model->increment_counter($collected_clients_ready[$client_source_id]['client_id']);
                                    }
                                    $msg_count++;
                                }
                            }

                            for ($i = 0; $i <= 3; $i++) {
                                if ($i === 0)
                                    $clients = $clients1;
                                elseif ($i === 1)
                                    $clients = $clients2;
                                elseif ($i === 2)
                                    $clients = $direct_messages;
                                elseif ($i === 3)
                                    $clients = $direct_messages_sent;
                                if ($clients) {
                                    foreach ($clients as $client_key => $client) {
                                        $client_source_id = array_search($client_key, $collected_ids_ready);
                                        foreach ($client as $msg) {

                                            $row = array(
                                                'name' => '',
                                                'description' => $msg['text'],
                                                'code' => $msg['status_id'],
                                                'email' => $client_source_id,
                                                'handle' => $client_twitter['twitter_handle'],
                                                'integration_type' => 2,
                                                'created_at' => $msg['created_at'],
                                            );

                                            if ($msg['is_own']) {
                                                $row['sender_source_id'] = $client_twitter['twitter_source'];
                                                $row['recipient_source_id'] = $client_source_id;
                                                $row['is_own'] = 1;
                                            } else {
                                                $row['sender_source_id'] = $client_source_id;
                                                $row['recipient_source_id'] = $client_twitter['twitter_source'];
                                                $row['is_own'] = 0;
                                            }

                                            if (isset(Msg_model::$types[$msg['status_type']])) {
                                                $row['type'] = Msg_model::$types[$msg['status_type']];
                                            }

                                            $row['attachments'] = $msg['attachments'];

                                            $msg_id = $this->Msg_model->create_msg($client_twitter['owner_id'], $collected_clients_ready[$client_source_id]['client_id'], 0, $row, FALSE);
                                            if ($msg_id === NULL) {
                                                return ajax_code(App_core::CODE_DB_ERROR);
                                            } elseif ($msg_id === TRUE) {
                                                
                                            } elseif ($msg_id === FALSE) {
                                                
                                            } else {
                                                $msg_added_count++;
                                                if (!$msg['is_own']) {
                                                    $this->Client_twitter_model->increment_counter($collected_clients_ready[$client_source_id]['client_id']);

                                                    $stop_on_respond = json_decode($collected_clients_ready[$client_source_id]['stop_on_respond'], TRUE);
                                                    if ($stop_on_respond && (in_array('all', $stop_on_respond) OR in_array('twitter_retweet', $stop_on_respond)) && ($msg['status_type'] === 'twitter_retweet' OR $msg['status_type'] === 'twitter_quote')) {
                                                        $this->Deal_workflow_model->stop_by_client_id($collected_clients_ready[$client_source_id]['client_id']);
                                                    } elseif ($stop_on_respond && (in_array('all', $stop_on_respond) OR in_array('twitter_reply', $stop_on_respond)) && ($msg['status_type'] === 'twitter_tweet')) {
                                                        $this->Deal_workflow_model->stop_by_client_id($collected_clients_ready[$client_source_id]['client_id']);
                                                    } elseif ($stop_on_respond && (in_array('all', $stop_on_respond) OR in_array('twitter_direct', $stop_on_respond)) && ($msg['status_type'] === 'twitter_direct')) {
                                                        $this->Deal_workflow_model->stop_by_client_id($collected_clients_ready[$client_source_id]['client_id']);
                                                    }
                                                }
                                            }
                                            $msg_count++;
                                        }
                                    }
                                }
                            }

                            $collected_ids_ready = array();
                        }
                    }
                }

                $last_twitter_code = $client_twitter['twitter_code'];
            }

            $finish_time = time();

            $seconds = $finish_time - $start_time;

            $hours = floor($seconds / 3600);
            $mins = floor(($seconds - ($hours * 3600)) / 60);
            $secs = floor($seconds % 60);



            echo 'Total messages: ' . $msg_count;
            echo "\n" . 'Total messages added: ' . $msg_added_count;
            echo "\n" . 'Total users: ' . $twitter_scan_count;
            echo "\n" . 'Time ellapsed: ' . $hours . 'h ' . $mins . 'm ' . $secs . 's';
        }
    }
}
