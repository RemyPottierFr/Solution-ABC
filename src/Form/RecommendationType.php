<?php

namespace App\Form;

use App\Entity\Recommendation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecommendationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('owner', null, ['choice_label' => 'name'])
            ->add('target', null, ['choice_label' => 'name'])
            ->add('comment')
            ->add('clientName')
            ->add('infoClient')
            ->add('status');
        $options;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recommendation::class,
        ]);
    }
}
