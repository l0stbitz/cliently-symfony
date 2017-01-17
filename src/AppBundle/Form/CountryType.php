<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * CountryType
 * Insert description here
 *
 * @category
 * @package
 * @author
 * @copyright
 * @license
 * @version
 * @link
 * @see
 * @since
 */
class CountryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('iso')
            ->add('iso3')
            ->add('isoNumeric')
            ->add('fips')
            ->add('name')
            ->add('capital')
            ->add('area')
            ->add('population')
            ->add('continent')
            ->add('tld')
            ->add('currencyCode')
            ->add('currencyName')
            ->add('phone')
            ->add('postalCodeFormat')
            ->add('postalCodeRegex')
            ->add('languages')
            ->add('neighbours')
            ->add('equivalentFipsCode');
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\Country'
            )
        );
    }
}
