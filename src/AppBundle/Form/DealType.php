<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * DealType
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
class DealType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('sourceDescription')
            ->add('value')
            ->add('isEnabled')
            ->add('initialClientId')
            ->add('workflowId')
            ->add('stageId')
            ->add('taskId')
            ->add('actionId')
            ->add('actionValues')
            ->add('ownerId')
            ->add('sourceId')
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
            'data_class' => 'AppBundle\Entity\Deal'
            )
        );
    }
}
