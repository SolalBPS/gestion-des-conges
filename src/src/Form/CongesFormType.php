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
                "required" => true,
                "constraints" => [ new NotBlank(["message" => "Ne doit pas être vide"])]
            ])
            ->add('motif', TextType::class, [
                "label" => "Motif des congés exceptionnels conventionnel",
                "required" => true,
            ])

            ->add('datedebut', DateType::class, [
                "label" => "Date de début",
                "required" => true,
                "widget" => "choice",
                "days" => range(1, 31),
                "months" => range(1, 12),
                "years" => range(date("Y"), date("Y")+5),
                "placeholder" => [
                    "day" => "Jour", "month" => "Mois", "year" => "Année"
                ],
                "constraints" => [ new NotBlank(["message" => "Ne doit pas être vide"])]
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
                "required" => true,
                "constraints" => [ new NotBlank(["message" => "Ne doit pas être vide"])]
            ])

            ->add('datefin', DateType::class, [
                "label" => "Date de fin",
                "required" => true,
                "widget" => "choice",
                "days" => range(1, 31),
                "months" => range(1, 12),
                "years" => range(date("Y"), date("Y")+5),
                "placeholder" => [
                    "day" => "Jour", "month" => "Mois", "year" => "Année"
                ],
                "constraints" => [ new NotBlank(["message" => "Ne doit pas être vide"])]
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
                "required" => true,
                "constraints" => [ new NotBlank(["message" => "Ne doit pas être vide"])]
            ])
        ;
//        $builder->get('nature')->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
//            $form = $event->getForm();
//            $nature= $form->get('nature')->getData();
//
//            if ($nature == "congés exceptionnels conventionnel"){
//                $form->add('motif', TextType::class, [
//                    "label" => "Motif des congés",
//                    "required" => true,
//                    "constraints" => [ new NotBlank(["message" => "Ne doit pas être vide"])]
//                ]);
//                $form->get("typedadedebut")->setData("matin");
//            }
//        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Conges::class,
        ]);
    }
}
