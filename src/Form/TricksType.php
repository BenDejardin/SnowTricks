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
        $groupRepository = $this->entityManager->getRepository(Group::class);
        $groups = $groupRepository->findAll();

        $choices = [];
        foreach ($groups as $group) {
            $choices[$group->getName()] = $group->getId(); // ID en valeur, nom en libellé
        }

        $builder
            ->add('name', null, [
                'label' => 'Nom du Tricks',
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
                'attr' => ['class' => 'form-control']
            ])
            ->add('images', FileType::class, [
                'label' => 'Image(s)',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control']
            ]) 
            ->add('videos', TextareaType::class, [
                'label' => 'Video(s)',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control']
            ]) 
            ;
            

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    }
}
