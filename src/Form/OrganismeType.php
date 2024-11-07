<?php

namespace App\Form;

use App\Entity\Civility;
use App\Entity\Organisme;
use App\Entity\Resource;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class OrganismeType extends AbstractType
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
            ->add('type', TextType::class, [
                'label' => 'Type :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'type',
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
                    'placeholder' => 'type',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('note', TextType::class, [
                'label' => 'Note :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'note',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3']]
            )
            ->add('logo', FileType::class, [
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
            'data_class' => Organisme::class,
        ]);
    }
}
