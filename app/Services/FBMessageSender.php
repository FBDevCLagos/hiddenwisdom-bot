<?php

namespace App\Services;

use GuzzleHttp\Client;

class FBMessageSender
{
    protected $base_uri = 'https://graph.facebook.com/v2.6/';

    public function send($recipientId, $message)
    {
        $client = new Client(['base_uri' => $this->base_uri]);
        $client->request(
            'POST',
            'me/messages',
            [
                'query' => ['access_token' => getenv('PAGE_ACCESS_TOKEN')],
                'json' => [
                    'recipient' => [
                        'id' => $recipientId
                    ],
                    'message' => $message
                ]
            ]
        );
    }

    public function sendArray($recipientId, $array)
    {
        $client = new Client(['base_uri' => $this->base_uri]);
        $client->request(
            'POST',
            'me/messages',
            [
                'query' => ['access_token' => getenv('PAGE_ACCESS_TOKEN')],
                'json' => ['recipient' => ['id' => $recipientId]] + $array
            ]
        );
    }

    public function sendPostBackActions()
    {
        $client = new Client(['base_uri' => $this->base_uri]);
        $client->request(
            'POST',
            'me/messages',
            [
                'query' => ['access_token' => getenv('PAGE_ACCESS_TOKEN')],
                'json' => [
                    'setting_type' => 'call_to_actions',
                    'thread_state' => 'existing_thread',
                    'call_to_actions' => [
                        [
                            'type' => "postback",
                            'title' => "Help",
                            'payload' => 'HELP'
                        ],
                        [
                            'type' => 'postback',
                            'title' =>'Random Quote',
                            'payload'=>"RANDOM_QUOTE"
                        ],
                    ]
                ]
            ]
        );
    }
}
