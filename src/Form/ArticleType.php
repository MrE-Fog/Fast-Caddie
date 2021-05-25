<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,[
            "label"=>"Nom",
            "required"=>false,
            "attr"=>[
                "placeholder"=>'Saisir le nom de l\'article'
            ]])
            ->add('prix', NumberType::class, [
                "required"=> false,
                "attr"=> [
                    "placeholder"=>"Saisir le prix de l'article"
                ]
            ])
            ->add('description', TextareaType::class,[
                    'required'=>false,
                    'attr'=>[
                        'placeholder'=> "Saisir a description de l'article",
                    ]
            ])
            ->add('type',ChoiceType::class,[
                "label"=>"Sélectionnez l'unité voulu",
                "required"=>false,
                "choices" => [
                    'kg' => 'kg',
                    '100g' => '100g',
                    '500g' => '500g'
                ]
            ])
            ->add('categorie', EntityType::class, [
                "class" => Categorie::class,
                "choice_label" => "nom",
                "attr" => [
                    "placeholder" => "Veuillez choisir une catégorie",
                    "class" => "types"
                    // Cette classe permet de faire un affichage dynamique
                ]])
            ->add('stock', IntegerType::class, [
                "label"=>"Nombre en Stock",
                "required"=>false,
                'attr'=> [
                    'placeholder'=> "0",
                ]
            ]);

        if($options['ajouter'] === true) {
            $builder
                ->add('image', FileType::class, [
                    "required" => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                "image/png",
                                "image/jpg",
                                "image/jpeg",
                                "image/svg"
                            ],
                            'mimeTypesMessage' => "Extensions Autorisées : PNG JPG JPEG SVG",
                            'maxSize' => 2000000,
                            'maxSizeMessage' => "Taille maximal autorisée : 2mo"
                        ])
                    ]
                ]);
        }
        else if($options['modifier'] === true) {
            $builder
                ->add('imageFile', FileType::class, [
                    "required" => false,
                    'mapped' => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                "image/png",
                                "image/jpg",
                                "image/jpeg",
                                "image/svg"
                            ],
                            'mimeTypesMessage' => "Extensions Autorisées : PNG JPG JPEG SVG",
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
            'data_class' => Articles::class,
            'ajouter' => false,
            'modifier' => false
        ]);
    }
}
