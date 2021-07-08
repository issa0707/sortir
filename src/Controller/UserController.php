<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/profil", name = "user_gererProfil")
     */
    public function gererProfil (UserRepository $userRepository,
                                EntityManagerInterface $entityManager,
                                Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $id = $this->getUser()->getId();
        //création de l'instance de User
        $user = $userRepository->find($id);

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $userForm->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'votre action a bien été prise en compte');

            return $this->redirectToRoute('accueil');
        }

        return $this->render('user/profil.html.twig', [
           'userForm' => $userForm->createView()
        ]);
    }

    /**
     * @Route("/profil/detail/{id}", name = "user_detailProfil")
     */
    public function detailProfil (int $id, UserRepository $userRepository) {

        $user = $userRepository->find($id);

        return $this->render('user/detail.html.twig', [
            'user' => $user
        ]);
    }
}
