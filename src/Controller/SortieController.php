<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\RechercheSortieType;
use App\Form\SortieType;
use App\Outils\RechercheSortieClass;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /*#[Route('/sortie', name: 'sortie')]
    public function index(SortieRepository $rep , UserRepository $urep): Response
    {


        $resultat= $rep->findall();
        $rechercheSortieForm = $this->createForm(RechercheSortieType::class);
        return $this->render('sortie/liste.html.twig',[
            'rechercheSortieForm'=>$rechercheSortieForm->createView(),
            'resultat'=>$resultat
        ]);
    }*/


    //fonction qui affiche la page liste de sortie en fonction des filtre
    /**
     * @param Request $request
     * @param SortieRepository $sortieRepository
     * @return Response
     * @Route("/sortie",name="sortie_listeSortie")
     */
    public function listeSortie(Request $request,SortieRepository $sortieRepository):Response{
        //cretaion de l'entité et du formulaire
        $rechercheSortie = new RechercheSortieClass();
        $rechercheSortieForm = $this->createForm(RechercheSortieType::class,$rechercheSortie);


        //recuperation du resultat
        $rechercheSortieForm->handleRequest($request);

        //si le formulaire a deja ete soumis
        if($rechercheSortieForm->isSubmitted()){
            //insertion de l'utilisateur dans l'entité
            $rechercheSortie->setUser($this->getUser());
            //recuperation du resultat de la requete
            $resultat=$sortieRepository->rechercheSortie($rechercheSortie);
        }
        //au premier passage
        else{
            //recuperation de toutes les sortie
            $resultat= $sortieRepository->findall();
        }
        //renvoi vers la page aves le formulaire et les resultat de la requete
        return $this->render('sortie/liste.html.twig',[
            'rechercheSortieForm'=>$rechercheSortieForm->createView(),
            'resultat'=>$resultat
        ]);
    }

    //fonction qui affiche une sortie en detail
    /**
     * @Route("sortie/detail/{id}",name = "sortie_detail",requirements={"id" : "\d+"})
     */
    public function detail($id,SortieRepository $rep):Response{
        //recuperation de l'entité de la sortie
        $sortie=$rep->find($id);

        //renvoi vers la page avec l'entité
        return $this->render('sortie/detailSortie.html.twig',[
            'sortie'=>$sortie
        ]);
    }

    /**
     * @Route("sortie/creer",name = "sortie_creer")
     */
    public function creer(EntityManagerInterface $entityManager, Request $request) {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->$this->addFlash('success', 'sortie créée ! ');
        }

    }
}
