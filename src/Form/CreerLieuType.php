<?php

namespace App\Form;

use App\Entity\Lieu;
use Proxies\__CG__\App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreerLieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,[
                'label_attr'=>['class'=>'col-5'],
                'attr'=>['class'=>'col-4']])
            ->add('rue',TextType::class,[
                'label_attr'=>['class'=>'col-5'],
                'attr'=>['class'=>'col-4']])
            ->add('latitude',IntegerType::class,[
                'label_attr'=>['class'=>'col-5'],
                'attr'=>['class'=>'col-4']
            ])
            ->add('longitude',IntegerType::class,[
                'label_attr'=>['class'=>'col-5'],
                'attr'=>['class'=>'col-4']
            ])
            ->add('ville', EntityType::class, [
                'class' =>Ville::class,
                'choice_label' => 'nom',
                'label_attr'=>['class'=>'col-5'],
                'attr'=>['class'=>'col-4']
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
