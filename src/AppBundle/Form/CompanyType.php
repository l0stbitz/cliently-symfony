<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * CompanyType
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
class CompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('addressLine1')
            ->add('addressLine2')
            ->add('city')
            ->add('state')
            ->add('zip')
            ->add('country')
            ->add('coords')
            ->add('googleLocation')
            ->add('foundationYear')
            ->add('phone')
            ->add('website')
            ->add('description')
            ->add('logo')
            ->add('ownerId')
            ->add('workspaceId')
            ->add('sourceId')
            ->add('isEnabled')
            ->add('createdAt')
            ->add('updatedAt');
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\Company'
            )
        );
    }
}
