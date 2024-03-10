<?php

namespace App\Form;
// IncidenciasAlFormType.php

use App\Entity\Cliente;
use App\Entity\Incidencia;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class IncidenciasAlFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class)
            ->add('estado', ChoiceType::class, [
                'choices' => [
                    'Iniciada' => 'iniciada',
                    'En Proceso' => 'en_proceso',
                    'Resuelta' => 'resuelta',
                ], 'placeholder' => 'Seleccionar Estado',
                'attr' => [
                    'class' => 'w-full px-3 py-2 border rounded-md',
                ],
            ])
            ->add('cliente', EntityType::class, [
                'class' => Cliente::class,
                'choices' => $options['clientes'], // Use choices to set the available clients
                'choice_label' => function ($cliente) {
                    // Customize this based on your Cliente entity structure
                    return $cliente->__toString();
                },
                'attr' => [
                    'class' => 'w-full px-3 py-2 border rounded-md',
                ],
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'd-none'];
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Incidencia::class,
            'clientes' => null,
        ]);
    }
}
