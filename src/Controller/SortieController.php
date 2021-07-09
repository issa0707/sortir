<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\RechercheSortieType;
use App\Form\SortieType;
use App\Outils\RechercheSortieClass;
use App\Repository\EtatRepository;
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
     * @Route("/",name="sortie_listeSortie")
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

    /**
     * @Route("sortie/inscription/{id}", name="sortie_inscription" ,requirements={"id" : "\d+"})
     */
    public function inscription( $id ,SortieRepository $sortieRepository, EtatRepository $etatRepository,EntityManagerInterface $entityManager):Response{
        //recherche de la sortie
        $sortie=new Sortie();
        $sortie=$sortieRepository->find($id);
        //recuperation get user
        $user=$this->getUser();
        //recuperation etat ouvert
        $etat=$etatRepository->findOneByLibelle("ouverte");
        //recuperation de la date
        $date=new \DateTime('now');
         // si la sortie est ouverte
        if($sortie->getEtat()==$etat){
            // si la date est inferieur a aujourd'hui
            if($sortie->getDateLimiteInscription()<$date){
                // si il reste des place
                if($sortie->getNbMaxInscription()>$sortie->getParticipation()->count()){
                    // verif si user est deja inscrit
                    //creation d'une variable memoire
                    $memoire=0;
                    foreach ($sortie->getParticipation() as $participant){
                        if($participant==$user){
                            //si on trouve l'utilisateur on passe la memoire a 1
                            $memoire=1;
                        }
                    }
                    if($memoire==0){

                        //inscription
                        $sortie->getParticipation()->add($user);
                        $entityManager->persist($sortie);
                        $entityManager->flush();
                        //message valider
                        //$this->$this->addFlash('success', 'vous etes inscrit ! ');
                    }
                    else{
                       // message incription imposible : vous etes deja inscrit
                    }
                }
                else{
                    // message incription imposible : le nombre max deja atteind
                }
            }
            else{
                //message incription imposible : la date d'inscription est depasser
            }
            //
        }
        else{
            //message incription imposible : la sortie n'est pas ouverte
        }
        return $this->render('sortie/detailSortie.html.twig',[
            'sortie'=>$sortie
        ]);
    }
    /**
     * @Route("sortie/desinscription/{id}", name="sortie_desinscription" ,requirements={"id" : "\d+"})
     */
    public function desinscription( $id ,SortieRepository $sortieRepository, EtatRepository $etatRepository,EntityManagerInterface $entityManager):Response{
        //recherche de la sortie
        $sortie=new Sortie();
        $sortie=$sortieRepository->find($id);
        //recuperation get user
        $user=$this->getUser();
        //recuperation etat ouvert
        $etat=$etatRepository->findOneByLibelle("ouverte");
        //recuperation de la date
        $date=new \DateTime('now');
        // si la sortie est ouverte
        if($sortie->getEtat()==$etat){
            // si la date est inferieur a aujourd'hui
            if($sortie->getDateLimiteInscription()<$date){
                // si il reste des place
                if($sortie->getNbMaxInscription()>$sortie->getParticipation()->count()){
                    // verif si user est deja inscrit
                    //creation d'une variable memoire
                    $memoire=0;
                    foreach ($sortie->getParticipation() as $participant){
                        if($participant==$user){
                            //si on trouve l'utilisateur on passe la memoire a 1
                            $memoire=1;
                        }
                    }
                    if($memoire==1){

                        //inscription

                        $sortie->getParticipation()->removeElement($user);
                        $entityManager->persist($sortie);
                        $entityManager->flush();
                        //message valider
                        //$this->$this->addFlash('success', 'vous etes inscrit ! ');
                    }
                    else{
                        // message incription imposible : vous etes n'etes pas inscrit
                    }
                }
                else{
                    // message incription imposible : le nombre max deja atteind
                }
            }
            else{
                //message incription imposible : la date d'inscription est depasser
            }
            //
        }
        else{
            //message incription imposible : la sortie n'est pas ouverte
        }
        return $this->render('sortie/detailSortie.html.twig',[
            'sortie'=>$sortie
        ]);
    }
}
