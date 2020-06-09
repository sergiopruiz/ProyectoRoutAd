<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['block_name'] == 'nuevo') {
            $builder
                ->add('username', TextType::class, array(
                    'label' => 'Usuario',
                    'required' => true
                ))
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repetir Password'],
                    'required' => true,
                    'invalid_message' => 'Las contraseñas no coinciden'
                ])
                ->add('submit', SubmitType::class, array('label' => 'Enviar'));
        }
        elseif ($options['block_name'] == 'editar') {
            $builder
                ->add('username', TextType::class, array(
                    'label' => 'Usuario',
                    'required' => true
                ))
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repetir Password'],
                    'required' => true,
                    'invalid_message' => 'Las contraseñas no coinciden'
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Actualizar',
                    'attr' => ['class' => 'btn btn-primary btn-lg']
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
