<?php
namespace App\Form\Type;

use App\DTO\ItemDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormInterface;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use App\Form\DataTransformer\UuidToCategoryTransformer;
use App\Form\DataTransformer\UuidToCollectionTransformer;

final class ItemType extends AbstractType  implements DataMapperInterface
{
    private $uuidToCategoryTransformer;
    private $uuidToCollectionTransformer;

    public function __construct(
        UuidToCategoryTransformer $uuidToCategoryTransformer,
        UuidToCollectionTransformer $uuidToCollectionTransformer
    )
    {
        $this->uuidToCategoryTransformer = $uuidToCategoryTransformer;
        $this->uuidToCollectionTransformer = $uuidToCollectionTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('year', IntegerType::class, ['required' => false])
            ->add('format', TextType::class, ['required' => false])
            ->add('author', TextType::class, ['required' => false])
            ->add('publisher', TextType::class, ['required' => false])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('store', TextType::class, ['required' => false])
            ->add('url', UrlType::class, ['required' => false])
            ->add('categories', Select2EntityType::class, [
                'multiple' => true,
                'remote_route' => 'admin_categories_autocomplete',
                'remote_params' => [], // static route parameters for request->query
                'class' => '\App\Entity\Category',
                'primary_key' => 'id',
                'text_property' => 'name',
                'minimum_input_length' => 3,
                'page_limit' => 10,
                'allow_clear' => true,
                'delay' => 250,
                'cache' => true,
                'cache_timeout' => 60000, // if 'cache' is true
                'language' => 'en',
                'placeholder' => 'Select a category',
            ])
            ->add('collections', Select2EntityType::class, [
                'multiple' => true,
                'remote_route' => 'admin_collections_autocomplete',
                'remote_params' => [], // static route parameters for request->query
                'class' => '\App\Entity\Collection',
                'primary_key' => 'id',
                'text_property' => 'name',
                'minimum_input_length' => 3,
                'page_limit' => 10,
                'allow_clear' => true,
                'delay' => 250,
                'cache' => true,
                'cache_timeout' => 60000, // if 'cache' is true
                'language' => 'en',
                'placeholder' => 'Select a collection',
            ])
            ->add('submit', SubmitType::class)
            ->setDataMapper($this);

            $builder->get('categories')->addModelTransformer($this->uuidToCategoryTransformer);
            $builder->get('collections')->addModelTransformer($this->uuidToCollectionTransformer);
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemDTO::class,
            'empty_data' => null,
        ]);
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param ItemDTO $itemDTO Structured data
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     */
    public function mapDataToForms($itemDTO, $forms): void
    {
        if (null == $itemDTO) {
            $itemDTO = new ItemDTO(null, '', null, null, null, null, null, null, null, [], []);
        }

        $forms = iterator_to_array($forms);
        $forms['name']->setData($itemDTO->getName());
        $forms['description']->setData($itemDTO->getDescription());
        $forms['year']->setData($itemDTO->getYear());
        $forms['format']->setData($itemDTO->getFormat());
        $forms['author']->setData($itemDTO->getAuthor());
        $forms['publisher']->setData($itemDTO->getPublisher());
        $forms['store']->setData($itemDTO->getStore());
        $forms['url']->setData($itemDTO->getUrl());
        $forms['categories']->setData($itemDTO->getCategories());
        $forms['collections']->setData($itemDTO->getCollections());
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     * @param ItemDTO $itemDTO Structured data
     */
    public function mapFormsToData($forms, &$itemDTO): void
    {
        $forms = iterator_to_array($forms);
        $itemDTO = new ItemDTO(
            null, 
            $forms['name']->getData(), 
            $forms['year']->getData(), 
            $forms['format']->getData(), 
            $forms['author']->getData(), 
            $forms['publisher']->getData(), 
            $forms['description']->getData(),
            $forms['store']->getData(),
            $forms['url']->getData(),
            $forms['categories']->getData(),
            $forms['collections']->getData()
        );
    }
}