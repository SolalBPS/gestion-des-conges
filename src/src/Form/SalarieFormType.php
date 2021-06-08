<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\Salarie;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\Email;

class SalarieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                "label" => 'Nom *',
                "required" => false,
                "constraints" => [ new NotBlank(["message" => "veuillez rentrer un nom"]), new Regex(["pattern" => "/^[a-zA-ZÀ-ÿ\- ]*$/", "message" => "Veuillez entrer un nom valide"])]
            ])
            ->add('prenom', TextType::class, [
                "label" => 'Prenom *',
                "required" => false,
                "constraints" => [ new NotBlank(["message" => "Veuillez entrer un prénom"]), new Regex(["pattern" => "/^[a-zA-ZÀ-ÿ\- ]*$/", "message" => "Veuillez entrer un prénom valide"])]
            ])
            ->add('email', EmailType::class, [
                "label" => 'E-mail *',
                "required" => false,
                "constraints" => [ new NotBlank(["message" => "Veuillez entrer un e-mail"]), new Email(["message" => "Veuillez entrer une adresse e-mail valide"])]
            ])
            ->add('telephone', TelType::class, [
                "label" => 'N° de téléphone',
                "required" => false,
                "constraints" => [ new Regex( ["pattern" => "/^(([0][0-9][0-9]{8})|([0][0-9] [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2}))$/","message" => "Veuillez entrer un numéro de téléphone français valide ou ne rien entrer (exemple: 0607080910 ou 06 07 08 09 10)"])]
            ])
            ->add('service', EntityType::class, [
                "label" => 'Service *',
                "mapped" => false,
                "required" => false,
                "class" => Service::class,
                "placeholder" => "Service",
                "constraints" => [ new NotBlank(["message" => "Veuillez choisir un service"])],
            ])
            ->add('roles', EntityType::class, [
                "label" => 'Rôle *',
                "mapped" => false,
                "required" => false,
                "class" => Role::class,
                "placeholder" => 'Rôle',
                "constraints" => [ new NotBlank(["message" => "Veuillez choisir un rôle"])],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Salarie::class,
            "attr" => [
                "novalidate"=>"novalidate",
            ]
        ]);
    }
}
