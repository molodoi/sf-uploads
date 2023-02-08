<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    public function testHomeIsSuccessful(): void
    {
        $client = static::createClient();
        // Log user
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@test.fr');
        $client->loginUser($testUser);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app.home'));
        $this->assertSame(1, $crawler->filter('html:contains("Welcome")')->count());
    }
}
