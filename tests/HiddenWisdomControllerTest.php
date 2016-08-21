<?php

class HiddenWisdomControllerTest extends TestCase
{
    public function testVerifyInvalid()
    {
        $response = $this->call('GET', '/webhook', ['hub_verify_token' => 'InvalidToken']);

         $this->assertEquals(403, $response->status());
    }

    public function testVerifyValid()
    {
        $response = $this->call('GET', '/webhook', [
                'hub_verify_token' => getenv('VALIDATION_TOKEN'),
                'hub_mode' => 'subscribe'
                ]);

         $this->assertEquals(200, $response->status());
    }

    public function testhandleMessageRequestReturnsMessage()
    {
        FBMessageSender::shouldReceive('send')
                    ->once()
                    ->andReturn('');
        $msg = 'Hello, World';
        $response = $this->call('POST', '/webhook', 
                [
                    'entry' => [
                        [
                            'messaging' => [
                                [
                                    'message' => [
                                        'text' => $msg                                        
                                    ],
                                    'sender' => [
                                        'id' => '419419'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);
         $this->assertEquals(200, $response->status());
         $this->assertEquals($msg, $response->getContent());
    }

    public function testhandleMessageReturnsBadRequest()
    {
        $response = $this->call('POST', '/webhook', []);
         $this->assertEquals(400, $response->status());
    }
}
