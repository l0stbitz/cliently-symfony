<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * LeadType
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
class LeadType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sourceId')
            ->add('clientSourceId')
            ->add('companySourceId')
            ->add('workflowId')
            ->add('actionId')
            ->add('actionValues')
            ->add('workspaceId')
            ->add('ownerId')
            ->add('isEnabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('accessedAt');
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\Lead'
            )
        );
    }
}
