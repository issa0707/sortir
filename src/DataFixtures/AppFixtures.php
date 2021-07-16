<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;



class AppFixtures extends Fixture



{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder=$passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {


        //aujourd'hui -25j
        $date = new \DateTime();
        $date->sub(new \DateInterval("P25D"));

        //creation d'un faker
        $faker=Faker\Factory::create('fr_FR');


        //mise en place des campus
        $this->campus($manager);

        //mise en place des etat
        $this->etat($manager);

        $campus=$manager->getRepository(Campus::class)->findAll();
        $etats=$manager->getRepository(Etat::class)->findAll();
        for($i=0;$i<250;$i++) {
            //creation d'une ville
            $this->ville($manager, $faker);
        }
        $villes=$manager->getRepository(Ville::class)->findAll();
        for($i=0;$i<250;$i++) {
            //creation d'un user
            $this->user($manager, $faker, $campus);
        }
        $users=$manager->getRepository(User::class)->findAll();
        for($i=0;$i<250;$i++) {
            //creation de  lieu
            $this->lieu($manager, $faker,$villes);
        }
        $lieux=$manager->getRepository(Lieu::class)->findAll();
        for($i=0;$i<250;$i++) {
            //creation de sorties
            $this->sortie($manager,$faker,$date,$etats,$lieux,$users,$campus);
        }
    }
    private function etat(ObjectManager $manager){
        $etat1 = $this->creeEtat("créée");
        $etat2 = $this->creeEtat("ouverte");
        $etat3 = $this->creeEtat("cloturée");
        $etat4 = $this->creeEtat("enCours");
        $etat5 = $this->creeEtat("passée");
        $etat6 = $this->creeEtat("archivée");
        $etat7 = $this->creeEtat("annulée");
        $lesEtats=[$etat1,$etat2,$etat3,$etat4,$etat5,$etat6,$etat7];
        foreach ($lesEtats as $unEtat){
            $manager->persist($unEtat);
        }
        $manager->flush();
    }
    private function campus(ObjectManager $manager){
        $campus1 = $this->creeCampus("Nantes");
        $campus2 = $this->creeCampus("Rennes");
        $campus3 = $this->creeCampus("niort");
        $campus4 = $this->creeCampus("brest");
        $lesCampus=[$campus1,$campus2,$campus3,$campus4];

        foreach ($lesCampus as $unCampus){
            $manager->persist($unCampus);
        }
        $manager->flush();
    }
    private function ville(ObjectManager $manager, $faker){

        //creation d'un code postal
        $cp=str_pad(rand(1,99999), 5, "0", STR_PAD_LEFT);
        //creation d'une ville
        $ville = $this->creeVille($faker->city.$this->uniInt(),$cp);
        $manager->persist($ville);
        $manager->flush();
    }
    private function user(ObjectManager $manager,$faker,$campus){

        //creation d'un user
        $pseudo = $faker->userName.$this->uniInt();
        $nom=$faker->lastName.$this->uniInt();
        $prenom=$faker->firstName.$this->uniInt();
        $tel="00000000000";
        $email=$faker->email.$this->uniInt();
        $mdp=123;
        $act=true;
        $roleAleatoire=rand(0,1);
        if($roleAleatoire==0){
            $role="ROLE_USER";
        }else{
            $role="ROLE_ADMIN";
        }
        $random=rand(0,(count($campus))-1);
        $campusUser=$campus[$random];
        $user=$this->creeUser($pseudo,$nom,$prenom,$tel,$email,$mdp,$act,$role,$campusUser);
        $manager->persist($user);
        $manager->flush();
    }
    private function sortie(ObjectManager $manager,$faker,\DateTime $date,$etats,$lieux,$users,$campus){

        $nomSortie=$faker->company.$this->uniInt();
        $coef=rand(0,50);
        $interval="P".$coef."D";
        $datelim=new \DateTime($date->format('Y-m-d H:i:s'));
        $datelim->add(new \DateInterval($interval));
        $duree=rand(0,240);
        $dateSortie=new \DateTime($datelim->format('Y-m-d H:i:s'));
        $dateSortie->add(new \DateInterval("P10D"));
        $maxPart=rand(5,25);
        $info="des infos sur la sortie";
        $randomEtat=rand(0,(count($etats))-1);
        $etatSortie=$etats[$randomEtat];
        $randomLieu=rand(0,(count($lieux))-1);
        $lieu=$lieux[$randomLieu];
        $randomUser=rand(0,(count($users))-1);
        $user=$users[$randomUser];
        $randomCampus=rand(0,(count($campus))-1);
        $campusUser=$campus[$randomCampus];
        $sortie=$this->creeSortie($nomSortie,$dateSortie,$duree,$datelim,$maxPart,$lieu,$info,$user,$etatSortie,$campusUser);
        $manager->persist($sortie);
        $randomParticipation=rand(0,$maxPart);
        for($i=0;$i<$randomParticipation;$i++){
            $sortie=$this->participation($sortie,$users);
        }
        $manager->persist($sortie);
        $manager->flush();
    }
    private function participation(Sortie $sortie,$users):Sortie{
        $randomUser=rand(0,(count($users))-1);
        $user=$users[$randomUser];
        $sortie->addParticipation($user);
        return $sortie;
    }
    private function lieu(ObjectManager$manager,$faker,$villes){

        $lat = $faker->latitude.$this->uniInt();
        $lon = $faker->longitude.$this->uniInt();
        $name=$faker->sha256;
        $rue=$faker->streetName.$this->uniInt();

        $random=rand(0,(count($villes))-1);
        $ville=$villes[$random];
        $lieu = $this->creeLieu($name, $lat, $lon,$rue , $ville);
        $manager->persist($lieu);
        $manager->flush();
    }
    private function uniInt():int{
        $rand=rand(0,500);
        return $rand;
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
