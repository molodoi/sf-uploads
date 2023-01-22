<?php
declare(strict_types=1);
namespace App\Tests\Unit;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostTest extends KernelTestCase
{
    public function getEntity(): Post
    {
        return (new Post())->setTitle('UnitTestPost Title');
    }

    public function testConstructNomicalCase(): void
    {
        $post = $this->getEntity();
        self::assertInstanceOf(Post::class, $post);
    }

    public function testEntityConstraintsIsValidTitle(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $post = $this->getEntity();

        $errors = $container->get('validator')->validate($post);

        $this->assertCount(0, $errors);
    }

    public function testEntityConstraintsInvalidTitle(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $post = $this->getEntity();
        $post->setTitle('');

        $errors = $container->get('validator')->validate($post);
        $this->assertCount(2, $errors);
    }
}
