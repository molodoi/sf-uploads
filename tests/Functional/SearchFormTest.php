<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SearchFormTest extends WebTestCase
{
    public function testSearchFormIsSuccessful(): void
    {
        $client = static::createClient();

        // Log user
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($testUser);


        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app.home'));

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame(1, $crawler->filter('html:contains("Welcome")')->count());

        $form = $crawler->filter('form[name=searchform]')->form([
            'q' => "Post 11",
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

       $this->assertSame(0, $crawler->filter('html:contains("Search results : Post 11")')->count());

       $this->assertRouteSame('app.search');

    }
}
