<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\AnnulationSortieType;
use App\Form\RechercheSortieType;
use App\Form\SortieModifierType;
use App\Form\SortieType;
use App\Outils\MiseAJourSorties;
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
    public function listeSortie(Request $request,EtatRepository $etatRepository,SortieRepository $sortieRepository,EntityManagerInterface $entityManager):Response{

        //maj des sortie
        $maj=new MiseAJourSorties($entityManager,$sortieRepository,$etatRepository);
        $maj->majAuto();

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
                $etat = $etatRepository->findOneByLibelle("créée");
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
        //recuperation de la sortie en fonction de l'id fourni en parametre
        $sortie = $sortieRepository->find($id);

        //si la sortie est a l'etat crée ou ouverte
        if($sortie->getEtat()->getLibelle()==="créée" || $sortie->getEtat()->getLibelle()==="ouverte") {

            //recuperation d'utilisateur session
            $user=$this->getUser();
            //creation formulaire avec la sortie en parametre
            $sortieModifierForm = $this->createForm(SortieModifierType::class, $sortie);
            //recuperation des donnee
            $sortieModifierForm->handleRequest($request);


            //si le formulaire a ete fourni et valide
            if ($sortieModifierForm->isSubmitted() && $sortieModifierForm->isValid() ) {
                //si on a clique sur enregistré
                if($request->request->get('enregistrer')){

                    //on set l'etat a crée
                    $sortie->setEtat($etatRepository->findOneByLibelle("créée"));
                }

                //si on a clique sur publier
                if($request->request->get('publier')){
                    //on set l'etat a ouvert
                    $sortie->setEtat($etatRepository->findOneByLibelle("ouverte"));
                }

                //si on a cliquer sur supprimer
                if($request->request->get('supprimer') && $sortie->getEtat()->getLibelle()=='créée') {


                    $this->supprime($sortie->getId(),$entityManager,$sortieRepository);
                    //redirection vers l'accueil
                    return $this->redirectToRoute('sortie_listeSortie');

                }

                //on met l'orgnisateur
                $sortie->setOrganisateur($user);

                //on persite et flush la sortie
                $entityManager->persist($sortie);
                $entityManager->flush();

                //ajout message
                $this->addFlash('success', 'La sortie a été modifiée avec succès !');
                //redirection vers le detail de la sortie
                return $this->redirectToRoute('sortie_detail', [
                           'id'=>$sortie->getId(),
                ]);

            }
            //redirection vers la page avec formulaire
            return $this->render('sortie/modifier.html.twig', [
                'sortieModifierForm' => $sortieModifierForm->createView(),
            ]);
        }

        //ajout d'un message ng
        $this->addFlash('error', 'Vous ne pouvez pas modifier cette sortie !');
        //redirection vers l'accueil
        return $this->redirectToRoute('sortie_listeSortie');
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

        //recuperation de la date
        $date=new \DateTime('now');
        // si la sortie est ouverte

        if ($sortie->getEtat()->getLibelle() == "ouverte" ) {
            // si la date est inferieur a aujourd'hui
            if ($sortie->getDateLimiteInscription() > $date) {
                // si il reste des place
                if ($sortie->getNbMaxInscription() > $sortie->getParticipation()->count()) {
                    // verif si user est deja inscrit
                    //creation d'une variable memoire
                    $memoire = 0;
                    foreach ($sortie->getParticipation() as $participant) {
                        if ($participant == $user) {
                            //si on trouve l'utilisateur on passe la memoire a 1
                            $memoire = 1;
                        }
                    }
                    //si l'user est pas deja inscrit
                    if ($memoire == 0) {

                        //si l'etat est ouvert et que le nombre d'inscrit ateindra le nombre max alors on passe en cloturer
                        if ($sortie->getEtat()->getLibelle() == "ouverte" &&
                            $sortie->getNbMaxInscription()-1==$sortie->getParticipation()->count()){
                            $sortie->setEtat($etatRepository->findOneByLibelle("cloturer"));
                        }
                        //inscription
                        $sortie->getParticipation()->add($user);
                        $entityManager->persist($sortie);
                        $entityManager->flush();
                        //message valider
                        $this->addFlash('success', 'vous etes inscrits ! ');
                    } else {
                        // message incription imposible : vous etes deja inscrit
                        $this->addFlash('error', 'vous etes deja inscrits ! ');
                    }
                } else {
                    // message incription imposible : le nombre max deja atteind
                    $this->addFlash('error', 'le nombre max de participant deja atteind ! ');
                }
            } else {
                //message incription imposible : la date d'inscription est depasser
                $this->addFlash('error', 'la date d\'inscription depassé ! ');
            }
            //
        } else {
            //message incription imposible : la sortie n'est pas ouverte
            $this->addFlash('error', 'la sortie n\'est pas ouverte ! ');
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
        if($sortie->getEtat()->getLibelle() == "ouverte"||$sortie->getEtat()->getLibelle() == "cloturer"){
            // si la date est inferieur a aujourd'hui
            if($sortie->getDateLimiteInscription()>$date){
                // si il reste des place
                if($sortie->getNbMaxInscription()>$sortie->getParticipation()->count()) {
                    // verif si user est deja inscrit
                    //Todo repasser la sortie a ouverte si elle est cloturer
                    //creation d'une variable memoire
                    $memoire = 0;
                }
                foreach ($sortie->getParticipation() as $participant){
                    if($participant==$user){
                        //si on trouve l'utilisateur on passe la memoire a 1
                        $memoire=1;
                    }
                }
                //si l'user est bien inscrit
                if($memoire==1){


                    if ($sortie->getEtat()->getLibelle() == "cloturer" &&
                        $sortie->getParticipation()->count()==$sortie->getNbMaxInscription())
                    {
                        $sortie->setEtat($etatRepository->findOneByLibelle("ouverte"));
                    }

                    //desinscription
                    $sortie->getParticipation()->removeElement($user);
                    $entityManager->persist($sortie);
                    $entityManager->flush();
                    //message valider
                    $this->addFlash('success', 'vous vous etes désistés ! ');
                }
                else {
                    // message incription imposible : vous etes n'etes pas inscrit
                    $this->addFlash('error', 'vous n\'etiez pas inscrits ! ');
                }
            }
            else{
                //message incription imposible : la date d'inscription est depasser
                $this->addFlash('error', 'la date d\'inscription depassé ! ');
            }
            //
        }
        else{
            //message incription imposible : la sortie n'est pas ouverte
            $this->addFlash('error', 'la sortie n\'est pas ouverte ou cloturer! ');
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
        if($sortie->getOrganisateur()==$user and ($sortie->getEtat()->getLibelle()=='créée' or $sortie->getEtat()->getLibelle()=='ouverte' )){
            $annulationForm = $this->createForm(AnnulationSortieType::class, $sortie);
            $annulationForm->handleRequest($request);
            if ($annulationForm->isSubmitted() && $annulationForm->isValid() ) {
                $etat=$etatRepository->findOneByLibelle("annulée");
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

    /**
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param EtatRepository $etatRepository
     * @param SortieRepository $sortieRepository
     * @Route ("sortie/supprime/{id}", name="sortie_supprime" ,requirements={"id" : "\d+"})
     */
    public function supprime(int $id, EntityManagerInterface $entityManager,
                             SortieRepository $sortieRepository){


        //recherche de la sortie
        $sortie=$sortieRepository->find($id);
        if($sortie->getEtat()->getLibelle()=="créée"||$sortie->getOrganisateur()==$this->getUser()) {
            //TODO mettre une alerte
            //suppression de la ligne
            $entityManager->remove($sortie);
            //on flush la sortie
            $entityManager->flush();

            //ajout message
            $this->addFlash('success', 'La sortie a été supprimée avec succès !');
        }else{
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cette sortie !');
        }
        //redirection vers l'accueil
        return $this->redirectToRoute('sortie_listeSortie');

    }
}
