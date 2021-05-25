<?php

namespace App\Form;

use App\Entity\Employe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EmployeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                "label" => 'Nom',
                "required" => false,
                "attr" => [
                    "placeholder" => 'Veuillez saisir un nom']
            ])
            ->add('prenom', TextType::class, [
                "label" => 'Prénom',
                "required" => false,
                "attr" => [
                    "placeholder" => 'Veuillez saisir un prénom']
            ])
            ->add('pseudo', TextType::class, [
                "label" => 'Pseudo',
                "required" => false,
                "attr" => [
                    "placeholder" => 'Veuillez saisir un pseudo']
            ]);

        if ($options['ajouter'] === true) {
            $builder
                ->add('photo', FileType::class, [
                    "required" => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                "image/png",
                                "image/jpg",
                                "image/jpeg"
                            ],
                            'mimeTypesMessage' => "Extensions Autorisées : PNG JPG JPEG",
                            'maxSize' => 2000000,
                            'maxSizeMessage' => "Taille maximal autorisée : 2mo"
                        ])
                    ]
                ]);
        } else if ($options['modifier'] === true) {
            $builder
                ->add('photoFile', FileType::class, [
                    "required" => false,
                    'mapped' => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                "image/png",
                                "image/jpg",
                                "image/jpeg"
                            ],
                            'mimeTypesMessage' => "Extensions Autorisées : PNG JPG JPEG",
                            'maxSize' => 2000000,
                            'maxSizeMessage' => "Taille maximal autorisée : 2mo"
                        ])
                    ]
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Employe::class,
            'ajouter' => false,
            'modifier' => false
        ]);
    }
}
