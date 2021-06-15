<?php


namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceFormType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ServiceController extends AbstractController
{
    private $entityManager;
    private $serviceRepository;

    public function __construct(EntityManagerInterface $entityManager, serviceRepository $serviceRepository)
    {
        $this->entityManager = $entityManager;
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @Route("/service/lister",name="app_service")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function services(){
        $services = $this->serviceRepository->findAll();
        return $this->render("service/service.html.twig", ["services" => $services]);
    }

    /**
     * @Route("/service/ajouter",name="app_service_ajout")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function newService(Request $request){
        $form = $this->createForm(ServiceFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingservice = $this->serviceRepository->findOneBy(["nom" => $form["nom"]->getData()]);
            if ($existingservice == null) {
                $service = $form->getData();
                $this->entityManager->persist($service);
                $this->entityManager->flush();
                $this->addFlash("success", "Service ajouté");
                return $this->redirectToRoute("app_service");
            }
            $this->addFlash("error", "Ce service existe déjà");
        }
        return $this->render("service/ajoutservice.html.twig", ["serviceForm" => $form->createView()]);
    }

    /**
     * @Route("/service/modification/{id}",name="app_service_modif")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function editService(Request $request, Service $service){
        $form = $this->createForm(ServiceFormType::class);
        $form->get('nom')->setData($service->getNom());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form["nom"]->getData() == $service->getNom()) {
                $service->setNom($form["nom"]->getData());
                $this->entityManager->persist($service);
                $this->entityManager->flush();
                $this->addFlash("success", "Service modifié");
                return $this->redirectToRoute("app_service");
            }
            $this->addFlash("error", "Ce service existe déjà");
        }
        return $this->render("service/modifservice.html.twig", ["serviceForm" => $form->createView()]);
    }

    /**
     * @Route("/service/suppression/{id}",name="app_service_suppr")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function deleteService(Service $service){
        $this->entityManager->remove($service);
        $this->entityManager->flush();
        return $this->json(["message" => "success", "value" => true]);

    }

}