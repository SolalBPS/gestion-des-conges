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
            if (!$manager->getRepository(Role::class)->findByRoleName([$key])) {
                $role = new Role();
                $role->setRoleName($key);
                $role->setLibelle($value);
                $manager->persist($role);
                $manager->flush();
            }
        }

        $user = new User();
        if (!$manager->find(User::class, 1)) {
            $user->setUsername('admin');
            $user->setRoles(["ROLE_RESPONSABLE_RH"]);
            $user->setPassword($this->encoder->encodePassword($user, 'admin'));
            $user->setNomComplet('Admin');
            $user->setEmail('admin@example.com');
            $user->setAdmin(true);
            $manager->persist($user);

            $manager->flush();
        }

        $service = new Service();
        if (!$manager->getRepository(Service::class)->findOneBy(["nom" => "Ressources humaines"])) {
            $service->setNom("Ressources humaines");
            $manager->persist($service);
            $manager->flush();
        }

        $Salarie = new Salarie();
        if (!$manager->find(Salarie::class, 1)) {
            $Salarie->setNom('Jost');
            $Salarie->setPrenom('Elisa');
            $Salarie->setRoles(["ROLE_RESPONSABLE_RH", "ROLE_RESPONSABLE_SERVICE", "ROLE_SALARIE"]);
            $Salarie->setPassword($this->encoder->encodePassword($Salarie, 'admin'));
            $Salarie->setEmail('rh@delko.com');
            $Salarie->setTelephone(null);
            $Salarie->setService('Ressources humaines');
            $manager->persist($Salarie);
            $manager->flush();
        }

        $Salarie = new Salarie();
        if (!$manager->find(Salarie::class, 1)) {
            $Salarie->setNom('Bompais');
            $Salarie->setPrenom('Solal');
            $Salarie->setRoles(["ROLE_SALARIE"]);
            $Salarie->setPassword($this->encoder->encodePassword($Salarie, 'solal'));
            $Salarie->setEmail('bompais.solal@gmail.com');
            $Salarie->setTelephone(null);
            $Salarie->setService('Développement web');
            $manager->persist($Salarie);
            $manager->flush();
        }

        $Salarie = new Salarie();
        if (!$manager->find(Salarie::class, 1)) {
            $Salarie->setNom('Lartaud');
            $Salarie->setPrenom('Jérôme');
            $Salarie->setRoles(["ROLE_RESPONSABLE_SERVICE","ROLE_SALARIE"]);
            $Salarie->setPassword($this->encoder->encodePassword($Salarie, 'webmanager'));
            $Salarie->setEmail('web.manager@delko.com');
            $Salarie->setTelephone(null);
            $Salarie->setService('Développement web');
            $manager->persist($Salarie);
            $manager->flush();
        }
    }
}
