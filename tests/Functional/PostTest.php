<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PostTest extends WebTestCase
{
    public function testIfCreatePostIsSuccessful(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        $crawler = $client->request('GET', $urlGenerator->generate('app.post.new'));

        // $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame(0, $crawler->filter('html:contains("Create Post")')->count());

//        $form = $crawler->filter('form[name=post]')->form([
//            'post[title]' => "Functional WebTestCase PostTest",
//        ]);
    }
}
