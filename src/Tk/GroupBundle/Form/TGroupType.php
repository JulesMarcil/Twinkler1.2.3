<?php

namespace Tk\GroupBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'attr' => array(
                    'placeholder' => 'Name of the group',
                ),
            ))
            ->add('currency','entity', array(
                            'class'         => 'TkGroupBundle:Currency',
                            'property'      => 'name',
                            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tk\GroupBundle\Entity\TGroup'
        ));
    }

    public function getName()
    {
        return 'tk_groupbundle_tgrouptype';
    }
}
