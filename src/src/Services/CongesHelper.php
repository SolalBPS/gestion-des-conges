<?php


namespace App\Services;


use App\Repository\CongesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

class CongesHelper extends AbstractController
{

    private $entityManager;
    private $congesRepository;

    public function __construct(EntityManagerInterface $entityManager, congesRepository $congesRepository)
    {
        $this->entityManager = $entityManager;
        $this->congesRepository = $congesRepository;
    }

    public function demandeConges(FormInterface $form) {
       $conges = $form->getData();
       $this->entityManager->persist($conges);
       $this->entityManager->flush();
       $this->addFlash("success", "Demande enregistrée");
    }

}