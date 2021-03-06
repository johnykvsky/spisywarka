<?php
namespace App\Form\Type;

use App\DTO\ProfileDTO;
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
            ->add('firstName', TextType::class, ['required' => true])
            ->add('lastName', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
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
            'data_class' => ProfileDTO::class,
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
     * @param ProfileDTO $profileDTO Structured data
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     */
    public function mapDataToForms($profileDTO, $forms): void
    {
        if (!empty($profileDTO)) {
            $forms = iterator_to_array($forms);
            $forms['firstName']->setData($profileDTO->getFirstName());
            $forms['lastName']->setData($profileDTO->getLastName());
            $forms['email']->setData($profileDTO->getEmail());
            $forms['plainPassword']->setData($profileDTO->getPlainPassword());
        }
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     * @param ProfileDTO $profileDTO Structured data
     */
    public function mapFormsToData($forms, &$profileDTO): void
    {
        $forms = iterator_to_array($forms);
        $profileDTO = new ProfileDTO(
            $forms['firstName']->getData(), 
            $forms['lastName']->getData(), 
            $forms['email']->getData(),
            $forms['plainPassword']->getData()
        );
    }
}