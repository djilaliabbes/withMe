<?php

namespace App\Form;

use App\Entity\UsersSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Activite;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UsersSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('activite', EntityType::class, [
            //     'required' => false,
            //     'class' => Activite::class,
            //     'choice_label' => 'name',
            // ])
            ->add('age', IntegerType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Âge']
            ])
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'homme' => 'h',
                    'femme' => 'f'
                ],
                'attr' => ['placeholder' => 'Sexe']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UsersSearch::class,
            'method' => 'get',
            'csrf_protection' => false

        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}
