<?php

namespace App\Controller;


use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController extends AbstractController
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="home")
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index(ArticleRepository $articleRepository, CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        foreach ($categories as $category) {
            $articles[] = $articleRepository->findOneBy(['category' => $category], ['created_at' => "DESC"]);
        }
        $timezone =  new \DateTimeZone("Europe/Paris");
        return new Response($this->twig->render('Front/home.html.twig',[
            'articles' => $articles,
            'timezone' => $timezone
        ]));
    }
}