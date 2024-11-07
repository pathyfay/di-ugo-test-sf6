<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                    'label' => 'Nom :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'nom',
                    ],
                    'label_attr' => [
                        'class' => 'fw-bold py-3 px-1',
                    ],
                    'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('short_nom', TextType::class, [
                    'label' => 'Short Nom :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'short nom',
                    ],
                    'label_attr' => [
                        'class' => 'fw-bold py-3 px-1',
                    ],
                    'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('reference', TextType::class, [
                    'label' => 'Réference :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'réference',
                    ],
                    'label_attr' => [
                        'class' => 'fw-bold py-3 px-1',
                    ],
                    'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('description', TextareaType::class, [
                    'label' => 'Description :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'description',
                    ],
                    'label_attr' => [
                        'class' => 'fw-bold py-3 px-1',
                    ],
                    'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('price', TextType::class, [
                'label' => 'Prix :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'description',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3']
            ])
            ->add('picture', FileType::class, [
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
                        'mimeTypes' => ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
