<?php

namespace App\Controller\Admin\Comment;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{

    /**
     * @Route("/admin", name="admin_index")
     */

    public function index(): Response
    {
        return new Response($this->renderView("Back/home.html.twig"));
    }


}