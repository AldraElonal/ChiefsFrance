<?php

namespace App\Controller\Admin\Comment;


use App\Repository\CommentRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    /**
     * @var CommentRepository
     */
    private $commentRepository;
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(CommentRepository $commentRepository, ObjectManager $manager)
    {

        $this->commentRepository = $commentRepository;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin", name="admin_index")
     */

    public function index(): Response
    {
        $comments = $this->commentRepository->findBy(['status' => 1]);
        return new Response($this->renderView("Back/home.html.twig", [
            "comments" => $comments
        ]));

    }

    /**
     * @Route("/admin/allowComment/{id}", name="admin_allowComment")
     */
    public function allowComment(int $id)
    {
        $comment = $this->commentRepository->findOneBy(['id' => $id]);
        if ($comment != null) {
            $comment->setStatus(3);
            $this->manager->flush();
            return $this->json([
                "message" => "Commentaire autorisé avec succès"
            ], 200);
        } else {
            return $this->json(["message" => "Commentaire introuvable"], 403);
        }
    }

    /**
     * @Route("/admin/disableComment/{id}", name="admin_disableComment")
     */
    public function disableComment(int $id)
    {
        $comment = $this->commentRepository->findOneBy(['id' => $id]);
        if ($comment != null) {
            $comment->setStatus(0);
            $this->manager->flush();
            return $this->json([
                "message" => "Commentaire supprimé avec succès"
            ], 200);
        } else {
            return $this->json(["message" => "Commentaire introuvable"], 403);
        }
    }
}