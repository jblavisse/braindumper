<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', options: [
                'label' =>'Prénom',
                'attr' => [
                    'class' => 'block text-sm py-3 px-4 rounded-lg w-full border outline-none',
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'block text-sm py-3 px-4 rounded-lg w-full border outline-none',
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => "J'accepte les termes et conditions.",
                    ]),
                ],
                'label' =>"J'accepte les conditions et les termes.",
                'attr'=>  [ 
                    'class'=> 'relative h-5 w-5 cursor-pointer appearance-none rounded-md border border-blue-gray-200 transition-all before:absolute before:top-2/4 before:left-2/4 before:block before:h-12 before:w-12 before:-translate-y-2/4 before:-translate-x-2/4 before:rounded-full before:bg-blue-gray-500 before:opacity-0 before:transition-opacity checked:border-pink-500 checked:bg-pink-500 checked:before:bg-pink-500 hover:before:opacity-10',
                ],
            ])
            ->add('password', PasswordType::class, [
                'mapped' => true,
                'label' =>'Mot de passe',
                'attr' => ['autocomplete' => 'new-password',
                'class' => 'block text-sm py-3 px-4 rounded-lg w-full border outline-none',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}