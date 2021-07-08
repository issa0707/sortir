<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class ,
                'choice_label' => 'nom'
            ])
            ->add('contient',TextType::class,[
                'label'=>'Le nom de sortie contient :',
                'required'=>false
            ])
            ->add('apres', DateType::class,[
                'label'=>'Entre le :',
                'required'=>false
            ])
            ->add('avant',DateType::class,[
                'label'=>' Et le :',
                'required'=>false
            ])
            ->add('organise',CheckboxType::class,[
                'label'=>'Sortie que j\'organise',
                'required'=>false
            ])
            ->add('inscrit',CheckboxType::class,[
                'label'=>'Sortie où je suis inscrit',
                'required'=>false
            ])
            ->add('pasInscrit',CheckboxType::class,[
                'label'=>'Sortie où je ne suis pas inscrit',
                'required'=>false
            ])
            ->add('passees',CheckboxType::class,[
                'label'=>'Sortie passées',
                'required'=>false
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
