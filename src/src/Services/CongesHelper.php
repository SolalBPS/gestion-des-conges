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
        $today = new \DateTime();
        if ($form["datedebut"]->getData()->format('m') == $today->format('m') || $form["datefin"]->getData()->format('m') <= $today->format('m')) {

        }
        if ($form["datedebut"]->getData()->format('d') <= $today->format('d') || $form["datefin"]->getData()->format('d') <= $today->format('d')) {
            return false;
        } else {
            return true;
        }

    }

}