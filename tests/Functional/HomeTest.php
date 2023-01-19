<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    public function testHomeIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'https://127.0.0.1:8000/');
        $this->assertSame(1, $crawler->filter('html:contains("Welcome")')->count());
    }
}
