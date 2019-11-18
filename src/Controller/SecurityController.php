<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Notification\ContactNotification;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use RandomLib\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    /**
     * @Route("/inscription", name="security_registration")
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param ContactNotification $notification
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, ContactNotification $notification)
    {

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->isValid());
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $factory = new Factory();
            $generator = $factory->getLowStrengthGenerator();
            $token = $generator->generateString(16);
            $user->setToken($token);
            $user->setRoles("ROLE_UNREGISTERED");


            $notification->notifyInscription($user);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Veuillez vérifier vos emails pour confirmer votre inscription');
//            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/validation/{token}",name="validation_token", methods="GET|POST")
     * @param ObjectManager $manager
     * @param UserRepository $repository
     * @param string $token
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmRegistration(ObjectManager $manager, UserRepository $repository, string $token)
    {
        $user = $repository->findOneBy(['token' => $token]);
        if ($user != null AND $user->getRoles() == ["ROLE_UNREGISTERED"]) {
            $user->setRoles("ROLE_ADMIN");
            $user->setToken(null);
            // regénérer un token une fois la validation confirmée?
            $manager->flush();
            return $this->render('security/validation.html.twig', [
                'user' => $user
            ]);
        } else {
            return $this->redirectToRoute("home");
        }
    }

    /**
     * @Route("/forgotPassword", name="security_forgotPassword")
     * @param UserRepository $repository
     * @param ContactNotification $notification
     * @param ObjectManager $manager
     * @return Response
     */
    public function forgotPassword(UserRepository $repository, ContactNotification $notification, ObjectManager $manager, Request $request): Response
    {

        $user = new User();

        $form = $this->createFormBuilder($user, [
            'validation_groups' => "newPassword"
        ])
            ->add('email')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $repository->findOneBy(['email' => $user->getEmail()]);
            if ($user == null) {
                $this->addFlash('success', "utilisateur non trouvé : email invalide");
            } else {
                $factory = new Factory();
                $generator = $factory->getLowStrengthGenerator();
                $token = $generator->generateString(16);
                $user->setToken($token);
                $manager->flush();
                $notification->notifyLossPassword($user);
                $this->addFlash('success', 'Un lien vous permettant de changer votre mot de passe vous a été envoyé par mail');
            }
        }
        return $this->render('security/askNewPassword.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/newPassword/{id}/{token}",name="security_newPassword")
     * @param UserRepository $repository
     * @param UserPasswordEncoderInterface $encoder
     * @param ObjectManager $manager
     * @return Response
     */
    public function askNewPassword(UserRepository $repository, string $token, int $id, UserPasswordEncoderInterface $encoder, ObjectManager $manager, Request $request)
    {
        $user = $repository->findOneBy(['id' => $id]);
        if ($user->getToken() == $token) {
//$user = new User();
//get User and check token =/= null

            $form = $this->createFormBuilder($user, [
                'validation_groups' => "newPassword"
            ])
                ->add('password', PasswordType::class)
                ->add('confirm_password', PasswordType::class)
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() AND $form->isValid()) {
                $hash = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hash);
                $user->setToken(null);
//            regénérer un token une fois le mdp modifié?
                $manager->flush();
                $this->addFlash('success', 'Votre mot de passe a été modifié, vous pouvez à présent vous connecter.');
                return $this->redirectToRoute('security_login');


            }
            return $this->render("security/setNewPassword.html.twig", [
                "user" => $user,
                'form' => $form->createView()
            ]);

        } else {
            return $this->redirectToRoute("home");
        }
    }


    /**
     * @Route("/membre/{username}", name="security_editmember")
     *
     */
    public function editProfile(string $username, UserRepository $repository, ObjectManager $manager, Request $request, UserPasswordEncoderInterface $encoder)
    {

        $emptyUser = new User();

        if ($this->getUser()->getUsername() == $username OR in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            $user = $repository->findOneBy(["username" => $username]);

            if ($user == null) {
                return $this->redirectToRoute("home");
            }

            $formProfile = $this->createForm(RegistrationType::class, $user);

            $formPassword = $this->createFormBuilder($emptyUser, [
                'validation_groups' => "newPassword"
            ])
                ->add('password', PasswordType::class)
                ->add('confirm_password', PasswordType::class)
                ->getForm();


            $formProfile->handleRequest($request);
                if ($formProfile->isSubmitted() AND $formProfile->isValid()) {
                    $manager->flush();
                    $this->addFlash('success', 'Votre profil a bien été modifié');
                }


            $formPassword->handleRequest($request);
                if ($formPassword->isSubmitted() AND $formPassword->isValid()) {
                    $hash = $encoder->encodePassword($emptyUser, $emptyUser->getPassword());
                    $user->setPassword($hash);
                    $manager->flush();
                    $this->addFlash('success', 'Votre mot de passe a bien été modifié');
                }

                dump($this->getUser()->getUsername());
if($username == $this->getUser()->getUsername()){
    $editroles = true;
}else{
    $editroles = false;
}
            return $this->render("security/editProfile.html.twig", [
                "formProfile" => $formProfile->createView(),
                "formPassword" => $formPassword->createView(),
                "user" => $user,
                "editroles" => $editroles
            ]);

        }


        return $this->redirectToRoute("home");

    }

    /**
     * @Route("/connexion",  name="security_login")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login()
    {

        return $this->render('security/login.html.twig');

    }

    /**
     * @Route("/deconnexion",name="security_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/checkValid", name="security_check")
     */
    public function checkValid()
    {

        if ($this->getUser()->getRoles() == ["ROLE_UNREGISTERED"] OR !$this->getUser()->gettoken() == "" OR !$this->getUser()->gettoken() == null) {
// utilisateur non enregistré ou token non nul

            return $this->redirectToRoute("security_logout");
        } else {


            return $this->redirectToRoute("home");

        }
    }
}
