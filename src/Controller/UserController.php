<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Outils\chargerPhoto;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function PHPUnit\Framework\isEmpty;

class UserController extends AbstractController
{
    /**
     * @Route("/profil", name = "user_gererProfil")
     */
    public function gererProfil (UserRepository $userRepository,
                                EntityManagerInterface $entityManager,
                                Request $request,
                                 UserPasswordEncoderInterface $passwordEncoder,
                                 chargerPhoto $chargerPhoto): Response
    {

        $user= $this->getUser();
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
            if($userForm->get('nouvellePhoto')->getData() !=null) {

                //récupération de l'objet UploadFile qui contient mon image
                $file = $userForm->get('nouvellePhoto')->getData();

                /**
                 * @var UploadedFile $file
                 */
                $directory = $this->getParameter('photo_profil');
                $fileName = $chargerPhoto->enregistrer($user->getUsername(), $directory, $file);
                $user->setPhoto($fileName);

            }
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'votre action a bien été prise en compte');

            return $this->render('user/detail.html.twig', [
                'user' => $user
            ]);
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
