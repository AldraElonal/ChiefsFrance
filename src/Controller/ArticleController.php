<?php

namespace App\Controller;


use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController{
    /**
     * @Route ("/actus", name="article_showall")
     */
    public function showAll(ArticleRepository $repository){

        $articles = $repository->findAll();
        return $this->render("Front/articles.html.twig",[
            "articles" => $articles]);

}



}