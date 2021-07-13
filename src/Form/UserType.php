<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Pseudo',TextType::class,[
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-4']
            ])
            ->add('Prenom',TextType::class,[
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-4']
            ])
            ->add('Nom',TextType::class,[
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-4']
            ])
            ->add('Telephone',TextType::class,[
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-4']
            ])
            ->add('Mail', EmailType::class,[
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-4']
            ])
            ->add('password', RepeatedType::class, [

                'type' => PasswordType::class,
                'invalid_message' => 'le mot de passe ne correspond pas.',
                'required' => true,
                'first_options' => ['label' => 'Mot de passe',
                    'label_attr'=>['class'=>'col-4'],
                    'attr'=>['class'=>'col-4']
                ],
                'second_options' => ['label' => 'Confirmation',
                    'label_attr'=>['class'=>'col-4'],
                    'attr'=>['class'=>'col-4']
                ]
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-4']
            ])
            ->add('nouvellePhoto', FileType::class,[
                'required' => false,
                'label' => 'photo',
                'mapped' => false,
                'label_attr'=>['class'=>'col-4'],
                'attr'=>['class'=>'col-8']
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
