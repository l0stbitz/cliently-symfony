<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AccountType
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
class AccountType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type')
            ->add('planId')
            ->add('nextPlanId')
            ->add('memberCount')
            ->add('creditBalance')
            ->add('acceptedDealCount')
            ->add('workspaceCount')
            ->add('pipelineCount')
            ->add('workflowCount')
            ->add('sourceCount')
            ->add('enabledMemberCount')
            ->add('enabledWorkspaceCount')
            ->add('enabledPipelineCount')
            ->add('enabledWorkflowCount')
            ->add('enabledSourceCount')
            ->add('dailyLeadsScanned')
            ->add('planStartedAt')
            ->add('ownerId')
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
            'data_class' => 'AppBundle\Entity\Account'
            )
        );
    }
}
