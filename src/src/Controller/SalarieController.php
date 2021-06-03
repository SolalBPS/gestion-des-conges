<?php


namespace App\Controller;

use App\Entity\Role;
use App\Entity\Salarie;
use App\Form\SalarieFormType;
use App\Repository\RoleRepository;
use App\Repository\SalarieRepository;
use App\Repository\ServiceRepository;
use App\Services\SalarieHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class SalarieController extends AbstractController
{

    private $entityManager;
    private $salarieRepository;
    private $roleRepository;
    private $serviceRepository;
    private $salarieHelper;


    public function __construct(EntityManagerInterface $entityManager, salarieRepository $salarieRepository, salarieHelper $salarieHelper, RoleRepository $roleRepository, ServiceRepository $serviceRepository)
    {
        $this->entityManager = $entityManager;
        $this->salarieRepository = $salarieRepository;
        $this->roleRepository = $roleRepository;
        $this->serviceRepository = $serviceRepository;
        $this->salarieHelper = $salarieHelper;
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
        //test
    }

    /**
     * @Route("/salarie/suppression/{id}",name="app_salarie_suppr")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function deleteSalarie(Salarie $salarie){
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



}