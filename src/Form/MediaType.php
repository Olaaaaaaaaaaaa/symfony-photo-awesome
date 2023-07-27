<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'mapped' => false
            ])
            ->add('title', TextType::class, [
                'label' => 'Nom du label :'
            ])
            ->add('description', TextareaType::class)
            /**
             * Comment ajouter un user au formulaire
             * Pas besoin car on prend l'utilisateur connecté !
             */
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'query_builder' => function (EntityRepository $er) {
            //         return $er->createQueryBuilder('u')
            //             ->orderBy('u.email', 'ASC');
            //     },
            //     'choice_label' => 'email',
            //     'expanded' => true
            // ]);
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'label',
                //VOUS AVEZ BESOIN de cette option si c'est un tableau d'entité
                'multiple' => true,
                'expanded' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
