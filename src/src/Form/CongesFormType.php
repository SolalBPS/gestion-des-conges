<?php

namespace App\Form;

use App\Entity\Conges;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CongesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nature', ChoiceType::class, [
                "choices" => [
                  "congés payés" => "congés payés",
                  "congés formation" => "congés formation",
                  "congés sans solde" => "congés sans solde",
                  "congés exceptionnels conventionnel" => "congés exceptionnels conventionnel",
                  "absence rémunérée" => "absence rémunérée"
                ],
                "placeholder" => "Nature des congés",
                "label" => "Nature des congés",
                "required" => false,
                "constraints" => [ new NotBlank(["message" => "Veuillez sélectionner la nature des congés"])]
            ])
            ->add('motif', TextType::class, [
                "label" => "Motif des congés exceptionnels conventionnel",
                "required" => false,
                "constraints" => [ new NotBlank(["message" => "Veuillez entrer le motif"])]
            ])

            ->add('datedebut', DateType::class, [
                "label" => "Date de début",
                "required" => false,
                "widget" => "choice",
                "days" => range(1, 31),
                "months" => range(1, 12),
                "years" => range(date("Y"), date("Y")+5),
                "placeholder" => [
                    "day" => "Jour", "month" => "Mois", "year" => "Année"
                ],
                "constraints" => [ new NotBlank(["message" => "Veuillez choisir la date de début des congés"])]
            ])
            ->add('typedatedebut', ChoiceType::class, [
                "choices" => [
                    "journée" => "journée",
                    "matin" => "matin",
                    "après midi" => "après midi",
                ],
                "expanded" => true,
                "multiple" => false,
                "label" => "Type de journée de la date de début",
                "required" => false,
                "placeholder" => false,
                "constraints" => [ new NotBlank(["message" => "Veuillez sélectionner le type de journée de la date de début des congés"])]
            ])

            ->add('datefin', DateType::class, [
                "label" => "Date de fin",
                "required" => false,
                "widget" => "choice",
                "days" => range(1, 31),
                "months" => range(1, 12),
                "years" => range(date("Y"), date("Y")+5),
                "placeholder" => [
                    "day" => "Jour", "month" => "Mois", "year" => "Année"
                ],
                "constraints" => [ new NotBlank(["message" => "Veuillez choisir la date de fin des congés"])]
            ])
            ->add('typedatefin', ChoiceType::class, [
                "choices" => [
                    "journée" => "journée",
                    "matin" => "matin",
                    "après midi" => "après midi",
                ],
                "expanded" => true,
                "multiple" => false,
                "label" => "Type de journée de la date de fin",
                "required" => false,
                "placeholder" => false,
                "constraints" => [ new NotBlank(["message" => "Veuillez sélectionner le type de journée de la date de fin des congés"])]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Conges::class,
        ]);
    }
}
