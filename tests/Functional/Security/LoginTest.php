<?php

namespace App\Tests\Functional\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginTest extends WebTestCase
{
    public function testLoginWorks(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('security.login')
        );

        $form = $crawler->filter('form[name=login]')->form([
            '_username' => 'test@test.fr',
            '_password' => 'test@test.fr'
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('app.home');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoginWithBadCredentials(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('security.login')
        );

        $form = $crawler->filter('form[name=login]')->form([
            '_username' => 'test@test.fr',
            '_password' => 'badpass'
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('security.login');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorExists('div.alert-warning');
        $this->assertSelectorTextContains('div.alert-warning', 'Invalid credentials.');
    }

    public function testLogoutWorks(): void
    {
        $client = static::createClient();

        /** @var UserRepository */
        $userRepository = $client->getContainer()->get(UserRepository::class);

        /** @var UrlGeneratorInterface */
        $urlGenerator = $client->getContainer()->get('router');

        /** @var User */
        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('security.logout')
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('security.login');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoginWithRememberMe(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface */
        $urlGenerator = $client->getContainer()->get('router');

        $this->assertBrowserNotHasCookie('REMEMBERME');

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('security.login')
        );

        $form = $crawler->filter('form[name=login]')->form([
            '_username' => 'test@test.fr',
            '_password' => 'test@test.fr',
            '_remember_me' => 'on'
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('app.home');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertBrowserHasCookie('REMEMBERME');
    }
}
