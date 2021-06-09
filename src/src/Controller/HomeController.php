<?php


namespace App\Controller;

use App\Repository\SalarieRepository;
use App\Repository\ServiceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private $salarieRepository;
    private $serviceRepository;

    public function __construct(salarieRepository $salarieRepository, ServiceRepository $serviceRepository)
    {
        $this->salarieRepository = $salarieRepository;
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @Route("/",name="app_homepage")
     */
    public function homepage(){
        return $this->redirectToRoute("app_login");
    }

    /**
     * @Route("/home",name="app_home")
     */
    public function index(){
        $salaries = $this->salarieRepository->findAll();
        $services = $this->serviceRepository->findAll();
        return $this->render("admin/main.html.twig", [
            "salaries" => $salaries,
            "services" => $services
        ]);
    }

}
