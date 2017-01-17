<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ClientType
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
class ClientType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('avatar')
            ->add('occupation')
            ->add('description')
            ->add('email')
            ->add('addressLine1')
            ->add('addressLine2')
            ->add('city')
            ->add('state')
            ->add('zip')
            ->add('country')
            ->add('coords')
            ->add('googleLocation')
            ->add('phone')
            ->add('social')
            ->add('contacts')
            ->add('value')
            ->add('newEventsCount')
            ->add('companyId')
            ->add('ownerId')
            ->add('workspaceId')
            ->add('sourceId')
            ->add('isVerified')
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
            'data_class' => 'AppBundle\Entity\Client'
            )
        );
    }
}
