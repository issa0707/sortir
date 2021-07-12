<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\AnnulationSortieType;
use App\Form\RechercheSortieType;
use App\Form\SortieModifierType;
use App\Form\SortieType;
use App\Outils\RechercheSortieClass;
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
            $resultat=$sortieRepository->rechercheSortie($rechercheSortieForm->getData());
        }
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
        $sortie = new Sortie;
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

        if($sortie->getEtat()->getLibelle()==="creee" || $sortie->getEtat()->getLibelle()==="ouverte") {

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
            if($request->request->get('supprimer')) {
                return $this->redirectToRoute('sortie_annulation');

            }

            $sortie->setOrganisateur($user);

            $entityManager->persist($sortie);
            $entityManager->flush();

                   return $this->redirectToRoute('sortie_detail', [
                       'id'=>$sortie->getId(),
                    ]);

                }
                return $this->render('sortie/modifier.html.twig', [
                    'sortieModifierForm' => $sortieModifierForm->createView(),
                ]);
                }

        $this->addFlash('error', 'Vous ne pouvez pas modifier cette sortie !');
        return $this->redirectToRoute('accueil');
    }
    /**
     * @Route("sortie/inscription/{id}", name="sortie_inscription" ,requirements={"id" : "\d+"})
     */
    public function inscription( $id ,SortieRepository $sortieRepository, EtatRepository $etatRepository,EntityManagerInterface $entityManager):Response{
        //recherche de la sortie
        $sortie=new Sortie;
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

    /**
    * @Route("sortie/annulation/{id}", name="sortie_annulation" ,requirements={"id" : "\d+"})
     */
    public function annulerSortie($id,Request $request,
                                  EtatRepository $etatRepository,
                                  SortieRepository $sortieRepository,
                                  EntityManagerInterface $entityManager):Response{
        $sortie=new Sortie;
        $sortie=$sortieRepository->find($id);
        $user=$this->getUser();
        if($sortie->getOrganisateur()==$user and ($sortie->getEtat()->getLibelle()=='creee' or $sortie->getEtat()->getLibelle()=='ouverte' )){
            $annulationForm = $this->createForm(AnnulationSortieType::class, $sortie);
            $annulationForm->handleRequest($request);
            if ($annulationForm->isSubmitted() && $annulationForm->isValid() ) {
                $etat=$etatRepository->findOneByLibelle("annulee");
                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();
                return $this->render('sortie/detailSortie.html.twig',[
                    'sortie'=>$sortie
                ]);
            }
            return $this->render('sortie/annulation.html.twig', [
                'annulationForm' => $annulationForm->createView(),
                'sortie'=>$sortie
            ]);
        }
        else{
            // message annulation impossible
            return $this->render('sortie/detailSortie.html.twig',[
                'sortie'=>$sortie
            ]);
        }


    }
}
