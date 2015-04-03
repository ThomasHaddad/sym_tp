<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category','entity',array(
                'class' => 'AppBundle:Category',
                'property' => 'name',
                'required'=>false,
                'placeholder'=>'all'
            ))
            ->add('Filter','submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CategoryFilter'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_categoryfilter';
    }
}
