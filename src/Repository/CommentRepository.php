<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Join;
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

    public function findArticleCommentAllowed(int $article)
    {
        $q = $this->createQueryBuilder('c')
            ->innerJoin('c.user','user',Expr\Join::WITH)
            ->select('c.id,c.content,c.status,c.created_at, user.username')
            ->where('c.Article = :article_id')
            ->setParameter('article_id', $article)
            ->andWhere('c.status >= :status')
            ->setParameter('status',1)
            ->orderBy('c.created_at','DESC')
            ->getQuery();
        return $q->getResult();
    }
}
