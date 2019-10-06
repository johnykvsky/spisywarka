<?php
namespace App\Form\Type;

use App\DTO\LoanDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormInterface;
use App\Form\DataTransformer\UuidToItemTransformer;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

final class LoanType extends AbstractType  implements DataMapperInterface
{
    private $transformer;

    public function __construct(UuidToItemTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('item', Select2EntityType::class, [
                'multiple' => false,
                'remote_route' => 'admin_items_autocomplete',
                'remote_params' => [], // static route parameters for request->query
                'class' => '\App\Entity\Item',
                'primary_key' => 'id',
                'text_property' => 'name',
                'minimum_input_length' => 3,
                'page_limit' => 10,
                'allow_clear' => true,
                'delay' => 250,
                'cache' => true,
                'cache_timeout' => 60000, // if 'cache' is true
                'language' => 'en',
                'placeholder' => 'Select an item'
            ])
            ->add('loaner', TextType::class, ['required' => false])
            ->add('loanDate', DateType::class, ['required' => false, 'widget' => 'single_text', 'html5' => true, 'attr' => ['placeholder' => 'yyyy-mm-dd'], 'placeholder' => 'Y-m-d','input'  => 'datetime', 'format' => 'yyyy-MM-dd', 'input_format' => 'Y-m-d'])
            ->add('returnDate', DateType::class, ['required' => false, 'widget' => 'single_text', 'html5' => true, 'attr' => ['placeholder' => 'yyyy-mm-dd'], 'input'  => 'datetime', 'input_format' => 'Y-m-d'])
            ->add('submit', SubmitType::class)
            ->setDataMapper($this);

            $builder->get('item')->addModelTransformer($this->transformer);
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LoanDTO::class,
            'empty_data' => null,
        ]);
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param LoanDTO $loanDTO Structured data
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     */
    public function mapDataToForms($loanDTO, $forms): void
    {
        if (empty($loanDTO)) {
            $loanDTO = new LoanDTO(null, null, null, null, null);
        }

        $forms = iterator_to_array($forms);
        $forms['item']->setData($loanDTO->getItemId());
        $forms['loaner']->setData($loanDTO->getLoaner());
        $forms['loanDate']->setData($loanDTO->getLoanDate());
        $forms['returnDate']->setData($loanDTO->getReturnDate());

    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface} instances
     * @param LoanDTO $loanDTO Structured data
     */
    public function mapFormsToData($forms, &$loanDTO): void
    {
        $forms = iterator_to_array($forms);
        $loanDTO = new LoanDTO(
            null, 
            $forms['item']->getData(), 
            $forms['loaner']->getData(), 
            $forms['loanDate']->getData(), 
            $forms['returnDate']->getData()
        );
    }
}