<?php

namespace App\Controller;

use App\Entity\Salarie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    protected function getSalarie(): Salarie
    {
        return parent::getUser();
    }

    protected function getId()
    {
        return $this->getUser()->getId();
    }
}
