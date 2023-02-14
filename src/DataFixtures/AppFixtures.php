<?php

/*
 * This file is part of the Symfony package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@test.fr')
            ->setFirstname('test')
            ->setLastname('test')
            ->setRoles(['ROLE_ADMIN', 'ROLE_USER'])
            ->setIsVerified(true)
            ->setPassword(
                $this->hasher->hashPassword($user, 'test@test.fr')
            );
        $manager->persist($user);

        for ($i = 0; $i < 9; ++$i) {
            $user = new User();
            $user->setEmail('test'.$i.'@test.fr')
                ->setFirstname('firstname'.$i)
                ->setLastname('lastname'.$i)
                ->setIsVerified(true)
                ->setPassword(
                    $this->hasher->hashPassword($user, 'test'.$i.'@test.fr')
                );

            $manager->persist($user);
        }

        $category1 = new Category();
        $category1->setTitle('Symfony 6');
        // $category1->setSlug('symfony');
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setTitle('Laravel 9');
        $manager->persist($category2);

        for ($i = 1; $i < 20; ++$i) {
            $category = new Category();
            $category->setTitle('Cat '.$i);
            $manager->persist($category);
        }

        $this->addReference('symfony-cat', $category1);
        $this->addReference('laravel-cat', $category2);

        for ($i = 1; $i < 20; ++$i) {
            $post = new Post();
            $post->setTitle('Post '.$i);
            $post->setSlug('post-'.$i);
            $post->setContent('Content post '.$i.' lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. ');
            if (0 !== $i % 2) {
                $post->setCategory($this->getReference('symfony-cat'));
            } else {
                $post->setCategory($this->getReference('laravel-cat'));
            }
            $post->setUser($user);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
