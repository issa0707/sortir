<?php

namespace App\Repository;

use App\Entity\Sortie;
use app\outils\RechercheSortieClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use function Doctrine\ORM\QueryBuilder;


/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{

    private $security;

    public function __construct(ManagerRegistry $registry,Security $security)
    {
        parent::__construct($registry, Sortie::class);
        $this->security=$security;

    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    //fonction de recherche de sortie en fonction des filtres

    public function rechercheSortie(RechercheSortieClass $getData)
    {

        $queryBulder=$this->createQueryBuilder('s');
        $queryBulder->join("s.campus","c")->addSelect("c");
        $queryBulder->join("s.etat","e")->addSelect("e");
        $queryBulder->join("s.lieu","l")->addSelect("l");
        $queryBulder->join("s.organisateur","o")->addSelect("o");

        $queryBulder->join("o.campus","oc")->addSelect("oc");
        $queryBulder->join("l.ville","v")->addSelect("v");


        //campus obligatoire

        $queryBulder->andWhere(
            $queryBulder->expr()->eq('s.campus',':campus')
        );
        $queryBulder->setParameter('campus',$getData->getCampus());


        //je teste si y a un text
        if($getData->getContient()!=null){
            $queryBulder->andWhere(
                $queryBulder->expr()->like('s.nom',':mot'));
            $queryBulder->setParameter('mot', '%'.$getData->getContient().'%');
        }

        //je teste les date si elle sont null je les remplace par des date arbitraire tres eloignée
        if($getData->getApres()==null){
            $getData->setApres("2020-01-01 00:00:00") ;
        }
        if($getData->getAvant()==null){
            $getData->setAvant("2026-01-01 00:00:00");
        }

        //je verifie que les date sont bien dans l'intervale
        $queryBulder->andWhere(
            $queryBulder->expr()->between('s.dateHeureDebut',':datemin',':datemax'));
        $queryBulder->setParameter('datemin', $getData->getApres());
        $queryBulder->setParameter('datemax', $getData->getAvant());


        //je test si la case organisteur est cocher
        if($getData->getOrganise()!=false){
            $queryBulder->andWhere(
                $queryBulder->expr()->eq('s.organisateur',':orga'));
            $queryBulder->setParameter('orga',$getData->getUser());
        }

        //je test si la case inscrit es cocher si ou je testerait pas la case pas inscrit
        if($getData->getInscrit()!=false){
            $queryBulder->andWhere(
                $queryBulder->expr()->isMemberOf(':participant','s.participation'));
            $queryBulder->setParameter('participant',$getData->getUser());
        }
        else{
            //je test la case pas inscrit
            if($getData->getPasInscrit()!=false){
                $queryBulder->andWhere(':nonParticipant NOT MEMBER OF s.participation ');
                $queryBulder->setParameter('nonParticipant',$getData->getUser());
            }
        }

        //je teste la case passée
        if($getData->getPassees()!=false){
            $queryBulder->andWhere(
                $queryBulder->expr()->eq('e.libelle',':etat'));
            $queryBulder->setParameter('etat','passée');
        }


        //je renvoye le resultat avec les filtres
         return $queryBulder->getQuery()->getResult();



    }
}
