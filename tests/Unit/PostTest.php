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

    public function testEntityConstraintsIsValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $post = $this->getEntity();

        $errors = $container->get('validator')->validate($post);

        $this->assertCount(1, $errors);
    }

    public function testEntityConstraintsInvalidTitle()
    {
        self::bootKernel();
        $container = static::getContainer();

        $post = $this->getEntity();
        $post->setTitle('');

        $errors = $container->get('validator')->validate($post);
        $value = 'Minimum 2 characters';
        $this->assertCount(3, $errors);
    }
}
