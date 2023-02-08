<?php

/*
 * This file is part of the Symfony package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllPostsQuery(): Query
    {
        
        return $this->createQueryBuilder('p')
            // ->addSelect('p', 'c', 'i')
            // ->leftJoin('p.category', 'c')
            // ->leftJoin('p.featuredImage', 'i')
            // ->orderBy('p.updatedAt', 'DESC')
            // ->getQuery();
            ->addSelect('p', 'c')
            ->leftJoin('p.category', 'c')
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery();
    }

    public function getAllPostsByUserQuery($user): Query
    {
        $query = $this->createQueryBuilder('p')
            // ->addSelect('p', 'c', 'i')
            // ->leftJoin('p.category', 'c')
            // ->leftJoin('p.featuredImage', 'i')
            // ->orderBy('p.updatedAt', 'DESC')
            // ->getQuery();
            ->addSelect('p', 'c')
            ->leftJoin('p.category', 'c');
            if(!in_array('ROLE_ADMIN', $user->getRoles())){
                $query->where('p.user = :user')
                ->setParameter('user', $user->getId()->toBinary());
            }
            return $query->orderBy('p.updatedAt', 'DESC')
            ->getQuery();
    }

    /** @return Post[] **/
    public function getLastPosts(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
//            ->andWhere('p.user = :val')
//            ->setParameter('val', $user)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
