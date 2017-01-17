<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * MsgType
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
class MsgType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('ownerId')
            ->add('description')
            ->add('attachments')
            ->add('clientId')
            ->add('dealId')
            ->add('email')
            ->add('handle')
            ->add('integrationType')
            ->add('senderSourceId')
            ->add('recipientSourceId')
            ->add('cc')
            ->add('bcc')
            ->add('uid')
            ->add('references')
            ->add('code')
            ->add('threadCode')
            ->add('type')
            ->add('isOwn')
            ->add('status')
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
            'data_class' => 'AppBundle\Entity\Msg'
            )
        );
    }
}
