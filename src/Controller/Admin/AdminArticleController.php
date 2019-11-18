<?php

namespace App\Controller\Admin;


use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticleController extends AbstractController
{


    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("admin/articles", name="admin_articles")
     */
    public function allArticles()
    {
        $articles = $this->articleRepository->findAll();
        return $this->render("Back/allArticles.html.twig", [
            "articles" => $articles
        ]);
    }


    /**
     * @Route("/admin/articles/editArticle/{id<\d+>?-1}", name="admin_editArticle" )
     */
    public function editArticle(int $id, Request $request, ObjectManager $manager): Response
    {
//dump($request);
        if ($id < 0) {
//            edition d'un nouvel article
            $article = new Article();
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);
            dump($form->isSubmitted());
            dump($form->isSubmitted() AND $form->isValid());
            if ($form->isSubmitted() AND $form->isValid()) {
                $article->setCreatedAt(new \DateTime());
                $article->setUserId($this->getUser());
                dump($article);
                $manager->persist($article);
                $manager->flush();
                $this->addFlash('success', 'Votre Article a été enregistré avec succès');
                return $this->redirectToRoute('admin_articles');
            }
            return $this->render("Back/editArticle.html.twig", [
                "form" => $form->createView()
            ]);

        } else {
            // edition d'un article existant
            $article = $this->articleRepository->findOneBy(['id' => $id]);
            $form = $this->createForm(ArticleType::class, $article);
            $form->handleRequest($request);
            dump($form->isSubmitted());
            dump($form->isSubmitted() AND $form->isValid());
            if ($form->isSubmitted() AND $form->isValid()) {
                $manager->flush();
                $this->addFlash('success', 'Votre Article a été modifié avec succès');
                return $this->redirectToRoute('admin_articles');
            }
        }

        return $this->render("Back/editArticle.html.twig", [
            "form" => $form->createView()
        ]);

    }
}