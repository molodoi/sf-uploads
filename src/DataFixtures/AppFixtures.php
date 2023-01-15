<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category1 = new Category();
        $category1->setTitle('Symfony 6');
        //$category1->setSlug('symfony');
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setTitle('Laravel 9');
        $manager->persist($category2);

        for ($i = 1; $i < 20; $i++) {
            $category = new Category();
            $category->setTitle('Cat ' . $i);
            $manager->persist($category);
        }

        $this->addReference('symfony-cat', $category1);
        $this->addReference('laravel-cat', $category2);

        for ($i = 1; $i < 20; $i++) {
            $post = new Post();
            $post->setTitle('Post ' . $i);
            $post->setSlug('post-' . $i);
            $post->setContent('Content post ' . $i . ' lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. ');
            if($i % 2 != 0){
                $post->setCategory($this->getReference('symfony-cat'));
            }else{
                $post->setCategory($this->getReference('laravel-cat'));
            }
            $manager->persist($post);
        }

        $manager->flush();
    }
}
