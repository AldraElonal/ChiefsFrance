<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */


    public function findArticleCommentAllowed(Article $article)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();


        $q = $qb->select('c.id','c.content','c.created_at')
            ->from(Comment::class, 'c')

            ->where('c.Article = :article_id')
            ->setParameter('article_id', $article->getId())
            ->andWhere('c.status >= :status')
            ->setParameter('status',2)
            ->join(User::class,'u')
            ->where('u.id = c.user')

//            ->orderBy('p.created_at', 'DESC')
            ->getQuery();

        return $q->getResult();
    }


    public function findOneByIdJoinedToCategory($productId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p, c
        FROM App\Entity\Product p
        INNER JOIN p.category c
        WHERE p.id = :id'
        )->setParameter('id', $productId);

        return $query->getOneOrNullResult();
    }


    /*
     *     public function findAllVisibleQuery() : Query{
            return  $this->createQueryBuilder('a')
                ->where('a.status = 1')// a check plus tard
                ->orderBy('a.created_at', 'DESC')
                ->getQuery();

        }
     */


    /*
     public function findByExampleField($value)
     {
         return $this->createQueryBuilder('c')
             ->andWhere('c.exampleField = :val')
             ->setParameter('val', $value)
             ->orderBy('c.id', 'ASC')
             ->setMaxResults(10)
             ->getQuery()
             ->getResult()
         ;
     }
     */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
