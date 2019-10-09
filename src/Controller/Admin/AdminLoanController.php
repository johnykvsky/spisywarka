<?php

namespace App\Controller\Admin;

use App\Command\DeleteLoanCommand;
use App\Repository\LoanRepository;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;
use Knp\Component\Pager\PaginatorInterface; 
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\LoanService;
use Symfony\Component\Form\FormInterface;

class AdminLoanController extends AbstractController
{
    /**
     * @var MessageBusInterface
     */
    private $commandBus;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var LoanRepository
     */
    private $repository;
    /**
     * @var LoanService
     */
    private $loanService;

    /**
     * @param MessageBusInterface $commandBus
     * @param LoggerInterface $logger
     * @param LoanRepository $repository
     * @param LoanService $loanService
     */
    public function __construct(
        MessageBusInterface $commandBus,
        LoggerInterface $logger,
        LoanRepository $repository,
        LoanService $loanService
    )
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->loanService = $loanService;
    }

    /**
     * @Route("/admin/loan/{id}", name="admin_loan", defaults={"id"=null}, methods={"GET","POST"})
     * 
     * @param string|null $id
     * @param Request $rawRequest
     * @return Response|RedirectResponse
     */
    public function createOrEditILoan(?string $id, Request $rawRequest): Response
    {
        $itemId = $rawRequest->query->get('i');

        if (empty($id) && empty($itemId)) {
            $this->addFlash('danger','Please create loan from Items page');
            return $this->redirectToRoute('admin_loans');
        }

        $form = $this->getForm($id, $itemId);
        $form->handleRequest($rawRequest);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $loanDTO = $form->getData();
                $loanDTO->setId($id);
                $command = $this->loanService->getCommand($loanDTO);
                $this->commandBus->dispatch($command);
                $this->addFlash('success','Your changes were saved!');
                return $this->redirectToRoute('admin_loan', ['id' => $command->getId()]);
            }
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while saving changes');
            $error = new FormError("There is an error: ".$e->getMessage());
            $form->addError($error);
        }

        return $this->render('loan/form.html.twig', [
            'form' => $form->createView(),
            'id' => $id
        ]);
    }

    /** 
     * @Route("/admin/loans/list", name="admin_loans")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response 
     */ 
    public function loansList(PaginatorInterface $paginator, Request $request): Response
    {         
        $searchQuery = $request->query->getAlnum('search');

        return $this->render('loan/list.html.twig', [ 
            'pagination' => $paginator->paginate(
             $this->repository->listAllLoans($searchQuery), $request->query->getInt('page', 1),10),
             'searchQuery'  => $searchQuery
        ]); 
    }

    /**
     * 
     * @Route("/admin/delete/loan/{id}", name="admin_delete_loan", methods={"GET"})
     * @param string $id
     * @param Request $rawRequest
     * @return Response|RedirectResponse
     */
    public function deleteLoan(string $id, Request $rawRequest): Response
    {
        try {
            $loanId = Uuid::fromString($id);
            $command = new DeleteLoanCommand($loanId);
            $this->commandBus->dispatch($command);
            $this->addFlash('success','Loan deleted!');
        } catch (\Exception $e) {
            $this->addFlash('danger','Error while deleting loan: '.$e->getMessage());
        }

        return $this->redirectToRoute('admin_loans');
    }

    /**
     * @param string|null $id
     * @param string|null $itemId
     * @return FormInterface
     */
    private function getForm($id = null, $itemId = null): FormInterface
    {
        if (!empty($id)) {
            $loan = $this->repository->getLoan(Uuid::fromString($id));
            $loanDTO = $this->loanService->fillLoanDTO($loan);
            return $this->createForm(\App\Form\Type\LoanType::class, $loanDTO);
        }

        if (!empty($itemId)) {
            $itemId = Uuid::fromString($itemId);
            $loanDTO = $this->loanService->getLoanDTOForItem($itemId);
            return $this->createForm(\App\Form\Type\LoanType::class, $loanDTO);
        }
    }
}
