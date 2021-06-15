<?php


namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\UserFormType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends BaseController
{
    private $userRepository;
    private $passwordEncoder;
    private $entityManager;
    private $roleRepository;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
    }

    public function fakepswd(Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $user = new User();
        $user->setEmail("mam@ddd.com")
            ->setNomComplet("nom comp")
            ->setUsername("mamless")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->passwordEncoder->encodePassword($user, $request->get("password")));
        // $user = $this->userRepository->saveUser($user);
        return $this->json(["id" => $user->getId(), "password" => $user->getPassword(), "decode" => $this->passwordEncoder->isPasswordValid($user, 1)]);
    }

    /**
     * @Route("/user",name="app_admin_users")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function users()
    {
        $users = $this->userRepository->findAll();
        return $this->render("admin/user/user.html.twig", ["users" => $users]);
    }

    /**
     * @Route("/user/new",name="app_admin_new_user")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function newUser(Request $request)
    {
        $form = $this->createForm(UserFormType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $password = $form["justpassword"]->getData();
            $role = $form["role"]->getData();
            $user->setAdmin(true)
                ->setPassword($this->passwordEncoder->encodePassword($user, $password))
                ->setRoles([$role->getRoleName()]);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash("success", "Utilisateur ajouté");
            return $this->redirectToRoute("app_admin_users");
        }
        return $this->render("admin/user/userform.html.twig", ["userForm" => $form->createView()]);
    }

    /**
     * @Route("/user/edit/{id}",name="app_admin_edit_user")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function editUser(User $user, Request $request)
    {
        $form = $this->createForm(UserFormType::class, $user);
        $form->get('justpassword')->setData($user->getPassword());
        $therole = $this->roleRepository->findOneBy(["roleName" => $user->getRoles()[0]]);
        $form->get('role')->setData($therole);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Role $role */
            $role = $form["role"]->getData();
            $password = $form["justpassword"]->getData();
            $user->setRoles([$role->getRoleName()]);
            if ($user->getPassword() != $password) {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash("success", "Utilisateur modifié");
            return $this->redirectToRoute("app_admin_users");
        }
        return $this->render("admin/user/userform.html.twig", ["userForm" => $form->createView()]);
    }

    /**
     * @Route("/user/delete/{id}",name="app_admin_delete_user")
     * @IsGranted("ROLE_RESPONSABLE_RH")
     */
    public function delete(User $user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return $this->json(["message" => "success", "value" => true]);
    }

}
