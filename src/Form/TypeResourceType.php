<?php

namespace App\Form;

use App\Entity\TypeResource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeResourceType extends AbstractType
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
            ->add('is_active', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,  // Affichage pour l'utilisateur et la valeur réelle en booléen
                    'Non' => false,
                ],
                'label' => 'Actif :',  // Ajustement du label pour être plus descriptif
                'expanded' => true,   // Radio boutons
                'multiple' => false,  // Un seul choix possible
                'attr' => [
                    'class' => 'd-flex justify-content-between gap-3',  // Classe pour le conteneur des choix
                ],
                'label_attr' => [
                    'class' => 'fw-bold py-3 px-1',  // Classe pour le label du champ
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TypeResource::class
        ]);
    }
}
