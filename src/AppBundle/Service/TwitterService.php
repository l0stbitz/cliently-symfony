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
