<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePwsdFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->translator = $options['translator'];

        $builder
            ->add("justpassword", PasswordType::class, [
                "label" => "Mot de passe actuel",
                "mapped" => false,
                "constraints" => [
                    new NotBlank(["message" => "Veuillez entrer le mot de passe actuel"]),
                    new UserPassword(["message" => "Veuillez entrer le mot de passe actuel"])
                ]
            ])
            ->add("newpassword", RepeatedType::class, [
                "mapped" => false,
                'invalid_message' => "Mots de passes non identiques",
                "type" => PasswordType::class,
                "constraints" => [
                    new NotBlank(["message" => "Ne doit pas être vide"])
                ],
                "first_options"  => ['label' => "Nouveau mot de passe"],
                "second_options"  => ['label' => "Confirmez le nouveau mot de passe"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('translator');

        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
