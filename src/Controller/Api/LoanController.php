<?php

namespace App\Controller\Api;

use App\Command\CreateLoanCommand;
use App\Command\UpdateLoanCommand;
use App\Command\DeleteLoanCommand;
use App\Error\ApiError;
use App\Repository\Exception\LoanNotFoundException;
use App\Repository\LoanRepository;
use App\Request\CreateLoanRequest;
use App\Request\UpdateLoanRequest;
use App\Traits\JsonErrorResponse;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Swagger\Annotations as SWG;
use App\CommandHandler\Exception\LoanNotDeletedException;
use Nelmio\ApiDocBundle\Annotation\Model;

class LoanController extends AbstractController
{
    use JsonErrorResponse;

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
     * @param MessageBusInterface $commandBus
     * @param LoggerInterface $logger
     * @param LoanRepository $repository
     */
    public function __construct(
        MessageBusInterface $commandBus,
        LoggerInterface $logger,
        LoanRepository $repository
    )
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->repository = $repository;
    }

    /**
     * @Route("/api/loan", name="create_loan", methods={"POST"})
     *
     * @ParamConverter("request", converter="fos_rest.request_body")
     * 
     * @SWG\Tag(name="Loan")
     * @SWG\Post(
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              required={"name"},
     *              @SWG\Property(property="itemId", type="string", format="UUID"),
     *              @SWG\Property(property="loaner", type="string", maxLength=255),
     *              @SWG\Property(property="loanDate", type="string", format="Y-m-d H:i"),
     *              @SWG\Property(property="returnDate", type="string", format="Y-m-d H:i"),
     *          )
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Returns ID of created Loan",
     *     @SWG\Schema(
     *          @SWG\Property(property="id", type="string", format="UUID")
     *     )
     * )
     * @SWG\Response(
     *     response=422,
     *     description="Loan was not created"
     * )
     * 
     * @param CreateLoanRequest $request
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function create(CreateLoanRequest $request, ConstraintViolationListInterface $validationErrors): JsonResponse
    {
        if ($validationErrors->count()) {
            $response = $this->jsonError(ApiError::ENTITY_VALIDATION_ERROR,
                'Validations errors for create Loan',
                Response::HTTP_BAD_REQUEST,
                $this->parseFormErrors($validationErrors)
            );
        }

        try {
            $id = Uuid::uuid4();
            $command = new CreateLoanCommand(
                $id,
                $request->getItemId(),
                $request->getLoaner(),
                $request->getLoanDate(),
                $request->getReturnDate()
            );
            $this->commandBus->dispatch($command);
            return $this->json(['id' => $id], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_CREATE_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    /**
     * @Route("/api/loan", name="update_loan", methods={"PATCH"})
     *
     * @ParamConverter("request", converter="fos_rest.request_body")
     *
     * @SWG\Tag(name="Loan")
     * @SWG\Patch(
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              required={"name"},
     *              @SWG\Property(property="id", type="string", format="UUID"),
     *              @SWG\Property(property="itemId", type="string", format="UUID"),
     *              @SWG\Property(property="loaner", type="string", maxLength=255),
     *              @SWG\Property(property="loanDate", type="string", format="Y-m-d H:i"),
     *              @SWG\Property(property="returnDate", type="string", format="Y-m-d H:i"),
     *          )
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Returns ID of updated Loan",
     *     @SWG\Schema(
     *          @SWG\Property(property="id", type="string", format="UUID")
     *     )
     * )
     * @SWG\Response(
     *     response=422,
     *     description="Loan was not updated"
     * )
     *
     * @param UpdateLoanRequest $request
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function update(UpdateLoanRequest $request, ConstraintViolationListInterface $validationErrors): JsonResponse
    {
        if ($validationErrors->count()) {
            return $this->jsonError(ApiError::ENTITY_VALIDATION_ERROR,
                'Validations errors for update Loan',
                Response::HTTP_BAD_REQUEST,
                $this->parseFormErrors($validationErrors)
                );
        }
        
        try {
            $command = new UpdateLoanCommand(
                $request->getId(),
                $request->getItemId(),
                $request->getLoaner(),
                $request->getLoanDate(),
                $request->getReturnDate()
                );
            $this->commandBus->dispatch($command);
            $loan = $this->repository->getLoan($command->getId());
            return $this->json($loan, Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_CREATE_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        }
    }

    /**
     * Delete Loan
     *
     * @SWG\Tag(name="Loan")
     * @SWG\Response(
     *     response="204",
     *     description="Loan was deleted"
     * )
     * @SWG\Response(
     *     response="404",
     *     description="Loan not found",
     * )
     *
     * @SWG\Response(
     *     response="422",
     *     description="Loan was not deleted",
     * )
     *
     * @Route("/api/loan/{id}", name="loan_delete", methods={"DELETE"})
     * @param string $id
     * @return JsonResponse
     */
    public function delete(string $id): JsonResponse
    {
        try {
            $uuid = Uuid::fromString($id);
            $command = new DeleteLoanCommand($uuid);
            $this->commandBus->dispatch($command);
            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Ramsey\Uuid\Exception\InvalidUuidStringException $e) {
            $response = $this->jsonError(ApiError::ENTITY_UUID_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        } catch (LoanNotFoundException $e) {
            $response = $this->jsonError(ApiError::ENTITY_READ_ERROR,
                $e->getMessage(),
                Response::HTTP_NOT_FOUND
                );
        } catch (LoanNotDeletedException $e) {
            $response = $this->jsonError(ApiError::ENTITY_DELETE_ERROR,
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
                );
        }
    }
    
    /**
     * List loaned items
     *
     * @SWG\Tag(name="Loan")
     * @SWG\Response(
     *     response=200,
     *     description="List of loaned items",
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=App\Entity\Item::class))
     *     )
     * )
     *
     * @param Request $request
     * @Route("/api/loaned", name="loaned-items-list", methods={"GET"})
     * @return JsonResponse
     */
    public function getLoanedItems(Request $request): JsonResponse
    {
        try {
            return $this->json($this->repository->listLoans());
        } catch (\Exception$e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_CREATE_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        }
    }
}
