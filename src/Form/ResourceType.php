<?php

namespace App\Form;

use App\Entity\Operation;
use App\Entity\Organisme;
use App\Entity\Resource;
use App\Entity\TypeResource;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EntityType::class, [
                'class' => TypeResource::class,
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez Type',
                ],
                'label' => 'Type',
                'label_attr' => [
                    'class' => 'fw-bold mb-2'
                ]
            ])
            ->add('montant_total', NumberType::class, [
                'label' => 'Montant Total :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'ex.: 100.00',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('montant_restant', NumberType::class, [
                'label' => 'Montant Restant :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'ex.: 10.00',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('mensualite', NumberType::class, [
                'label' => 'mensualite :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'ex.: 2',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('taux', NumberType::class, [
                'label' => 'Taux :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'ex.: 0.2',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('reserve', NumberType::class, [
                'label' => 'Reserve :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'ex.: 25',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez une date du debut',
                ],
                'label' => 'Date du debut',
                'label_attr' => [
                    'class' => 'fw-bold mb-2'
                ]
            ])
            ->add('date_fin', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez une date de fin',
                ],
                'label' => 'Date de fin',
                'label_attr' => [
                    'class' => 'fw-bold mb-2'
                ]
            ])
            ->add('date_prelevement', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez une date de prelevement',
                ],
                'label' => 'Date de prelevement',
                'label_attr' => [
                    'class' => 'fw-bold mb-2'
                ]
            ])
            ->add('operation', EntityType::class, [
                'class' => Operation::class,
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez Operation',
                ],
                'label' => 'Operation Id',
                'label_attr' => [
                    'class' => 'fw-bold mb-2'
                ]
            ])
            ->add('organisme', EntityType::class, [
                'class' => Organisme::class,
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez l\' Organisme',
                ],
                'label' => 'Organisme Id',
                'label_attr' => [
                    'class' => 'fw-bold mb-2'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resource::class,
        ]);
    }
}
