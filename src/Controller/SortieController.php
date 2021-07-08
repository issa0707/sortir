<?php

namespace App\Controller;

use App\Form\RechercheSortieType;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
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

    /**
     * @param Request $request
     * @param SortieRepository $sortieRepository
     * @return Response
     * @Route("/sortie",name="sortie_listeSortie")
     */
    public function listeSortie(Request $request,SortieRepository $sortieRepository):Response{
        $rechercheSortieForm = $this->createForm(RechercheSortieType::class);
        $rechercheSortieForm->handleRequest($request);
        if($rechercheSortieForm->isSubmitted()){
            $resultat=$sortieRepository->rechercheSortie($rechercheSortieForm->getData());
        }
        else{
            $resultat= $sortieRepository->findall();
        }

        return $this->render('sortie/liste.html.twig',[
            'rechercheSortieForm'=>$rechercheSortieForm->createView(),
            'resultat'=>$resultat
        ]);
    }

    /**
     * @Route("sortie/detail/{id}",name = "sortie_detail",requirements={"id" : "\d+"})
     */
    public function detail($id,SortieRepository $rep):Response{
        $sortie=$rep->find($id);
        dd($sortie->getParticipation());
        return $this->render('sortie/detailSortie.html.twig',[
            'sortie'=>$sortie
        ]);
    }
}
