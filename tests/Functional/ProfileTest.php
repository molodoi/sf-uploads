<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileTest extends WebTestCase
{
    public function testIfProfilUpdateIsSuccessful(): void
    {
        $client = static::createClient();

        // Log user
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($testUser);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET, 
            $urlGenerator->generate('app.profile.index', ['id' => $testUser->getId()])
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame(1, $crawler->filter('html:contains("Profile")')->count());

        $form = $crawler->filter('form[name=profile]')->form([
            'profile[lastname]' => "test",
            'profile[firstname]' => "test",
            'profile[email]' => "test@test.fr",
            'profile[password]' => "test@test.fr",
        ]);

       $client->submit($form);

       $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    //    $client->followRedirect();

       $this->assertSame(1, $crawler->filter('html:contains("Profile")')->count());
       $this->assertSelectorTextContains('h1.mt-3', 'Profile');

       $this->assertRouteSame('app.profile.index');
    }
}
