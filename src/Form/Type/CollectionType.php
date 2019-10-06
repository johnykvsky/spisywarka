<?php
namespace App\Form\Type;

use App\DTO\CollectionDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormInterface;

final class CollectionType extends AbstractType  implements DataMapperInterface
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, ['required' => false])
            ->add('submit', SubmitType::class)
            ->setDataMapper($this);
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollectionDTO::class,
            'empty_data' => null,
        ]);
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param CollectionDTO $collectionDTO Structured data
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     */
    public function mapDataToForms($collectionDTO, $forms): void
    {
        if (empty($collectionDTO)) {
            $collectionDTO = new CollectionDTO(null, '', null);
        }

        $forms = iterator_to_array($forms);
        $forms['name']->setData($collectionDTO->getName());
        $forms['description']->setData($collectionDTO->getDescription());
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     * @param CollectionDTO $collectionDTO Structured data
     */
    public function mapFormsToData($forms, &$collectionDTO): void
    {
        $forms = iterator_to_array($forms);
        $collectionDTO = new CollectionDTO(
            null, 
            $forms['name']->getData(), 
            $forms['description']->getData()
        );
    }
}