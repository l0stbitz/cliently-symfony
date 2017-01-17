<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ClientlySubIndustriesType
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
class ClientlySubIndustriesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subIndustryIndustryid')
            ->add('subIndustryName')
            ->add('subIndustryShortDescription')
            ->add('subIndustryDescription')
            ->add('isActive')
            ->add('isDelete')
            ->add('createdDate', 'datetime')
            ->add('modifiedDate', 'datetime');
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\ClientlySubIndustries'
            )
        );
    }
}
