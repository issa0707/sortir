<?php


namespace App\Outils;


use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class MiseAJourSorties
{

    private \DateTime $date;
    private EntityManagerInterface $entityManager;
    private SortieRepository $sortieRepository;
    private EtatRepository $etatRepository;



    public function __construct(EntityManagerInterface $entityManager,SortieRepository $sortieRepository,EtatRepository $etatRepository)
    {
        //recuperation de la date
        $this->date=new \DateTime('now');
        //declarartion des variable
        $this->entityManager=$entityManager;
        $this->sortieRepository=$sortieRepository;
        $this->etatRepository=$etatRepository;


    }

    public function majAuto(){

        $this->majOuverte();
        $this->majcloturee();
        $this->majEnCours();
        $this->majPassee();
        $this->majAnnulee();

    }


    public function majOuverte(){



        //recuperation des sortie ouverte
        $sortiesOuverte=$this->sortieRepository->findByEtat($this->etatRepository->findOneByLibelle("ouverte"));
        //mise a jours etat si date inscription dépasser

        foreach ($sortiesOuverte as $sortie){
            if($sortie->getDateLimiteInscription()<=$this->date){

                $sortie->setEtat($this->etatRepository->findOneByLibelle("cloturée"));
                $this->entityManager->persist($sortie);
            }
        }
        $this->entityManager->flush();
    }

    public function majcloturee(){


        //recuperation sortie cloturée
        $sortiesCloturee=$this->sortieRepository->findByEtat($this->etatRepository->findOneByLibelle("cloturée"));
        //mise a jours si la date de debut est depasser
        foreach ($sortiesCloturee as $sortie){
            if($sortie->getDateHeureDebut()<=$this->date){
                $sortie->setEtat($this->etatRepository->findOneByLibelle("enCours"));
                $this->entityManager->persist($sortie);
            }
        }
        $this->entityManager->flush();
    }

    public function majEnCours(){


        //recuperation sortie enCours
        $sortiesEnCours=$this->sortieRepository->findByEtat($this->etatRepository->findOneByLibelle("enCours"));
        //mise a jours si la date de fin est depasser
        foreach ($sortiesEnCours as $sortie) {
           $dateFin= $sortie->getDateHeureDebut()->add(new \DateInterval('PT' . $sortie->getDuree(). 'M'));
           if($dateFin<$this->date){
               $sortie->setEtat($this->etatRepository->findOneByLibelle("passée"));
               $this->entityManager->persist($sortie);
           }
        }
        $this->entityManager->flush();
    }

    public function majPassee(){
        //recuperation sortie passée
        $sortiesPassee=$this->sortieRepository->findByEtat($this->etatRepository->findOneByLibelle("passée"));
        //mise a jours si la date de fin est depasser
        foreach ($sortiesPassee as $sortie) {
            $dateArchivage= $sortie->getDateHeureDebut()->add(new \DateInterval('P' . $sortie->getDuree(). 'M'));
            if($dateArchivage<$this->date){
                $sortie->setEtat($this->etatRepository->findOneByLibelle("archivée"));
                $this->entityManager->persist($sortie);
            }
        }
        $this->entityManager->flush();
    }
    public function majAnnulee(){

        //recuperation sortie annulée
        $sortiesAnnulee=$this->sortieRepository->findByEtat($this->etatRepository->findOneByLibelle("annulée"));
        //mise a jours si la date de fin est depasser
        foreach ($sortiesAnnulee as $sortie) {
            $dateArchivage= $sortie->getDateHeureDebut()->add(new \DateInterval('P' . $sortie->getDuree(). 'M'));
            if($dateArchivage<$this->date){
                $sortie->setEtat($this->etatRepository->findOneByLibelle("archivée"));
                $this->entityManager->persist($sortie);
            }
        }
        $this->entityManager->flush();
    }
}