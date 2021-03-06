<?php

namespace App\Form;

use App\Entity\Salarie;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("justpassword", PasswordType::class, [
                "label" => "Mot de passe actuel",
                "mapped" => false,
                "required" => false,
                "constraints" => [
                    new NotBlank(["message" => "Veuillez entrer le mot de passe actuel"]),
                    new UserPassword(["message" => "Le mot de passe entré ne correspond pas au mot de passe actuel"])
                ]
            ])
            ->add("newpassword", RepeatedType::class, [
                "mapped" => false,
                "required" => false,
                'invalid_message' => "Mots de passes non identiques",
                "type" => PasswordType::class,
                "constraints" => [
                    new NotBlank(["message" => "Ne doit pas être vide"]),
                    new PasswordRequirements([
                        'minLength' => 6,
                        'requireLetters' => true,
                        'requireNumbers' => true,
                    ])
                ],
                "first_options"  => ['label' => "Nouveau mot de passe"],
                "second_options"  => ['label' => "Confirmez le nouveau mot de passe"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Salarie::class,
        ]);
    }
}
