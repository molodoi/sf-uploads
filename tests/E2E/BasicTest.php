<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

class BasicTest extends PantherTestCase
{
    public function testEnvironnementIsOk(): void
    {
        
        $this->assertTrue(true);
    }
}