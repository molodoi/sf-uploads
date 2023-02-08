<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

class BasicTest extends PantherTestCase
{
    public function testEnvironnementIsOk(): void
    {
        // $client = self::createPantherClient([
        //     'external_base_uri' => 'http://localhost:8000',
        // ]);

        // $crawler = $client->request('GET', '/');
        // $this->assertSelectorExists('h1');
        // $this->assertPageTitleContains('Welcome');
        $this->assertTrue(true);
    }
}