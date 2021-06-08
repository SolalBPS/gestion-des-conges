<?php


namespace App\Services;


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
    private $congesRepository;
    private $salarieRepository;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, congesRepository $congesRepository, MailerInterface $mailer, SalarieRepository $salarieRepository)
    {
        $this->entityManager = $entityManager;
        $this->congesRepository = $congesRepository;
        $this->salarieRepository = $salarieRepository;
        $this->mailer = $mailer;
    }

    public function demandeConges(FormInterface $form) {
       $conges = $form->getData();
       $conges->setUserId($this->getUser()->getId());
       $this->entityManager->persist($conges);
       $this->entityManager->flush();
       $this->addFlash("success", "Demande enregistrée");
    }

    public function notifDemandeConges(FormInterface $form) {
        $resp = $this->salarieRepository->findOneByRoleAndService("ROLE_RESPONSABLE_SERVICE", $this->getUser()->getService());
        $resprh = $this->salarieRepository->findOneByRole("ROLE_RESPONSABLE_RH");
        if ($resp == null) {
            $email = (new TemplatedEmail())
                ->from("rh@delko.fr")
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
                ]);
            $this->mailer->send($email);
            $this->addFlash("success", "Responsable RH notifié(e)");
        } else {
            $email = (new TemplatedEmail())
                ->from("rh@delko.fr")
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
                ]);
            $this->mailer->send($email);
        }
    }
}