<?php

namespace Tk\ExpenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tk\UserBundle\Entity\MemberRepository;

class ExpenseType extends AbstractType
{
    protected $group;

    public function __construct($group)
    {
        $this->group = $group;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $group = $this->group;
        $builder->add('owner', 'entity', array(
                        'class'         => 'TkUserBundle:Member', 
                        'property'      => 'name',
                        'query_builder' => function(MemberRepository $member) use ($group) {
                            return $member->createQueryBuilder('m')
                                      ->where('m.tgroup = :group')
                                      ->andWhere('m.active = :active')
                                      ->setParameters(array(
                                                'group'  => $group,
                                                'active' => 1
                                                ));
                            }
                        ))
                ->add('name', 'text')
                ->add('amount', 'money', array('currency' => $group->getCurrency()->getIso(),
                                               'required' => true))
                ->add('date', 'date', array(
                        'widget' => 'single_text',
                        'format' => 'dd-MM-yyyy',
                        'attr' => array('class' => 'date')
                        ))
                ->add('users', 'entity', array(
                        'class'         => 'TkUserBundle:Member',
                        'property'      => 'name',
                        'multiple'      => 'true',
                        'expanded'      => 'true',
                        'required'      => 'true',
                        'query_builder' => function(MemberRepository $member) use ($group) {
                            return $member->createQueryBuilder('m')
                                      ->where('m.tgroup = :group')
                                      ->setParameter('group', $group);
                            }
                        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tk\ExpenseBundle\Entity\Expense'
        ));
    }

    public function getName()
    {
        return 'tk_expensebundle_expensetype';
    }
}
