<?php


namespace App\Controller;

use App\Entity\Conges;
use App\Form\CongesFormType;
use App\Repository\CongesRepository;
use App\Repository\SalarieRepository;
use App\Services\CongesHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CongesController extends AbstractController
{

    private $congesHelper;
    private $congesRepository;
    private $salarieRepository;

    public function __construct(CongesHelper $congesHelper, CongesRepository $congesRepository, SalarieRepository $salarieRepository) {
        $this->congesHelper = $congesHelper;
        $this->congesRepository = $congesRepository;
        $this->salarieRepository = $salarieRepository;
    }

    /**
     * @Route("/conges/demandes",name="app_conges_demande")
     * @IsGranted("ROLE_SALARIE")
     */
    public function demandeConges(Request $request){
        $form = $this->createForm(CongesFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->congesHelper->demandeNotifConges($form);
            return $this->redirectToRoute("app_conges");
        }
        return $this->render("conges/demande_conges.html.twig", ["congesForm" => $form->createView()]);
    }

    /**
     * @Route("/conges/lister",name="app_conges")
     * @IsGranted("ROLE_SALARIE")
     */
    public function showDemandesConges(){
        $demandes = $this->congesRepository->findBy(["userId" => $this->getUser()->getId()]);
        return $this->render("conges/conges.html.twig", ["demandes" => $demandes]);
    }

    /**
     * @Route("/conges/demande/{userId}/{id}",name="app_conges_gerer")
     * @IsGranted("ROLE_RESPONSABLE_SERVICE")
     */
    public function manageDemande(int $userId, int $id){
        $demande = $this->congesRepository->findOneBy(["userId" => $userId, "id" => $id]);
        $salarie = $this->salarieRepository->findOneBy(["id" => $userId]);
        if ($salarie == null) {
            $this->addFlash("error", "Ce salarié n'existe plus");
            return $this->redirectToRoute("app_home");
        }
        if ($demande == null) {
            $this->addFlash("error", "Cette demande n'existe plus");
            return $this->redirectToRoute("app_home");
        }
        return $this->render("conges/gerer_conges.html.twig", ["demande" => $demande, "salarie" => $salarie]);
    }

    /**
     * @Route("/conges/gerer",name="app_conges_valider")
     * @IsGranted("ROLE_SALARIE")
     */
    public function validateDemandesConges(){
        $demandes = $this->congesRepository->findBy(["userId" => $this->getUser()->getId()]);
        return $this->render("conges/conges.html.twig", ["demandes" => $demandes]);
    }

    /**
     * @Route("/conges/gerer",name="app_conges_refuser")
     * @IsGranted("ROLE_SALARIE")
     */
    public function denyDemandesConges(){
        $demandes = $this->congesRepository->findBy(["userId" => $this->getUser()->getId()]);
        return $this->render("conges/conges.html.twig", ["demandes" => $demandes]);
    }

}