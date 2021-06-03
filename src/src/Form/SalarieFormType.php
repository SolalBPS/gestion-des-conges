<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\Salarie;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SalarieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                "label" => 'Nom *',
                "required" => true,
                "attr" => ["title" => "Nom","pattern" => "^\p{L}+$", "oninvalid" => "this.setCustomValidity('Veuillez entrer un nom')", "onchange" => "this.setCustomValidity('')"],
                "constraints" => [ new NotBlank(["message" => "Ne doit pas être vide"])]
            ])
            ->add('prenom', TextType::class, [
                "label" => 'Prenom *',
                "required" => true,
                "attr" => ["title" => "Prenom","pattern" => "^\p{L}+$", "oninvalid" => "this.setCustomValidity('Veuillez entrer un prénom')", "onchange" => "this.setCustomValidity('')"],
                "constraints" => [ new NotBlank(["message" => "Ne doit pas être vide"])]
            ])
            ->add('email', EmailType::class, [
                "label" => 'E-mail *',
                "required" => true,
                "constraints" => [ new NotBlank(["message" => "Ne doit pas être vide"])]
            ])
            ->add('telephone', TelType::class, [
                "label" => 'N° de téléphone',
                "attr" => ["title" => "Numéro de téléphone francais (exemple: 0102030405)","pattern" => "^(([0][0-9][0-9]{8})|([0][0-9] [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2}))$", "oninvalid" => "this.setCustomValidity('Veuillez entrer un numéro de téléphone français valide')", "onchange" => "this.setCustomValidity('')"],
                "required" => false
            ])
            ->add('service', EntityType::class, [
                "label" => 'Service *',
                "mapped" => false,
                "class" => Service::class,
                "required" => true,
                "placeholder" => "Service",
                "constraints" => [ new NotBlank(["message" => "Ne doit pas être vide"])],
            ])
            ->add('roles', EntityType::class, [
                "label" => 'Rôle *',
                "mapped" => false,
                "class" => Role::class,
                "required" => true,
                "placeholder" => 'Rôle',
                "constraints" => [ new NotBlank(["message" => "ne doit pas être vide"])],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Salarie::class,
        ]);
    }
}
