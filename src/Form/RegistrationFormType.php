<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    
        {
            $builder
                ->add('nombre', null, [
                    'constraints' => [
                        new NotBlank(['message' => 'Por favor, ingresa tu nombre.']),
                    ],
                ])
                ->add('apellidos', null, [
                    'constraints' => [
                        new NotBlank(['message' => 'Por favor, ingresa tus apellidos.']),
                    ],
                ])
                ->add('email', null, [
                    'constraints' => [
                        new NotBlank(['message' => 'Por favor, ingresa tu correo electrónico.']),
                        new Email(['message' => 'El formato del correo electrónico no es válido.']),
                    ],
                ])
                ->add('password', null, [
                    'constraints' => [
                        new Length(['min' => 4, 'minMessage' => 'Tu contraseña debe tener al menos {{ limit }} caracteres.']),
                        new NotBlank(['message' => 'Por favor, ingresa tu contraseña.']),
                    ],
                ])
                ->add('tel', null, [
                    'constraints' => [
                        new NotBlank(['message' => 'Por favor, ingresa tu número de teléfono.']),
                    ],
                ])
                ->add('foto', FileType::class, [
                    'constraints' => [
                        new NotBlank(['message' => 'Por favor, sube tu foto.']),
                    ],
                ]);
        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
