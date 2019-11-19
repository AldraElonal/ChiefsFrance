<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Constraints\Timezone;

class ArticleController extends AbstractController
{


    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    private $localTimeZone;
    /**
     * ArticleController constructor.
     * @param ArticleRepository $articleRepository
     * @param UserRepository $userRepository
     */
    public function __construct(ArticleRepository $articleRepository, UserRepository $userRepository)
    {
$this->localTimeZone = new \DateTimeZone("Europe/Paris");
        $this->articleRepository = $articleRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/articles", name="articles_all")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
//        $articles =  $this->articleRepository->findAll();
        $articles = $paginator->paginate($this->articleRepository->findAllVisibleQuery(),
            $request->query->getInt('page', 1),
            4);

//        foreach ($articles as $article) {
//            $user = $this->userRepository->findOneBy(['id' => $article->getUserId()]);
//            $article->setUserId($user);
//        }

        return $this->render('Front/articles.html.twig', [
            'articles' => $articles,
            'timezone' => $this->localTimeZone
        ]);
    }

    /**
     * @Route("/article/{id}", name="article")
     * @param int $id
     * @param CommentRepository $commentRepository
     * @param ObjectManager $manager
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function article(int $id, CommentRepository $commentRepository, ObjectManager $manager, Request $request): Response
    {

        $article = $this->articleRepository->findOneBy(['id' => $id]);
        $user = $this->userRepository->findOneBy(['id' => $article->getUserId()]);
        $article->setUserId($user);
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);

        $formComment->handleRequest($request);
        if ($formComment->isSubmitted() AND $formComment->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setCreatedAt(new \DateTime());
            $comment->setArticle($article);
            $comment->setStatus(1);
            $manager->persist($comment);
            $manager->flush();
            unset($formComment);
            unset($comment);
            $comment = new Comment();
            $formComment = $this->createForm(CommentType::class, $comment);

        }

        $articleComments = $commentRepository->findBy(["Article" => $id]);

//        dump($article->getCreatedAt()->getTimezone());

//        findBy(["Article" => $id,'status' => 2], ["created_at" => "DESC"]);
        //findArticleCommentAllowed($id);
        //findBy(["Article" => $id,'status' => ], ["created_at" => "DESC"]);

        return $this->render('front/article.html.twig', [
            'article' => $article,
            'formComment' => $formComment->createView(),
            'comments' => $articleComments,
            'timezone' => $this->localTimeZone
        ]);
    }

    /**
     * @param int $id
     * @param CommentRepository $commentRepository
     * @param int $page
     * @return JsonResponse
     * @Route("/comments/{id}/{page}", name="article_comments")
     */
    public function articleComments(int $id, CommentRepository $commentRepository, int $page = 0)
    {

        $nbComments = $commentRepository->count(["Article" => $id]);
        if($nbComments < ($page) * 4){ // page trop élevée => on affiche la première page
            $page = 0;
        }
        $comments = $commentRepository->findBy(["Article" => $id]);
        $commentsToDisplay = array_slice($comments,2*$page,4);
        $response = $this->render("Front/comments.html.twig", [
            "comments" => $commentsToDisplay,
            'timezone' => $this->localTimeZone
        ]);
        return new JsonResponse($response->getContent());
    }


    /**
     * @Route("/signalComment/{id}", name="article_signalComment")
     */

    public function signalComment(int $id, CommentRepository $commentRepository, ObjectManager $manager)
    {

        if ($this->getUser() != null) {
            $comment = $commentRepository->findOneBy(['id' => $id]);
            $comment->setStatus(1);
            $manager->flush();
     return $this->json([
         "message" => "commentaire signalé avec succès"
            ],200);
        } else {
            return $this->json([
                "message" => 'Vous devez etre connecté pour signaler un commentaire'
            ],403);
        }


    }
}