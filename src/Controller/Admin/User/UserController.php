<?php

namespace App\Controller\Admin\User;


use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $user = $this->repository->findOneBy(['id' => $id]);
        $user->setRoles($_POST['roles']);
        $manager->flush();
        return $this->redirectToRoute("admin_user");
    }

    /**
     * @Route("/banUser/{username}", name="admin_banUser")
     * @param string $username
     * @param UserRepository $userRepository
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function banUser(string $username, UserRepository $userRepository, ObjectManager $manager)
    {
        if ($this->getUser()->getRoles() != ['ROLE_ADMIN']) {
            $this->addFlash('warning', "Vous n'avez pas l'autorisation de réaliser cette action");
            return $this->redirectToRoute("admin_index");
        }
        if ($username != $this->getUser()->getUsername() ) {
            $user = $userRepository->findOneBy(['username' => $username]);
            $user->setRoles(["ROLE_BANNED"]);
            $manager->flush();
        }
        return $this->redirectToRoute("admin_user");
    }

    /**
     * @Route("/unbanUser/{username}", name="admin_unbanUser")
     * @param string $username
     * @param UserRepository $userRepository
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unbanUser(string $username, UserRepository $userRepository, ObjectManager $manager)
    {
        if ($this->getUser()->getRoles() != ['ROLE_ADMIN']) {
            $this->addFlash('warning', "Vous n'avez pas l'autorisation de réaliser cette action");
            return $this->redirectToRoute("admin_index");
        }
            $user = $userRepository->findOneBy(['username' => $username]);
            if ($user->getRoles() == ["ROLE_BANNED"]) {
                $user->setRoles(["ROLE_USER"]);
                $manager->flush();
            }
        return $this->redirectToRoute("admin_user");
    }
}



