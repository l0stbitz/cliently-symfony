<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ActionType
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
class ActionType extends AbstractType
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
            ->add('workflowId')
            ->add('ownerId')
            ->add('position')
            ->add('values')
            ->add('extra')
            ->add('sort')
            ->add('defaultId')
            ->add('isEnabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('executedAt');
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\Action'
            )
        );
    }
}
