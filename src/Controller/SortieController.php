<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\RechercheSortieType;
use App\Form\SortieModifierType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
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

        return $this->render('sortie/detailSortie.html.twig',[
            'sortie'=>$sortie
        ]);
    }

    /**
     * @Route("sortie/creer",name = "sortie_creer")
     */
    public function creer(EntityManagerInterface $entityManager, Request $request, EtatRepository $etatRepository) {
           $sortie = new Sortie();
        $user=$this->getUser();
        $sortie->setCampus($user->getCampus());
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);


        if ($sortieForm->isSubmitted() && $sortieForm->isValid() ) {
            if($request->request->get('enregistrer')){
                $etat = $etatRepository->findOneByLibelle("creee");
                $sortie->setEtat($etat);
            }

            if($request->request->get('publier')){
                $etat = $etatRepository->findOneByLibelle("ouverte");
                $sortie->setEtat($etat);

            }
            $sortie->setOrganisateur($user);

            $entityManager->persist($sortie);
            $entityManager->flush();

               return $this->redirectToRoute('sortie_detail', ['id'=>$sortie->getId()]);//endif
        }
        return $this->render('sortie/creer.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            ]);
    }

    /**
     * @Route("sortie/modifier/{id}",name = "sortie_modifier")
     */
    public function modifier(int $id, EntityManagerInterface $entityManager,
                             Request $request,
                             EtatRepository $etatRepository,
                             SortieRepository $sortieRepository) {

        $sortie = $sortieRepository->find($id);
        $user=$this->getUser();
        $sortieModifierForm = $this->createForm(SortieModifierType::class, $sortie);
        $sortieModifierForm->handleRequest($request);


        if ($sortieModifierForm->isSubmitted() && $sortieModifierForm->isValid() ) {
            if($request->request->get('enregistrer')){
                $etat = $etatRepository->findOneByLibelle("creee");
                $sortie->setEtat($etat);
            }

            if($request->request->get('publier')){
                $etat = $etatRepository->findOneByLibelle("ouverte");
                $sortie->setEtat($etat);

            }
            $sortie->setOrganisateur($user);

            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_detail', ['id'=>$sortie->getId()]);//endif
        }
        return $this->render('sortie/modifier.html.twig', [
            'sortieModifierForm' => $sortieModifierForm->createView(),
        ]);
    }
}
