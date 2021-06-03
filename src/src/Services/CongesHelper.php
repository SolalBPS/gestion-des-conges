<?php


namespace App\Services;


use App\Repository\CongesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class CongesHelper
{

    private $entityManager;
    private $congesRepository;

    public function __construct(EntityManagerInterface $entityManager, congesRepository $congesRepository)
    {
        $this->entityManager = $entityManager;
        $this->congesRepository = $congesRepository;
    }

    public function demandeConges(FormInterface $form) {
        $today = new \DateTime('midnight');
//        dump($today);
//        dump($form["datedebut"]->getData());
//        dd($form["datefin"]->getData());
//        if ($form["datedebut"]->getData() == $today || $form["datefin"]->getData() == $today) {
//            return false;
//        } else {
//            return true;
//        }

    }

}