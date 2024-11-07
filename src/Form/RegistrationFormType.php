<?php

namespace App\Form;

use App\Entity\Civility;
use App\Entity\CivilStatus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civility', EntityType::class, [
                'placeholder' => 'Choose an option',
                'class' => Civility::class,
                'choice_label' => 'label',
                'required' => true,
                'label' => 'Title : ',
                'expanded' => true,
                'multiple' => false,
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => [
                    'class' => 'form-group mb-3',
                ],
                'attr' => [
                    'class' => 'm-1 gap-2 d-flex',
                ]
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
            ->add('postalCode', TextType::class, [
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
            ->add('streetAddress', TextType::class, [
                'label' => 'Street Address :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Street Address',
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
                    'inputmode' => 'Mob',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3'],
                'required' => false
            ])
            ->add('phone', TelType::class, [
                'label' => 'Numéro de téléphone fixe :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre numéro de téléphone fixe +33',
                    'inputmode' => 'tel',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3'],
                'required' => false
            ])
            ->add('dateOfBirth', DateType::class, [
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
                        'mimeTypes' => ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
                    ])
                ]
            ])
            ->add('civilStatus', EntityType::class, [
                'class' => CivilStatus::class,
                'choice_label' => 'label',
                'placeholder' => 'Select your civil status',
                'attr' => [
                    'class' => 'form-control',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => ['class' => 'form-group mb-3'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles :',
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                    'Manager' => 'ROLE_MANAGER',
                ],
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'm-1 gap-2 d-flex',
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',
                ],
                'row_attr' => [
                    'class' => 'form-group mb-3',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control',
                    ],
                    'label_attr' => ['class' => 'form-label fw-bold py-1 px-1'],
                    'row_attr' => ['class' => 'mb-3'],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control',
                    ],
                    'label_attr' => ['class' => 'form-label fw-bold py-1 px-1'],
                    'row_attr' => ['class' => 'mb-3'],
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a password']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
//            ->add('agreeTerms', CheckboxType::class, [
//                'mapped' => true,
//                'constraints' => [
//                    new IsTrue([
//                        'message' => 'You should agree to our terms.',
//                    ]),
//                ],
//                'attr' => [
//                    'class' => 'form-control',
//                ],
//                'label_attr' => [
//                    'class' => 'fw-bold py-1 px-1',
//                ],
//                'row_attr' => ['class' => 'form-group mb-3'],
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'civilities' => [],
        ]);
        $resolver->setAllowedTypes('civilities', 'array');
    }
}
