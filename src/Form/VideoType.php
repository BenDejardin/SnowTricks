<?php

namespace App\Form;

use App\Entity\Videos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder
        //     ->add('url', TextareaType::class, [
        //         'attr' => [
        //             'class' => 'video-url', // Ajoutez une classe CSS si nécessaire
        //         ],
        //     ])
    
        //     ->add('remove', ButtonType::class, [
        //         'label' => 'Remove',
        //         'attr' => ['class' => 'btn btn-danger'],
        //     ])
        // ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Videos::class,
        ]);
    }
}
