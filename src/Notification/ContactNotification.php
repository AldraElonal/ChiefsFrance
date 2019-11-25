<?php
namespace App\Notification;

use App\Entity\User;
use Twig\Environment;

class ContactNotification
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $renderer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer){

        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    public function notifyInscription(User $user){
dump($user);
        $message = (new \Swift_Message('Bienvenue sur ChiefsFrance'))
            ->setFrom('noreply@remileger.eu')
            ->setTo($user->getEmail())
            ->setBody($this->renderer->render('emails/inscription.html.twig',[
                'user' => $user
            ]),'text/html');

        $this->mailer->send($message);

    }


    public function notifyLossPassword(User $user){

        $message = (new \Swift_Message('Mot de passe Perdu'))
            ->setFrom('noreply@server.com')
            ->setTo($user->getEmail())
            ->setBody($this->renderer->render('emails/forgotPassword.html.twig',[
                'user' => $user
            ]),'text/html');

        $this->mailer->send($message);

    }
}