<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->add('mediaTitle', TextType::class, [
                'label' => 'Titre du media : ',
                'required' => false
            ])
            ->add('userEmail', TextType::class, [
                'label' => 'Email du créateur : ',
                'required' => false
            ])
            ->add('mediaCreatedAt', DateType::class, [
                'label' => 'Date de sortie supérieur à : ',
                'required' => false,
                'widget' => 'single_text'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
