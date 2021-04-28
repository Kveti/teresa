<?php

namespace App\Form;

use App\Entity\Logs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LogsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('months', ChoiceType::class, [
                'label' => 'Logy:',
                'expanded' => true,
                'multiple' => true,
                'choices' => $months
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Merdžnuť'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Logs::class,
        ]);
    }
}
