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
                'class' => 'border w-1/2 p-2 rounded-md focus:outline-none focus:ring focus:border-pink-300',
            ],
        ])
            ->add('description', options: [
                'attr' => [
                    'class' => 'w-full border p-2 rounded-md focus:outline-none focus:ring focus:border-pink-300',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Memory::class,
        ]);
    }
}