<?php

namespace Uauth\Test;

use Buzz;

class BasicTest extends \PHPUnit_Framework_TestCase
{
    const BASE_URL = 'http://localhost:8080';

    protected function createBrowser($user = null, $password = null)
    {
        $browser = new Buzz\Browser();

        if ($user) {
            $browser->addListener(new Buzz\Listener\BasicAuthListener($user, $password));
        }

        return $browser;
    }

    public function testSimple()
    {
        $browser = $this->createBrowser();
        $response = $browser->head(self::BASE_URL.'/simple.php');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertRegExp('/realm="My restricted Area"/', $response->getHeader('WWW-Authenticate'));

        $browser  = $this->createBrowser('unknown_user', '****');
        $response = $browser->head(self::BASE_URL.'/simple.php');
        $this->assertEquals(401, $response->getStatusCode());

        $browser  = $this->createBrowser('jon', 'snow');
        $response = $browser->get(self::BASE_URL.'/simple.php');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Welcome jon', $response->getContent());
    }

    public function testVerify()
    {
        $browser  = $this->createBrowser('unknown_user', '****');
        $response = $browser->get(self::BASE_URL.'/verify.php');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertRegExp('/realm="Bob zone"/', $response->getHeader('WWW-Authenticate'));
        $this->assertEquals('Unauthorized', $response->getContent());

        $browser  = $this->createBrowser('bob', 'bobby');
        $response = $browser->get(self::BASE_URL.'/verify.php');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Welcome bob', $response->getContent());
    }
}
