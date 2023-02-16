<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    public function testIfCreateUserIsSuccessful(): void
    {
        $client = static::createClient();

        // Log user
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($testUser);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app.user.new'));

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame(1, $crawler->filter('html:contains("Create User")')->count());

        $form = $crawler->filter('form[name=user]')->form([
            'user[lastname]' => "TestLastname",
            'user[firstname]' => "TestFirstname",
            'user[email]' => "functional@webtestcase.fr",
            'user[password]' => "functional@webtestcase.fr",
            'user[roles]' => ["ROLE_USER"],
            'user[is_verified]' => true,
        ]);

       $client->submit($form);

       $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);

       $client->followRedirect();

       $this->assertSame(1, $crawler->filter('html:contains("All users")')->count());
       $this->assertSelectorTextContains('h1.mt-3', 'All users');

       $this->assertRouteSame('app.user.index');

    }
}
