<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\DateTime;


class AppFixtures extends Fixture



{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder=$passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {

        $campus1 = $this->creeCampus("Nantes");
        $campus2 = $this->creeCampus("Rennes");
        $etat1 = $this->creeEtat("ouverte");
        $etat2 = $this->creeEtat("cloturée");
        $manager->persist($campus1);
        $manager->persist($campus2);
        $manager->persist($etat1);
        $manager->persist($etat2);

        for ($i=0; $i <5; $i++){
            if ($i==1 || $i==3 || $i==5) {
                $user = $this->creeUser(
                    "Rissa".$i,
                    "Rissa".$i,
                    "Dupont",
                    "0000000000",
                    "rissa".$i."@machin.fr",
                    '123',
                    true,
                    "ROLE_USER",$campus1);

            } else {
                $user = $this->creeUser("Rissa".$i,
                    "Rissa".$i,
                    "Dupont",
                    "0000000000",
                    "rissa".$i."@machin.fr",
                    '123',
                    true,
                    "ROLE_ADMIN",
                    $campus2);

            }
            $date = new \DateTime();
            $ville = $this->creeVille("ville".$i,"00000");
            $lieu = $this->creeLieu("lieu".$i,$i,$i,"rue".$i,$ville);
            if ($i==1 || $i==3 || $i==5) {
                $sortie=$this->creeSortie("sortie".$i,$date,$i*10,$date,$i+1,$lieu,"blalalalalalalala",$user,$etat1,$campus1);
            } else {
                $sortie=$this->creeSortie("sortie".$i,$date,$i*10,$date,$i+1,$lieu,"blalalalalalalala",$user,$etat2,$campus2);
            }
            $manager->persist($user);
            $manager->persist($ville);
            $manager->persist($lieu);
            $manager->persist($sortie);
        }
        $manager->flush();
    }
    private function creeEtat(String $string):Etat{
        $etat = new Etat();
        $etat->setLibelle($string);
        return $etat;
    }
    private function creeCampus(String $string):Campus{
        $campus = new Campus();
        $campus->setNom($string);
        return $campus;
    }
    /*TODO*/
    private function creeUser(String $pseudo,String $nom,String $prenom,String $tel,String $email,String $mdp,Bool $act,String $role,Campus $campus):user{
        $user = new User();
        $user->setPseudo($pseudo);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setTelephone($tel);
        $user->setMail($email);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $mdp));
        $user->setActif($act);
        $user->setRoles([$role]);
        $user->setCampus($campus);
        return $user;
    }
    private function creeVille(String $nom,String $cp):Ville{
        $ville=new Ville();
        $ville->setNom($nom);
        $ville->setCodePostal($cp);
        return $ville;
    }
    private function creeLieu(String $nom,int $lat,int $lon,String $rue,Ville $ville):Lieu{
        $lieu=new Lieu();
        $lieu->setNom($nom);
        $lieu->setLatitude($lat);
        $lieu->setLongitude($lon);
        $lieu->setRue($rue);
        $lieu->setVille($ville);
        return $lieu;
    }
    private function creeSortie(String $nom,\DateTime $datedeb,int $duree,\DateTime $datelim,
                                int $nbMax,Lieu $lieu,String $infoSor,User $orga,Etat $etat,Campus $campus):Sortie{
        $sortie=new Sortie;
        $sortie->setNom($nom);
        $sortie->setDateHeureDebut($datedeb);
        $sortie->setDuree($duree);
        $sortie->setDateLimiteInscription($datelim);
        $sortie->setNbMaxInscription($nbMax);
        $sortie->setLieu($lieu);
        $sortie->setInfoSortie($infoSor);
        $sortie->setOrganisateur($orga);
        $sortie->setEtat($etat);
        $sortie->setCampus($campus);
        return $sortie;
    }
}
