<?php
namespace App\EventSubscriber;

use App\Repository\CongesRepository;
use App\Repository\SalarieRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $congesRepository;
    private $salarieRepository;

    public function __construct( CongesRepository $congesRepository, SalarieRepository $salarieRepository)
    {
        $this->congesRepository = $congesRepository;
        $this->salarieRepository = $salarieRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $conges = $this->congesRepository->findBy(["etat" => "validée"]);
        foreach ($conges as $demande) {
            $user = $this->salarieRepository->findOneBy(["id" => $demande->getUserId()]);
            if ($user) {
                $event = new Event(
                    $user->getPrenom() . " " . $user->getNom(),
                    $demande->getDatedebut(),
                    $demande->getDatefin()
                );
                $event->setAllDay(true);
                $calendar->addEvent($event);
            }
        }
    }
}