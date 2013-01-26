<?php

namespace Kodify\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TagType extends AbstractType
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
            ->add('name', 'text')
            ->add(
                'enabled',
                'choice',
                array(
                    'choices' => array(
                        '1' => 'Enabled',
                        '0' => 'Disabled',
                    ),
                    'multiple'  => false
                )
            );
    }

    /**
     * Get the name of the form
     * @return string
     */
    public function getName()
    {
        return 'tag';
    }
}
