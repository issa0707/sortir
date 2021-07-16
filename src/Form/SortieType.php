<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'label' => 'Nom de la sortie:',
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-4']
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie:',
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-4 d-inline ']
            ])
            ->add('duree', IntegerType::class, [
                'label'=> 'Durée:',
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-4']
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription:',
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-4 d-inline'],

            ])
            ->add('nbMaxInscription', IntegerType::class,[
                'label' => 'Nombre de places:',
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-4 ']
            ])
            ->add('infoSortie', TextareaType::class, [
                'label'=> 'Information sur la sortie:',
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-4']
            ])
            ->add('ville', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'ville.nom',
                'mapped' => false,
                'label_attr'=>['class'=>'col-4']

            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'label_attr'=>['class'=>'col-6'],
                'attr'=>['class'=>'col-4']

            ])
          // ->add('Enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
