<?php


namespace App\Services;


use App\Repository\CongesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
       $conges->setUserId($this->getUser()->getId());
       $this->entityManager->persist($conges);
       $this->entityManager->flush();
       $this->addFlash("success", "Demande enregistrée");
    }

}