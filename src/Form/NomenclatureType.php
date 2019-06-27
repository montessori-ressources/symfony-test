<?php

namespace App\Form;

use App\Entity\Nomenclature;
use App\Entity\Language;
use App\Form\CardType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NomenclatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('createdAt')
            ->add('name')
            //->add('card', CardType::class, [])
            
            // begin add language list
            ->add('language', EntityType::class, [
                // looks for choices from this entity
                'class' => Language::class,            
                'choice_label' => 'name',
            
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            // End add language list

            ->add('cards', CollectionType::class, [
                'entry_type' => CardType::class,
                'allow_add' => true,
                'by_reference' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Create Nomenclature'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Nomenclature::class,
        ]);
    }
}
