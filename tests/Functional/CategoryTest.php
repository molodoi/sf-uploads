<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryTest extends WebTestCase
{
    public function testIfCreateCategoryIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/category/new');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $form = $crawler->filter('form[name=category]')->form([
            'category[title]' => "Functional WebTestCase CategoryTest",
        ]);

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
