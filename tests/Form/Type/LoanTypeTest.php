<?php 

namespace App\Tests\Form\Type;

use App\Form\Type\TestedType;
use App\DTO\LoanDTO;
use App\Form\Type\LoanType;
use Symfony\Component\Form\Test\TypeTestCase;
use Ramsey\Uuid\Uuid;
use App\Form\DataTransformer\UuidToItemTransformer;
use Symfony\Component\Form\PreloadedExtension;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\Forms;

class LoanTypeTest extends TypeTestCase
{
    private $objectManager;

    protected function setUp()
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
          ->addExtensions($this->getExtensions())
          ->getFormFactory();
        // mock any dependencies
        //$this->transformer = $this->createMock(UuidToItemTransformer::class);
        //parent::setUp();
    }

    protected function getExtensions()
    {
        $mockEntityType = $this->getMockBuilder('Tetranz\Select2EntityBundle\Form\Type\Select2EntityType')
            ->disableOriginalConstructor()
            ->getMock();

        // $mockLoanType = $this->getMockBuilder('App\Form\Type\LoanType')
        //     ->disableOriginalConstructor()
        //     ->getMock();
            $transformer = $this->createMock(UuidToItemTransformer::class);
        $mockLoanType = $this->getMockBuilder('App\Form\Type\LoanType')
            ->setMethods(array('__construct'))
            ->setConstructorArgs(array($transformer))
            ->disableOriginalConstructor()
            ->getMock();

        // $mockEntityType->expects($this->any())->method('getName')
        //                ->will($this->returnValue('tetranz_select2entity'));
        //$transformer = $this->createMock(UuidToItemTransformer::class);
        //$loanType = new LoanType($transformer);
        return array(new PreloadedExtension(array(
                $mockEntityType, $mockLoanType
        ), array()));

        //$registry = $this->createMock(ManagerRegistry::class);
        //$router = $this->createMock(RouterInterface::class);

        // $select2EntityType = $this->getMockBuilder('\Tetranz\Select2EntityBundle\Form\Type\Select2EntityType')
        //     ->disableOriginalConstructor()
        //     ->getMock();

        // create a type instance with the mocked dependencies
        //$loanType = new LoanType($this->transformer);
        // $entityType = new Select2EntityType($registry, $router, [
        //     'minimum_input_length' => 3,
        //     'page_limit' => 5,
        //     'scroll' => false,
        //     'allow_clear' => false,
        //     'allow_add' => ['enabled'=>false, 'new_tag_prefix'=> '_new', 'tag_separators'=> ';', 'new_tag_text'=> 'Add'],
        //     'delay' => 5,
        //     'language' => 'en',
        //     'cache' => false,
        //     'cache_timeout' => 100,
        //     'width' => null,
        //     'render_html' => false,
        //  ]);

        // return [
        //     // register the type instances with the PreloadedExtension
        //     new PreloadedExtension([$select2EntityType, $loanType], []),
        // ];
    }

    public function testSubmitValidData()
    {
        $formData = [
            'itemId' => Uuid::uuid4()->toString(),
            'loaner' => 'John',
            'loanDate' => new \DateTime,
            'returnDate' => null,
        ];

        // $loanDTO = new LoanDTO(null, $formData['itemId'], $formData['loaner'], $formData['loanDate'], $formData['returnDate']);
        
        // //$form = $this->factory->create(LoanType::class, $loanDTO);
        // $transformer = $this->createMock(UuidToItemTransformer::class);
        // $form = $this->factory->create(new LoanType($transformer), $loanDTO);
        // // $form = $this->getMockBuilder('App\Form\Type\LoanType')
        // //     ->setMethods(array('__construct'))
        // //     ->setConstructorArgs(array($transformer))
        // //     ->disableOriginalConstructor()
        // //     ->getMock();
        // $form->submit($formData);

        // $this->assertTrue($form->isSynchronized());
        // $this->assertEquals($form->getData(), $loanDTO);

        // $view = $form->createView();
        // $children = $view->children;

        // foreach (array_keys($formData) as $key) {
        //     $this->assertArrayHasKey($key, $children);
        // }
    }
}