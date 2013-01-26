<?php

namespace Kodify\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

class ClipType extends AbstractType
{
    /**
     * Form builder
     * @param FormBuilder $builder the form builder
     * @param array       $options options for this form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden')
            ->add('startTime', 'text', array(
                'label' => 'Start time', 'required' => true, 'attr' => array('class' => "input-micro"))
        )
            ->add('endTime', 'text', array(
                'label' => 'End time', 'required' => true, 'attr' => array('class' => "input-micro"))
        )
            ->add('title', 'text', array('required' => true))
            ->add('pornstars', 'text', array('required' => false))
            ->add('tags', 'text', array('required' => true));
    }


    /**
     * Get the name of the form
     * @return string
     */
    public function getName()
    {
        return 'Clip';
    }
}
