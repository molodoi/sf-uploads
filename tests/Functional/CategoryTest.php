<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryTest extends WebTestCase
{
    public function testIfCreateCategoryIsSuccessful(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        $crawler = $client->request('GET', $urlGenerator->generate('app.category.new'));

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame(1, $crawler->filter('html:contains("Create Category")')->count());

        // $form = $crawler->filter('form[name=category]')->form([
        //     'category[title]' => "Functional WebTestCase CategoryTest",
        // ]);

//        $client->submit($form);

//        $this->assertTrue($client->getResponse()->isRedirection());
//        sleep(2);
//
//        $client->followRedirect();
//
//        $this->assertSame(1, $crawler->filter('html:contains("All categories")')->count());
//
//        $this->assertRouteSame('app.category.index');
    }
}
