<?php

namespace App\Form;

use App\Entity\Civility;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CivilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                    'label' => 'code :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'ex.:nom',
                    ],
                    'label_attr' => [
                        'class' => 'fw-bold py-3 px-1',
                    ],
                    'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('label', TextType::class, [
                    'label' => 'label :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'ex.:label',
                    ],
                    'label_attr' => [
                        'class' => 'fw-bold py-3 px-1',
                    ],
                    'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('description', TextType::class, [
                    'label' => 'Description :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'ex.: Homme, Garçon, ...',
                    ],
                    'label_attr' => [
                        'class' => 'fw-bold py-3 px-1',
                    ],
                    'row_attr' => ['class' => 'form-group mb-3']]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Civility::class,
            'customers' => []
        ]);
    }
}
