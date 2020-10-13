<?php

namespace App\Form;

use App\Entity\Job;
use App\Entity\Prestation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('job', EntityType::class, [
                'choices' => $options['jobs'],
                'class' => Job::class,
                'choice_label' => 'nameJob'
            ]);
        $options;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prestation::class,
            'jobs' => []
        ]);

        $resolver->setAllowedTypes('jobs', 'array');
    }
}
