<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PostTest extends WebTestCase
{
    public function testIfCreatePostIsSuccessful(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app.post.new'));

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame(1, $crawler->filter('html:contains("Create Post")')->count());

       $form = $crawler->filter('form[name=post]')->form([
           'post[title]' => "Functional WebTestCase PostTest",
       ]);

       $client->submit($form);

       $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);

       $client->followRedirect();

       $this->assertSame(1, $crawler->filter('html:contains("All posts")')->count());

       $this->assertRouteSame('app.post.index');
    }
}
