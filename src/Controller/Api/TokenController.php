<?php

namespace App\Controller\Api;

use App\Error\ApiError;
use App\Entity\User;
use App\Repository\UserRepository;
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
use App\Service\JWTService;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Enum\UserStatusEnum;
use App\Request\LoginRequest;
use App\Traits\JWTHelper;

class TokenController extends AbstractController
{
    use JsonErrorResponse;
    use JWTHelper;
    
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var JWTService
     */
    private $jwtService;

    /**
     * @param LoggerInterface $logger
     * @param UserRepository $repository
     * @param ValidatorInterface $validator
     * @param JWTService $jwtService
     */
    public function __construct(
        LoggerInterface $logger,
        UserRepository $repository,
        ValidatorInterface $validator,
        JWTService $jwtService
    )
    {
        $this->logger = $logger;
        $this->repository = $repository;
        $this->validator = $validator;
        $this->jwtService = $jwtService;
    }
    /**
     * @SWG\Tag(name="Security")
     * @SWG\Post(
     *     @SWG\Parameter(
     *          name="token",
     *          in="body",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              @SWG\Property(property="token", type="string", format="JWT", example="123.456.789"),
     *          )
     *     ),
     *     @SWG\Response(response=200, description="New JWT"),
     *     @SWG\Response(response=401, description="Unauthorized because of invalid token, expired or signature error"),
     *     @SWG\Response(response=404, description="User not found"),
     *     @SWG\Response(response="422", description="Validation failed")
     * )
     * @Route("/api/jwt/validate", name="validate-token", methods={"POST"})
     * @return JsonResponse
     */
    public function validateAction(Request $rawRequest): JsonResponse
    {
        $token = $this->getToken($rawRequest);
        try {
            $payload = $this->jwtService->decode($token);
            $user = $this->repository->getUser(Uuid::fromString($payload->id));

            $extraFields = [
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'status' => $user->getStatus()->getValue(),
                'roles' => $user->getRoles(),
            ];

            $newToken = $this->jwtService->generateToken($user->getId()->toString(), $user->getEmail(), $extraFields);
            return $this->json(['token' => $newToken]);
        } catch (UserNotFoundException $e) {
            return $this->jsonError(ApiError::ENTITY_READ_ERROR,
                $e->getMessage(),
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_READ_ERROR,
                $e->getMessage(),
                Response::HTTP_UNAUTHORIZED
            );
        }
    }

    /**
     * @SWG\Tag(name="Security")
     * @SWG\Post(
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              @SWG\Property(property="email", type="string"),
     *              @SWG\Property(property="password", type="string")
     *          )
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="JWT token",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="User not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Invalid login/pass"
     * )
     *
     * @Route("/api/login", name="auth", methods={"POST"})
     * @ParamConverter("request", converter="fos_rest.request_body")
     * @param LoginRequest $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function loginAction(LoginRequest $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($request);

        if ($errors->count()) {
            return $this->jsonError(ApiError::ENTITY_VALIDATION_ERROR,
                'Login request validations errors',
                Response::HTTP_BAD_REQUEST,
                $this->parseFormErrors($errors)
            );
        }

        try {
            $user = $this->repository->getUserByEmail($request->getEmail());
            $valid = $encoder->isPasswordValid($user, $request->getPassword());
            if (!$valid) {
                throw new \Exception('Invalid login or password');
            }
        } catch (UserNotFoundException $e) {
            return $this->jsonError(ApiError::ENTITY_READ_ERROR,
                $e->getMessage(),
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_READ_ERROR,
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
            );
        }

        $extraFields = [
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'status' => $user->getStatus()->getValue(),
            'roles' => $user->getRoles(),
        ];

        $token = $this->jwtService->generateToken($user->getId()->toString(), $user->getEmail(), $extraFields);
        return $this->json(['token' => $token]);
    }   
}
