<?php

namespace App\Controller\Admin\User;


use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class UserController extends AbstractController
{


    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {

        $this->repository = $repository;
    }

    /**
     *
     * @Route("/admin/user", name="admin_user")
     * @return Response
     */
    public function index(): Response
    {

        $users = $this->repository->findAll();
        return new Response($this->renderView('Back/users.html.twig', ['users' => $users]));
    }


    /**
     *
     * @Route("/admin/user/edit/{id}" , methods="post")
     * @param int $id
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function editUser(int $id, ObjectManager $manager)
    {
        var_dump($_POST["roles"]);
        var_dump($_POST);
        $user = $this->repository->findOneBy(['id' => $id]);

        $user->setRoles($_POST['roles']);
        $manager->flush();

        return $this->redirectToRoute("admin_user");

    }
}



