<?php

namespace App\Form;

use App\Entity\Tricks;
use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
// EntityType
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// FileType
use Symfony\Component\Form\Extension\Core\Type\FileType;
// TextType
use Symfony\Component\Form\Extension\Core\Type\TextType;
// UrlType
use Symfony\Component\Form\Extension\Core\Type\UrlType;
// TextareaType
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
// VideosType
use App\Form\VideoType;

class TricksType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom du Trick',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', null, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control']
            ])
            ->add('id_group', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'label' => 'Groupe',
                'placeholder' => 'Sélectionnez un groupe',
                'attr' => ['class' => 'form-control']
            ])

            ->add('images', CollectionType::class, [
                'entry_type' => FileType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => ' ',
                'mapped' => false,
                'required' => false,
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,

                'allow_delete' => true,
                'by_reference' => false,
                'label' => ' ',
                'mapped' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    }
}
