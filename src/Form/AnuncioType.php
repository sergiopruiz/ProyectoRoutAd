<?php

namespace App\Form;

use App\Entity\Anuncio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class AnuncioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['block_name'] == 'nuevo') {
            $builder
                ->add('video', FileType::class, [
                    'mapped' => true,
                    'required' => true,
                    'constraints' => [
                        new File([
                            'maxSize' => '100M',
                            'mimeTypes' => [
                                'video/mp4',
                            ],
                            'mimeTypesMessage' => 'Formato incorrecto, únicamente archivos MP4',
                        ])
                    ],
                ])
                ->add('imagen', FileType::class, [
                    'mapped' => true,
                    'required' => true,
                    'constraints' => [
                        new File([
                            'maxSize' => '10M',
                            'mimeTypes' => [
                                'image/png',
                                'image/jpg',
                                'image/jpeg'
                            ],
                            'mimeTypesMessage' => 'Formato incorrecto, únicamente archivos PNG , JPG o JPEG',
                        ])
                    ]
                ])
                ->add('url', TextType::class,[
                    'label' => 'URL publicitario',
                    'required' => false
                ])
                ->add('duracion', HiddenType::class, [
                    'mapped' => false
                ])
                ->add('latitud',HiddenType::class,[
                    'required' => true
                ])
                ->add('longitud',HiddenType::class,[
                    'required' => true
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Guardar',
                    'attr' => ['class' => 'btn btn-primary btn-lg']
                ]);
        }
        if($options['block_name'] == 'editar')
        {
            $builder
                ->add('url',TextType::class,[
                    'label' => 'URL',
                    'required' => false
                ])
                ->add('video', FileType::class, [
                    'label' => 'Cambiar video',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '100M',
                            'mimeTypes' => [
                                'video/mp4',
                            ],
                            'mimeTypesMessage' => 'Formato incorrecto, únicamente archivos MP4',
                        ])
                    ],
                ])
                ->add('duracion', HiddenType::class, [
                    'mapped' => false
                ])
                ->add('imagen', FileType::class, [
                    'label' => 'Cambiar imagen',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '10M',
                            'mimeTypes' => [
                                'image/png',
                                'image/jpg',
                                'image/jpeg'
                            ],
                            'mimeTypesMessage' => 'Formato incorrecto, únicamente archivos PNG , JPG o JPEG',
                        ])
                    ]
                ])
                ->add('url', TextType::class,[
                    'label' => 'URL publicitario',
                    'required' => false
                ])
                ->add('latitud',HiddenType::class,[
                    'required' => false
                ])
                ->add('longitud',HiddenType::class,[
                    'required' => false
                ])
                ->add('activo',CheckboxType::class,[
                    'label' => 'Cambiar estado',
                    'label_attr' => ['class' => 'switch-custom'],
                    'required' => false,
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
            'data_class' => Anuncio::class,
        ]);
    }
}
