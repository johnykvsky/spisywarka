<?php
namespace App\Form\Type;

use App\DTO\UserDTO;
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

class UserProfileType extends AbstractType  implements DataMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
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
            'data_class' => UserDTO::class,
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
     * @param UserDTO $userDTO Structured data
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     */
    public function mapDataToForms($userDTO, $forms): void
    {
        $forms = iterator_to_array($forms);
        $forms['firstName']->setData($userDTO->getFirstName());
        $forms['lastName']->setData($userDTO->getLastName());
        $forms['email']->setData($userDTO->getEmail());
        $forms['plainPassword']->setData($userDTO->getPlainPassword());
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     * @param UserDTO $userDTO Structured data
     */
    public function mapFormsToData($forms, &$userDTO): void
    {
        $forms = iterator_to_array($forms);
        $userDTO = new UserDTO(
            $forms['firstName']->getData(), 
            $forms['lastName']->getData(), 
            $forms['email']->getData(),
            $forms['plainPassword']->getData()
        );
    }
}