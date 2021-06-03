<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{

    /**
     * @Route("/email", name="app_sendmail")
     */
    public function sendEmail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('rh@delko.fr')
            ->to('bompais.solal@gmail.com')
            ->subject('initialisation du mot de passe Delko RH')
            ->text('Veuillez initialiser votre mot de passe pour Delko RH')
            ->html('<p style="color: red">See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
        return $this->redirectToRoute("app_home");

    }

}