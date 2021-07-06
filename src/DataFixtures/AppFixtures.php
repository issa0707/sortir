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
use Symfony\Component\Security\Core\User\UserInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $campus1 = new Campus();
        $campus2 = new Campus();
        $campus1->setNom("Rennes");
        $campus2->setNom("Nantes");
        $manager->persist($campus1);
        $manager->persist($campus2);

        $etat1 = new Etat();
        $etat1->setLibelle("ouverte");
        $etat2 = new Etat();
        $etat2->setLibelle("cloturée");
        $manager->persist($etat1);
        $manager->persist($etat2);

        for ($i=0; $i <5; $i++){
            $user = new User();

            $user->setPseudo("Rissa".$i);
            $user->setNom("Rissa".$i);
            $user->setPrenom("Dupont");
            $user->setTelephone("0000000000");
            $user->setMail("rissa".$i."@machin.fr");
            $user->setPassword(passwordEncoder->encodePassword(
                $user,
                '123'
            );
            $user->setActif(true);
            if ($i==1 || $i==3 || $i==5) {
                $user->setRoles(["USER"]);
                $user->setCampus($campus1);
            } else {
                $user->setRoles(["ADMIN"]);
                $user->setCampus($campus2);
            }
            $manager->persist($user);

            $date = new \DateTime();

            $ville = new Ville();
            $lieu = new Lieu();
            $sortie = new Sortie();
            $ville->setNom("ville".$i);
            $ville->setCodePostal("00000");
            $lieu->setNom("lieu".$i);
            $lieu->setLatitude($i);
            $lieu->setLongitude($i);
            $lieu->setRue("rue".$i);
            $lieu->setVille($ville);
            $sortie->setNom("sortie".$i);
            $sortie->setDateHeureDebut(new \DateTime());
            $sortie->setDuree($i*10);
            $sortie->setDateLimiteInscription($date);
            $sortie->setNbMaxInscription($i+1);
            $sortie->setLieu($lieu);
            $sortie->setInfoSortie("blalalalalalalala");
            $sortie->setOrganisateur($user);

            if ($i==1 || $i==3 || $i==5) {
                $sortie->setEtat($etat1);
                $sortie->setCampus($campus1);
            } else {
                $sortie->setEtat($etat2);
                $sortie->setCampus($campus2);
            }
            $manager->persist($ville);
            $manager->persist($lieu);
            $manager->persist($sortie);
        }

        $manager->flush();
    }
}
