<?php

namespace App\Form;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Cliente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class ClienteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Por favor, ingresa tu nombre.']),
                ],
            ])
            ->add('apellidos', TextType::class, [
                'required' => false,
            ])
            ->add('tel', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Por favor, ingresa tu número de teléfono.']),
                ],
            ])
            ->add('direccion', TextType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cliente::class,
        ]);
    }
}
