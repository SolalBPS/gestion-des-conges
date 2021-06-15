<?php


namespace App\Services;


use App\Entity\Role;
use App\Entity\Salarie;
use App\Form\SalarieFormType;
use App\Repository\RoleRepository;
use App\Repository\SalarieRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class SalarieHelper extends AbstractController
{

    use ResetPasswordControllerTrait;

    private $entityManager;
    private $salarieRepository;
    private $passwordEncoder;
    private $resetPasswordHelper;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, salarieRepository $salarieRepository, UserPasswordEncoderInterface $passwordEncoder, ResetPasswordHelperInterface $resetPasswordHelper, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->salarieRepository = $salarieRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->mailer = $mailer;
    }

    public function addSalarie(FormInterface $form) {
        $randompwd = random_bytes(10);
        $service = $form["service"]->getData();
        $role = $form["roles"]->getData();
        $rhcheck = $this->salarieRepository->findOneByRole("ROLE_RESPONSABLE_RH");
        $respcheck = $this->salarieRepository->findOneByRoleAndService("ROLE_RESPONSABLE_SERVICE", $service->getNom());
        $emailcheck = $this->salarieRepository->findOneByEmail($form["email"]->getData());
        if ($emailcheck !== null) {
            $this->addFlash("error", "Cette addresse e-mail est déjà associée à un salarié");
            return false;
        }
        if ($service->getNom() !== "Ressources humaines" && $role->getRoleName() == "ROLE_RESPONSABLE_RH") {
            $this->addFlash("error", "Le/La responsable RH peut seulement être affecté(e) au service Ressources humaines");
            return false;
        }
        if ($rhcheck == false || $role->getRoleName() !== "ROLE_RESPONSABLE_RH" ){
            if ( $respcheck == null || $role->getRoleName() == "ROLE_SALARIE"){
                $salarie = $form->getData();
                $salarie->setService($service->getNom());
                $salarie->setPassword($this->passwordEncoder->encodePassword($salarie, $randompwd));
                if ($role->getRoleName() === "ROLE_RESPONSABLE_RH"){
                    $salarie->setRoles([$role->getRoleName(), "ROLE_RESPONSABLE_SERVICE", "ROLE_SALARIE"]);
                } elseif ($role->getRoleName() == "ROLE_RESPONSABLE_SERVICE") {
                    $salarie->setRoles([$role->getRoleName(), "ROLE_SALARIE"]);
                } else {
                    $salarie->setRoles([$role->getRoleName()]);
                }
                $this->entityManager->persist($salarie);
                $this->entityManager->flush();
                $this->addFlash("success", "Salarié ajouté");
                return true;
            } else {
                $this->addFlash("error", $respcheck->getPrenom() . " " . $respcheck->getNom() . " est déjà défini(e) comme étant le/la responsable de ce service");
            }
        } else {
            $this->addFlash("error", $rhcheck->getPrenom() . " " . $rhcheck->getNom() . " est déjà défini(e) comme étant le/la responsable RH");
        }
    }

    public function editSalarie(FormInterface $form, Salarie $salarie)
    {
        $service = $form["service"]->getData();
        $role = $form["roles"]->getData();
        $rhcheck = $this->salarieRepository->findOneByRole("ROLE_RESPONSABLE_RH");
        $respcheck = $this->salarieRepository->findOneByRoleAndService("ROLE_RESPONSABLE_SERVICE", $service->getNom());
        $emailcheck = $this->salarieRepository->findOneByEmail($form["email"]->getData());
        if ($emailcheck !== null && $emailcheck->getId() !== $salarie->getId()) {
            $this->addFlash("error", "Cette addresse e-mail est déjà associée à un salarié");
            return false;
        }
        if ($service->getNom() !== "Ressources humaines" && $role->getRoleName() == "ROLE_RESPONSABLE_RH") {
            $this->addFlash("error", "Le/La responsable RH peut seulement être affecté(e) au service Ressources humaines");
            return false;
        }
        if ($rhcheck == null || $role->getRoleName() !== "ROLE_RESPONSABLE_RH" || $rhcheck->getId() == $salarie->getId()) {
            if ($respcheck == null || $role->getRoleName() == "ROLE_SALARIE" || $respcheck->getId() == $salarie->getId()) {
                $salarie->setNom($form["nom"]->getData());
                $salarie->setPrenom($form["prenom"]->getData());
                $salarie->setEmail($form["email"]->getData());
                $salarie->setTelephone($form["telephone"]->getData());
                $service = $form["service"]->getData();
                $salarie->setService($service->getNom());
                $roles = $form["roles"]->getData();
                if ($roles->getRoleName() == "ROLE_RESPONSABLE_RH") {
                    $salarie->setRoles([$roles->getRoleName(), "ROLE_RESPONSABLE_SERVICE", "ROLE_SALARIE"]);
                } elseif ($roles->getRoleName() == "ROLE_RESPONSABLE_SERVICE") {
                    $salarie->setRoles([$roles->getRoleName(), "ROLE_SALARIE"]);
                } else {
                    $salarie->setRoles([$roles->getRoleName()]);
                }
                $this->entityManager->persist($salarie);
                $this->entityManager->flush();
                $this->addFlash("success", "Salarie modifié");
                return true;
            } else {
                $this->addFlash("error", $respcheck->getPrenom() . " " . $respcheck->getNom() . " est déjà défini(e) comme étant le/la responsable de ce service");
            }
        } else {
            $this->addFlash("error", $rhcheck->getPrenom() . " " . $rhcheck->getNom() . " est déjà défini(e) comme étant le/la responsable RH");
        }
    }

    public function notifSalarie(FormInterface $form) {
        $emailaddr = $form["email"]->getData();
        $user = $this->getDoctrine()->getRepository(Salarie::class)->findOneBy([
            'email' => $emailaddr,
        ]);

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->redirectToRoute('app_check_email');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('rh@delko.fr', 'RH Delko'))
            ->to($user->getEmail())
            ->subject('Compte RH Delko : Initialisation du mot de passe')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'prenom' => $user->getPrenom(),
                'nom' => $user->getNom(),
            ])
        ;
        $this->mailer->send($email);

        $this->addFlash("success", "E-mail envoyé au salarié ajouté !");
    }
}