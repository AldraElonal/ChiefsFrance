<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController{
    /**
     * @Route ("/actus", name="article_showall")
     */
    public function showAll(){

        return new Response('response');

}



}