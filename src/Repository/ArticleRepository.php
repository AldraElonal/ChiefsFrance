<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return Query
     */
    public function findAllQuery() : Query{
        return  $this->createQueryBuilder('a')
            ->orderBy('a.created_at', 'DESC')
            ->getQuery();
    }


    /**
     * @return Query
     */
    public function findAllVisibleQuery() : Query{
        return  $this->createQueryBuilder('a')
            ->where('a.status = 1')// a check plus tard
            ->orderBy('a.created_at', 'DESC')
            ->getQuery();

    }
}
