<?php
declare(strict_types=1);
namespace App\Tests\Unit;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
{
    public function getEntity(): Category
    {
        return (new Category())->setTitle('UnitTestCategory Title');

    }

    public function testEntityConstraintsIsValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $category = $this->getEntity();

        $errors = $container->get('validator')->validate($category);

        $this->assertCount(1, $errors);
    }

    public function testEntityConstraintsInvalidTitle()
    {
        self::bootKernel();
        $container = static::getContainer();

        $category = $this->getEntity();
        $category->setTitle('');

        $errors = $container->get('validator')->validate($category);
        $value = 'Minimum 2 characters';
        $this->assertCount(3, $errors);
    }
}
