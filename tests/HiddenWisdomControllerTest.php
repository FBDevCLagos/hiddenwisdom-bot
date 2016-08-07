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
}
