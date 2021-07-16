<?php

namespace App\Controller;
use App\Entity\Lieu;
use App\Form\CreerLieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class LieuController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param LieuRepository $lieuRepository
     * @return Response
     * @Route ("lieu/creer", name="lieu_creer")
     */
   public function creerLieu(EntityManagerInterface $entityManager, Request $request,LieuRepository $lieuRepository){
       $lieu=new Lieu;
       $creerLieuForm=$this->createForm(CreerLieuType::class,$lieu);
       $creerLieuForm->handleRequest($request);
       if($creerLieuForm->isSubmitted() && $creerLieuForm->isValid()){
           $entityManager->persist($lieu);
           $entityManager->flush();
           $this->addFlash('success', 'Lieu ajouté ! ');
           return $this->redirectToRoute('sortie_creer');
       }
       return $this->render('lieu/creerLieu.html.twig',[
           'creerLieuForm'=>$creerLieuForm->createView()
       ]);   }}
