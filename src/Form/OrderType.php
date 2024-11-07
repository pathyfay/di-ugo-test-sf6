<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('order_id', DateType::class, [
                    'widget' => 'single_text',
                    'html5' => true,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Sélectionnez Order Id',
                    ],
                    'label' => 'Order Id',
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
            ->add('product_id', TextType::class, [
                'label' => 'Product Id :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Product Id',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3']])
            ->add('price', TextType::class, [
                'label' => 'Price :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Price',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3']])
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
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez une date',
                ],
                'label' => 'Date de commande',
                'label_attr' => [
                    'class' => 'fw-bold mb-2'
                ]
            ])
//            ->add('customer', EntityType::class, [
//                'class' => Customer::class,
//                'choice_label' => 'customerId',
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
