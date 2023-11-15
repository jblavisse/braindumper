<?php

namespace App\Form;

use App\Entity\Memory;
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Doctrine\ORM\EntityManagerInterface;
// use App\Entity\Category;
// use App\Entity\Type;


class AddMemoryType extends AbstractType
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $option, ): void
    {
        $builder->add('title', options: [
            'label' => 'Nom du Memory',
            'attr' => [
                'class' => 'border p-2 rounded-md focus:outline-none focus:ring focus:border-blue-300',
            ],
        ])
            ->add('description', options: [
                'attr' => [
                    'class' => 'w-full border p-2 rounded-md focus:outline-none focus:ring focus:border-blue-300',
                ],
            ]);

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