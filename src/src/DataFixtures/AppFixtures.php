<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\Salarie;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->encoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $roles = [
            "ROLE_RESPONSABLE_RH" => "Responsable RH",
            "ROLE_RESPONSABLE_SERVICE" => "Responsable de service",
            "ROLE_SALARIE" => "Salarié"
        ];

        foreach ($roles as $key => $value) {
                $role = new Role();
                $role->setRoleName($key);
                $role->setLibelle($value);
                $manager->persist($role);
                $manager->flush();
        }

        $service = new Service();
        $service->setNom("Ressources humaines");
        $manager->persist($service);
        $manager->flush();

        $service = new Service();
        $service->setNom("Développement digital");
        $manager->persist($service);
        $manager->flush();

        $service = new Service();
        $service->setNom("Marketing");
        $manager->persist($service);
        $manager->flush();

        $salarie = new Salarie();
        $salarie->setNom('RH');
        $salarie->setPrenom('Responsable');
        $salarie->setRoles(["ROLE_RESPONSABLE_RH", "ROLE_RESPONSABLE_SERVICE", "ROLE_SALARIE"]);
        $salarie->setPassword($this->encoder->encodePassword($salarie, 'admin'));
        $salarie->setEmail('rh@delko.fr');
        $salarie->setTelephone(null);
        $salarie->setService('Ressources humaines');
        $salarie->setColor("#087f48");
        $manager->persist($salarie);
        $manager->flush();

        $salarie = new Salarie();
        $salarie->setNom('Service');
        $salarie->setPrenom('Responsable');
        $salarie->setRoles(["ROLE_RESPONSABLE_SERVICE","ROLE_SALARIE"]);
        $salarie->setPassword($this->encoder->encodePassword($salarie, 'responsable'));
        $salarie->setEmail('resp@delko.fr');
        $salarie->setTelephone(null);
        $salarie->setService('Développement digital');
        $salarie->setColor("#c1950f");
        $manager->persist($salarie);
        $manager->flush();
    }
}
