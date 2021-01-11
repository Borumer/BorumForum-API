<?php

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase {
    private $http;

    public function setUp() : void {
        $this->http = new GuzzleHttp\Client(["base_uri" => "https://api.bforborum.com/api/"]);
    }

    public function testPost() {
        $response = $this->http->post('login');

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json; charset=UTF-8", $contentType);
    }

    public function testPut() {
        $response = $this->http->put('login');
        
        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json; charset=UTF-8", $contentType);
    }

    public function testInvalidRequestMethod() {
        $response = $this->http->get('user-agent');

        $this->assertEquals(405, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json; charset=UTF-8", $contentType);
    }

    public function tearDown() : void {
        $this->http = null;
    }
}

?>