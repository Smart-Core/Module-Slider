<?php

namespace SmartCore\Module\Slider\Form\Type;

use SmartCore\Module\Slider\Entity\Slider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SliderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['attr' => ['autofocus' => 'autofocus']])
            ->add('width')
            ->add('height')
            ->add('pause_time')
            ->add('slide_properties')
            ->add('mode', ChoiceType::class, [
                'choices' => [
                    'INSET' => 'INSET',
                    'OUTBOUND' => 'OUTBOUND',
                ],
            ])
            ->add('library', ChoiceType::class, [
                'choices' => [
                    'jcarousel' => 'jcarousel',
                    'nivoslider' => 'nivoslider',
                    'bootstrap3' => 'bootstrap3',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Slider::class,
        ]);
    }

    public function getName()
    {
        return 'smart_module_slider';
    }
}
