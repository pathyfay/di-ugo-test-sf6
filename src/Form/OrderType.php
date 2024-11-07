<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdAt', DateType::class, [
                    'widget' => 'single_text',
                    'html5' => true,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Sélectionnez Order date',
                    ],
                    'label' => 'Order date',
                    'label_attr' => [
                        'class' => 'fw-bold mb-2'
                    ]
                ]
            )
            ->add('quantity', TextType::class, [
                'label' => 'Quantity :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'quantity',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3']])
            ->add('products', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'id',
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez Product Id',
                ],
                'label' => 'Product Id',
                'label_attr' => [
                    'class' => 'fw-bold mb-2'
                ]
            ])
            ->add('tva', NumberType::class, [
                'label' => 'tva :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Price',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('currency', ChoiceType::class, [
                'label' => 'Currency :',
                'choices' => [
                    'dollars' => 'dollars',
                    'euros' => 'euros',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => [
                    'class' => 'form-group mb-3',
                ],
                'placeholder' => 'Select a currency',
            ])
            ->add('status', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez une date',
                ],
                'label' => 'Date de commande',
                'label_attr' => [
                    'class' => 'fw-bold mb-2'
                ],
                'choices' => [
                    'pending' => 'pending',
                    'completed' => 'completed',
                    'canceled' => 'canceled',
                ],
                'placeholder' => 'Select a status'
            ])
//            ->add('user', EntityType::class, [
//                'class' => Customer::class,
//                'choice_label' => 'id',
//                'attr' => [
//                    'class' => 'form-control',
//                    'placeholder' => 'Sélectionnez user Id',
//                ],
//                'label' => 'user Id',
//                'label_attr' => [
//                    'class' => 'fw-bold mb-2'
//                ]
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
