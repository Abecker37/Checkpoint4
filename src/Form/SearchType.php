<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('searchTerm', ChoiceType::class, [
                'label' => 'Nom du jeu',
                'placeholder' => 'Choisissez un jeu',
                'choices' => array_flip($options['game_choices']),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('game_choices');
    }
}
