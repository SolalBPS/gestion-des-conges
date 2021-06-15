<?php


namespace App\Controller;

use App\Repository\SalarieRepository;
use App\Repository\ServiceRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\CalendarLinks\Link;

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
        $salarie = $this->getUser();
        $calendar = Calendar::create("Congés des salariés du service" . $salarie->getService())
            ->description("Quand vos collègues sont ils en congés ?")
            ->get();

        $from = DateTime::createFromFormat('Y-m-d H:i', '2018-02-01 09:00');
        $to = DateTime::createFromFormat('Y-m-d H:i', '2018-02-01 18:00');
        $link = Link::create('Sebastian’s birthday', $from, $to)
            ->description('Cookies & cocktails!')
            ->address('Kruikstraat 22, 2018 Antwerpen');
//        dd($link->ics());
        $salaries = $this->salarieRepository->findAll();
        $services = $this->serviceRepository->findAll();
        return $this->render("admin/main.html.twig", [
            "salaries" => $salaries,
            "services" => $services,
            "calendarlink" => $link->ics(),
            "calendarlinkgoogle" => $link->google()
        ]);
    }

}
