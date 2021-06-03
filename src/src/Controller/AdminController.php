<?php


namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class AdminController extends AbstractController
{

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
        return $this->render("admin/main.html.twig");
    }

}
