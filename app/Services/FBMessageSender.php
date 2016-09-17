<?php

namespace App\Services;

use GuzzleHttp\Client;

class FBMessageSender
{
    public function send($recipientId, $message)
    {
        $client = new Client(['base_uri' => 'https://graph.facebook.com/v2.6/']);
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
        $client = new Client(['base_uri' => 'https://graph.facebook.com/v2.6/']);
        $client->request(
            'POST',
            'me/messages',
            [
                'query' => ['access_token' => getenv('PAGE_ACCESS_TOKEN')],
                'json' => ['recipient' => ['id' => $recipientId]] + $array
            ]
        );
    }
}
