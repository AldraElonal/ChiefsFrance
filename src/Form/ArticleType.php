<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
//            ->add('created_at')
            ->add('content', CKEditorType::class, [
                'config' => [
                    'uicolor' => '#ffffff'
                ]
            ])
            ->add('imageFile', FileType::class, [
                'required' => false
            ])
            ->add('status')
//            ->add('user')
            ->add('categoryId', EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'name']
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
