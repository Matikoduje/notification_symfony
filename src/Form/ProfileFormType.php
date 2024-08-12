<?php

// src/Form/ProfileType.php
namespace App\Form;

use App\Entity\User;
use App\Form\Helpers\CountryPhonePrefixes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProfileFormType extends AbstractType
{
    private array $countryPhonePrefixes;

    public function __construct()
    {
        $this->countryPhonePrefixes = CountryPhonePrefixes::getPrefixes();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ],
            ])
            ->add('countryPrefix', ChoiceType::class, [
                'label' => 'Country Prefix',
                'choices' => $this->countryPhonePrefixes,
                'required' => false,
                'mapped' => false,
                'placeholder' => '---',
                'constraints' => [
                    new Callback([$this, 'validateCountryPrefix']),
                ],
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Phone Number',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d{9}$/',
                        'message' => 'Please enter a valid phone number.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'New Password',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'groups' => ['password_change']
                    ]),
                ],
            ])->add('smsNotificationConsent', CheckboxType::class, [
                'label' => 'I agree to receive SMS notifications.',
                'mapped' => true,
            ])->add('emailNotificationConsent', CheckboxType::class, [
                'label' => 'I agree to receive email notifications.',
                'mapped' => true,
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $user = $form->getData();
            $countryPrefix = $form->get('countryPrefix')->getData();
            $phoneNumber = $form->get('phoneNumber')->getData();
            if ($countryPrefix && $phoneNumber) {
                $fullPhoneNumber = $countryPrefix . $phoneNumber;
                $user->setFullPhoneNumber($fullPhoneNumber);
            } else if (!$phoneNumber) {
                $user->setFullPhoneNumber(null);
            }
        });
    }

    public function validateCountryPrefix($countryPrefix, ExecutionContextInterface $context): void
    {
        $form = $context->getRoot();
        $phoneNumber = $form->get('phoneNumber')->getData();

        if ($phoneNumber && !$countryPrefix) {
            $context->buildViolation('Country code is required when phone number is provided.')
                ->atPath('countryPrefix')
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
