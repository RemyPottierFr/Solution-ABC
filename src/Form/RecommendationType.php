<?php

namespace App\Form;

use App\Entity\Recommendation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecommendationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment')
            ->add('owner')
            ->add('clientName')
            ->add('infoClient')
            ->add('displayClient', CheckboxType::class, ['label' => 'Afficher le client recommandé']);
        $options;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recommendation::class,
        ]);
    }
}
