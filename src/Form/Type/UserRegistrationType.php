<?php
namespace App\Form\Type;

use App\DTO\UserRegistrationDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\DataMapperInterface; 
use Symfony\Component\Form\FormInterface;

class UserRegistrationType extends AbstractType  implements DataMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['required' => true])
            ->add('lastName', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('submit', SubmitType::class)
            ->setDataMapper($this);
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserRegistrationDTO::class,
            'empty_data' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param UserRegistrationDTO $userRegistrationDTO Structured data
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     */
    public function mapDataToForms($userRegistrationDTO, $forms): void
    {
        if (!empty($userRegistrationDTO)) {
            $forms = iterator_to_array($forms);
            $forms['firstName']->setData($userRegistrationDTO->getFirstName());
            $forms['lastName']->setData($userRegistrationDTO->getLastName());
            $forms['email']->setData($userRegistrationDTO->getEmail());
            $forms['plainPassword']->setData($userRegistrationDTO->getPlainPassword());
        }
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     * @param UserRegistrationDTO $userRegistrationDTO Structured data
     */
    public function mapFormsToData($forms, &$userRegistrationDTO): void
    {
        $forms = iterator_to_array($forms);
        $userRegistrationDTO = new UserRegistrationDTO(
            $forms['firstName']->getData(), 
            $forms['lastName']->getData(), 
            $forms['email']->getData(),
            $forms['plainPassword']->getData()
        );
    }
}