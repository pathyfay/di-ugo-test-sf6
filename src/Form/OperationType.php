<?php

namespace App\Form;

use App\Entity\Operation;
use App\Entity\Resource;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                    'label' => 'Nom :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'ex.:nom',
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
                        'placeholder' => 'ex.:description',
                    ],
                    'label_attr' => [
                        'class' => 'fw-bold py-3 px-1',
                    ],
                    'row_attr' => ['class' => 'form-group mb-3']]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Operation::class,
        ]);
    }
}
