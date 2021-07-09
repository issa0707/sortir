<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\RechercheVillesType;
use App\Form\VilleType;
use App\Outils\RechercheVillesClass;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    #[Route('/ville', name: 'ville')]
    public function index(): Response
    {
        return $this->render('ville/index.html.twig', [
            'controller_name' => 'VilleController',
        ]);
    }

    /**
     * @Route("/admin/gererVille",name="admin_ville-gererVille")
     */
    public function gererVille(EntityManagerInterface $entityManager, Request $request, VilleRepository $villeRepository):Response{
        $ville=new Ville;
        $villeForm=$this->createForm(VilleType::class,$ville);
        $rechercheVille=new RechercheVillesClass();
        $rechercheVilleForm=$this->createForm(RechercheVillesType::class,$rechercheVille);
        $rechercheVilleForm->handleRequest($request);
        $villeForm->handleRequest($request);
        if ($villeForm->isSubmitted() && $villeForm->isValid()) {

            $entityManager->persist($ville);
            $entityManager->flush();


        }
        $resultats=$villeRepository->rechercheVille($rechercheVille);
        return $this->render('ville/gererVille.html.twig',[
            'villeForm'=>$villeForm->createView(),
            'rechercheVilleForm'=>$rechercheVilleForm->createView(),
            'resultats'=>$resultats
        ]);

    }
}
