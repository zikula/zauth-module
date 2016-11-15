<?php

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula Foundation - http://zikula.org/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\ZAuthModule\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Zikula\ZAuthModule\Validator\Constraints\ValidPassword;

class LostPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['includeReset']) {
            $builder
                ->add('uname', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                    'required' => false,
                    'label' => $options['translator']->__('User name'),
                    'input_group' => ['left' => '<i class="fa fa-user"></i>'],
                ])
                ->add('email', 'Symfony\Component\Form\Extension\Core\Type\EmailType', [
                    'required' => false,
                    'label' => $options['translator']->__('Email Address'),
                    'input_group' => ['left' => '<i class="fa fa-at"></i>'],
                ])
            ;
        } else {
            $builder
                ->add('pass', 'Symfony\Component\Form\Extension\Core\Type\RepeatedType', [
                    'type' => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
                    'first_options' => [
                        'label' => $options['translator']->__('Create new password'),
                        'input_group' => ['left' => '<i class="fa fa-asterisk"></i>']
                    ],
                    'second_options' => [
                        'label' => $options['translator']->__('Repeat new password'),
                        'input_group' => ['left' => '<i class="fa fa-asterisk"></i>']
                    ],
                    'invalid_message' => $options['translator']->__('The passwords must match!'),
                    'constraints' => [
                        new NotNull(),
                        new ValidPassword()
                    ]
                ])
            ;
        }
        $builder
            ->add('submit', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
                'label' => $options['translator']->__('Submit'),
                'icon' => 'fa-check',
                'attr' => ['class' => 'btn btn-success']
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'zikulazauthmodule_account_lostpassword';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translator' => null,
            'includeReset' => false,
            'constraints' => new Callback(['callback' => function ($data, ExecutionContextInterface $context) use ($resolver) {
                if (!isset($data['pass']) && empty($data['uname']) && empty($data['email'])) {
                    $context->buildViolation(__('Error! You must enter either your username or email address.'))
                        ->addViolation();
                }
            }]),
        ]);
    }
}
