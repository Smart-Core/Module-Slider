<?php

namespace SmartCore\Module\Slider\Form\Type;

use SmartCore\Bundle\CMSBundle\Module\AbstractNodePropertiesFormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class NodePropertiesFormType extends AbstractNodePropertiesFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('slider_id', ChoiceType::class, [
                'choices' => $this->getChoicesByEntity('SliderModuleBundle:Slider'),
                'label'   => 'Slider',
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'smart_module_slider_node_properties';
    }
}
