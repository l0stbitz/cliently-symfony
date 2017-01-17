<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * DealActionType
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
class DealActionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('actionTypeId')
            ->add('name')
            ->add('description')
            ->add('actionId')
            ->add('dealWorkflowId')
            ->add('ownerId')
            ->add('position')
            ->add('values')
            ->add('extra')
            ->add('isEnabled')
            ->add('isFailed')
            ->add('runTime')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('executedAt')
            ->add('dueAt')
            ->add('startedAt');
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\DealAction'
            )
        );
    }
}
