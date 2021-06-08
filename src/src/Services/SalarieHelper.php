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
use Symfony\Component\Routing\Annotation\Route;

class SalarieHelper extends AbstractController
{

    private $entityManager;
    private $salarieRepository;



    public function __construct(EntityManagerInterface $entityManager, salarieRepository $salarieRepository)
    {
        $this->entityManager = $entityManager;
        $this->salarieRepository = $salarieRepository;

    }

    public function addSalarie(FormInterface $form) {
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
                if ($role->getRoleName() == "ROLE_RESPONSABLE_RH"){
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
}