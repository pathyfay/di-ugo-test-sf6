<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

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
//                'row_attr' => ['class' => 'p-3'],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Firstname :',
                'attr' => [
                    'class' => 'form-control',
//                    'placeholder' => 'Firstname',
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
//                    'placeholder' => 'Lastname',
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
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
