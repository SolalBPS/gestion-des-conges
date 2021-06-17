<?php


namespace App\Services;

use App\Entity\Conges;
use App\Repository\CongesRepository;
use App\Repository\SalarieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class CongesHelper extends AbstractController
{

    private $entityManager;
    private $salarieRepository;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer, SalarieRepository $salarieRepository)
    {
        $this->entityManager = $entityManager;
        $this->salarieRepository = $salarieRepository;
        $this->mailer = $mailer;
    }

    public function demandeNotifConges(FormInterface $form) {
        //enregistre la demande
       $conges = $form->getData();
       $conges->setUserId($this->getUser()->getId());
       $conges->setEtat("en attente");
       $conges->setDatedemande(new \DateTime("today"));
       $this->entityManager->persist($conges);
       $this->entityManager->flush();
       $this->addFlash("success", "Demande enregistrée");

        //envoie une notif par mail
        $resp = $this->salarieRepository->findOneByRoleAndService("ROLE_RESPONSABLE_SERVICE", $this->getUser()->getService());
        $resprh = $this->salarieRepository->findOneByRole("ROLE_RESPONSABLE_RH");
        if ($resp === null) {
            $email = (new TemplatedEmail())
                ->from(new Address("rh@delko.fr", "RH Delko"))
                ->to(new Address($resprh->getEmail()))
                ->subject("Demande de congés")
                ->htmlTemplate("emails/notif_demande_conges.html.twig")
                ->context([
                    "prenom" => $this->getUser()->getPrenom(),
                    "nom" => $this->getUser()->getNom(),
                    "nature" => $form["nature"]->getData(),
                    "motif" => $form["motif"]->getData(),
                    "datedebut" => $form["datedebut"]->getData()->format("d/m/Y"),
                    "datefin" => $form["datefin"]->getData()->format("d/m/Y"),
                    "typedatedebut" => $form["typedatedebut"]->getData(),
                    "typedatefin" => $form["typedatefin"]->getData(),
                    "userId" => $this->getUser()->getId(),
                    "id" => $conges->getId(),
                ]);
            $this->mailer->send($email);
            $this->addFlash("success", "Le/La responsable RH a été notifié(e)");
        } else {
            $email = (new TemplatedEmail())
                ->from(new Address("rh@delko.fr", "RH Delko"))
                ->to(new Address($resp->getEmail()))
                ->subject("Demande de congés")
                ->htmlTemplate("emails/notif_demande_conges.html.twig")
                ->context([
                    "prenom" => $this->getUser()->getPrenom(),
                    "nom" => $this->getUser()->getNom(),
                    "nature" => $form["nature"]->getData(),
                    "motif" => $form["motif"]->getData(),
                    "datedebut" => $form["datedebut"]->getData()->format("d/m/Y"),
                    "datefin" => $form["datefin"]->getData()->format("d/m/Y"),
                    "typedatedebut" => $form["typedatedebut"]->getData(),
                    "typedatefin" => $form["typedatefin"]->getData(),
                    "userId" => $this->getUser()->getId(),
                    "id" => $conges->getId(),
                ]);
            $this->mailer->send($email);
            $this->addFlash("success", "Le/La responsable de votre service a été notifié(e)");
        }
    }
}