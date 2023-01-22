<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryTest extends WebTestCase
{
    public function testIfCreateCategoryIsSuccessful(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $urlGenerator->generate('app.category.new'));

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame(1, $crawler->filter('html:contains("Create Category")')->count());

        $form = $crawler->filter('form[name=category]')->form([
            'category[title]' => "Functional WebTestCase CategoryTest",
        ]);

       $client->submit($form);

    //    dd($client);

       $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);

       $client->followRedirect();

    //    $this->assertSame(1, $crawler->filter('html:contains("All categories")')->count());
    //    $this->assertSelectorTextContains('h1.mt-3', 'All categories');

    //    $this->assertRouteSame('app.category.index');
    }
}
