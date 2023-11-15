<?php

namespace App\Form;

use App\Entity\Memory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// use App\Entity\Category;
// use App\Entity\Type;


class AddMemoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', options: [
            'label' => 'Nom du Memory'
        ])
            ->add('description');

            // ->add('category', EntityType::class, [
            //     'class' => Category::class,
            //     'choice_label' => 'name',
            //     'label' => 'Catégorie',
            //     'group_by' => 'parent.name' //classe les catégories par parent et par ordre alphabétique.
            // ])
            // ->add('type', EntityType::class, [
            //     'class' => Type::class,
            //     'choice_label' => 'name',
            //     'label' => 'Types de Memory'
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Memory::class,
        ]);
    }
}