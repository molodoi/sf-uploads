<?php
declare(strict_types=1);
namespace App\Tests\Unit;

use App\Entity\Post;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
{
    public function getEntity(): Category
    {

        return (new Category())->setTitle('UnitTestCategory Title');
    }

    public function testConstructNomicalCase(): void
    {
        $category = $this->getEntity();
        self::assertInstanceOf(Category::class, $category);
    }

    public function testEntityConstraintsIsValidTitle(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $category = $this->getEntity();

        $errors = $container->get('validator')->validate($category);
        $this->assertCount(0, $errors);
    }

    public function testEntityConstraintsInvalidTitle(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $category = $this->getEntity();
        $category->setTitle('');
        
        $errors = $container->get('validator')->validate($category);
        $this->assertCount(2, $errors);
    }
}
