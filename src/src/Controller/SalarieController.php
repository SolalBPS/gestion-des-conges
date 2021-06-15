<?php


namespace App\Controller;

use App\Entity\Role;
use App\Entity\Salarie;
use App\Form\ChangePasswordFormType;
use App\Form\SalarieFormType;
use App\Repository\CongesRepository;
use App\Repository\ResetPasswordRequestRepository;
use App\Repository\RoleRepository;
use App\Repository\SalarieRepository;
use App\Repository\ServiceRepository;
use App\Services\SalarieHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SalarieController extends AbstractController
{

    private $entityManager;
    private $salarieRepository;
    private $resetPasswordRequestRepository;
    private $roleRepository;
    private $serviceRepository;
    private $salarieHelper;
    private $passwordEncoder;
    private $congesRepository;

    public function __construct(EntityManagerInterface $entityManager, salarieRepository $salarieRepository, CongesRepository $congesRepository,UserPasswordEncoderInterface $passwordEncoder, salarieHelper $salarieHelper, RoleRepository $roleRepository, ServiceRepository $serviceRepository, ResetPasswordRequestRepository $resetPasswordRequestRepository)
    {
        $this->entityManager = $entityManager;
        $this->salarieRepository = $salarieRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->roleRepository = $roleRepository;
        $this->serviceRepository = $serviceRepository;
        $this->salarieHelper = $salarieHelper;
        $this->resetPasswordRequestRepository = $resetPasswordRequestRepository;
        $this->congesRepository = $congesRepository;
    }

    /**
     * @Route("/salarie/ajouter",name="app_salarie_ajout")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function newSalarie(Request $request){
        $form = $this->createForm(SalarieFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->salarieHelper->addSalarie($form) == true ) {
                $this->salarieHelper->notifSalarie($form);
                return $this->redirectToRoute("app_salarie");
            }
        }
        return $this->render("salarie/ajoutsalarie.html.twig", ["salarieForm" => $form->createView()]);
    }

    /**
     * @Route("/salarie/modifier/{id}",name="app_salarie_modif")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function editSalarie(Request $request, Salarie $salarie){
        $form = $this->createForm(SalarieFormType::class);
        $form->get('nom')->setData($salarie->getNom());
        $form->get('prenom')->setData($salarie->getPrenom());
        $form->get('email')->setData($salarie->getEmail());
        $form->get('telephone')->setData($salarie->getTelephone());
        $oldrole = $this->roleRepository->findOneBy(["roleName" => $salarie->getRoles()[0]]);
        $form->get('roles')->setData($oldrole);
        $oldservice = $this->serviceRepository->findOneBy(["nom" => $salarie->getService()]);
        $form->get('service')->setData($oldservice);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->salarieHelper->editSalarie($form, $salarie) == true) {
                return $this->redirectToRoute("app_salarie");
            }
        }
        return $this->render("salarie/modifsalarie.html.twig", ["salarieForm" => $form->createView()]);
    }

    /**
     * @Route("/salarie/suppression/{id}",name="app_salarie_suppr")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function deleteSalarie(Salarie $salarie){
        $resetpwdrequest = $this->resetPasswordRequestRepository->findOneBy(["user" => $salarie]);
        $demande = $this->congesRepository->findOneBy(["userId" => $salarie->getId()]);
        if ($resetpwdrequest) {
            $this->entityManager->remove($resetpwdrequest);
        }
        if ($demande) {
            $this->entityManager->remove($demande);
        }
        $this->entityManager->remove($salarie);
        $this->entityManager->flush();
        return $this->json(["message" => "success", "value" => true]);
    }

    /**
     * @Route("/salarie/lister",name="app_salarie")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function showSalaries(Request $request)
    {
        $salaries = $this->salarieRepository->findAll();
        return $this->render("salarie/salarie.html.twig", ["salaries" => $salaries]);
    }

    /**
     * @Route("/salarie/changermdp",name="app_salarie_changepswd")
     * @IsGranted("ROLE_SALARIE")
     */
    public function changePswd(Request $request)
    {
        $salarie = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, $salarie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form["justpassword"]->getData();
            $newPassword = $form["newpassword"]->getData();

            if ($this->passwordEncoder->isPasswordValid($salarie, $password)) {
                $salarie->setPassword($this->passwordEncoder->encodePassword($salarie, $newPassword));
            } else {
                $this->addFlash("error", "Erreur lors du changement de mot de passe");
                return $this->render("admin/params/changeMdpForm.html.twig", ["passwordForm" => $form->createView()]);
            }

            $this->entityManager->persist($salarie);
            $this->entityManager->flush();
            $this->addFlash("success", "Mot de passe modifié !");
            return $this->redirectToRoute("app_home");
        }
        return $this->render("admin/params/changeMdpForm.html.twig", ["passwordForm" => $form->createView()]);
    }

}