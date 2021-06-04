<?php


namespace App\Controller;

use App\Form\CongesFormType;
use App\Services\CongesHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class CongesController extends AbstractController
{

    private $congesHelper;

    public function __construct(CongesHelper $congesHelper) {
        $this->congesHelper = $congesHelper;
    }

    /**
     * @Route("/conges/demande",name="app_conges_demande")
     * @IsGranted("ROLE_SALARIE")
     */
    public function demandeConges(Request $request){
        $form = $this->createForm(CongesFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->congesHelper->demandeConges($form);
            return $this->redirectToRoute("app_home");
        }
        return $this->render("conges/demande_conges.html.twig", ["congesForm" => $form->createView()]);
    }



}