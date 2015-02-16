<?php

namespace Gtb\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReservationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date')
            ->add('restaurant', 'entity', array(
                    'class' => 'Gtb\Bundle\CoreBundle\Entity\Restaurant',
                    'property' => 'id'
                ))
            ->add('person', 'entity', array(
                    'class' => 'Gtb\Bundle\CoreBundle\Entity\Person',
                    'property' => 'id'
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gtb\Bundle\CoreBundle\Entity\Reservation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gtb_bundle_corebundle_reservation';
    }
}
