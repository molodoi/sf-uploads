<?php

namespace App\Tests\Functional\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegisterTest extends WebTestCase
{

    public function testRegistrationWorks(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('security.register')
        );

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertSame(1, $crawler->filter('html:contains("Register")')->count());

        $newRegisteredUser = 'test'.uniqid().'@test.fr';

        $form = $crawler->filter('form[name=registration]')->form([
            'registration[lastname]' => $newRegisteredUser,
            'registration[firstname]' => $newRegisteredUser,
            'registration[email]' => $newRegisteredUser,
            'registration[plainPassword][first]' => $newRegisteredUser,
            'registration[plainPassword][second]' => $newRegisteredUser,
            'registration[agreeTerms]' => true
        ]);
        
        $client->submit($form);
        sleep(5);
        $this->assertQueuedEmailCount(1);  
        $email = $this->getMailerMessage();

        $this->assertEmailHtmlBodyContains($email, 'activated your account');
        $this->assertEmailTextBodyContains($email, $newRegisteredUser);      

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('security.login');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testForgetPasswordWorks(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('security.forget_password')
        );

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertSame(1, $crawler->filter('html:contains("Forget password")')->count());

        $form = $crawler->filter('form[name=forget_password]')->form([
            'forget_password[email]' => 'test@test.fr'
        ]);
        
        $client->submit($form);

        $this->assertEmailCount(1);  
        $email = $this->getMailerMessage();

        $this->assertEmailHtmlBodyContains($email, 'reset password request');
        $this->assertEmailTextBodyContains($email, 'Hi test test');      

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('security.login');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testForgetPasswordWithUnexistingBadMail(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('security.forget_password')
        );

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertSame(1, $crawler->filter('html:contains("Forget password")')->count());

        $form = $crawler->filter('form[name=forget_password]')->form([
            'forget_password[email]' => 'tes@test.fr'
        ]);
        
        $client->submit($form);

        $this->assertEmailCount(0);      

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('security.login');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(1, $crawler->filter('html:contains("Login")')->count());
        $this->assertStringContainsString('A problem has occurred', $client->getResponse()->getContent());
    }
}