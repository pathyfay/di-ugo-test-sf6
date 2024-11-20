<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', ChoiceType::class, [
                'choices' => [
                    'Mr.' => 'Mr',
                    'Mme' => 'Mme',
                ],
                'label' => 'Title : ',
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-check-input d-flex justify-content-between gap-3',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Firstname :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Firstname',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3'],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Lastname :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Lastname',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3'],
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'Postal Code :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Postal Code',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3'],
            ])
            ->add('city', TextType::class, [
                'label' => 'City :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'City',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3'],
            ])
            ->add('email', TextType::class, [
                'label' => 'Email :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3'],
            ])
            ->add('mobile', TelType::class, [
                'label' => 'Numéro de téléphone :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre numéro de téléphone +33',
                    'inputmode' => 'tel',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3'],
                'required' => false
            ])
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez une date d\'anniversaire',
                ],
                'label' => 'Birthday',
                'label_attr' => [
                    'class' => 'fw-bold mb-2'
                ],
                'required' => false
            ])
            ->add('photo', FileType::class, [
                'label' => 'Télécharger une photo :',
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3'],
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new Assert\File([
                        'mimeTypes' => ['image/jpeg','image/jpg', 'image/png', 'image/gif'],
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
