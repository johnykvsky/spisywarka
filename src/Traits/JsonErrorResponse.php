<?php
namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Shortcut for returning errors in json, just like for 200 OK.
 */
trait JsonErrorResponse
{
    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     * @param string $errorCode
     * @param string $errorMessage
     * @param int $httpStatusCode
     * @param array $errorData
     * @param array $headers
     * @param array $context
     * @return JsonResponse
     */
    protected function jsonError(
        string $errorCode,
        string $errorMessage,
        int $httpStatusCode = 400,
        array $errorData = [],
        array $headers = [],
        array $context = []
    ): JsonResponse
    {
        $response = [
            'errors' =>
                [
                    'code' => $errorCode,
                    'message' => $errorMessage
                ]
        ];

        if (!empty($errorData)) {
            $response = array_merge($response, $errorData);
        }

        if ($this->container->has('serializer')) {
            $json = $this->container->get('serializer')->serialize(
                $response,
                'json',
                array_merge(['json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS], $context)
            );

            return new JsonResponse($json, $httpStatusCode, $headers, true);
        }

        return new JsonResponse($response, $httpStatusCode, $headers);
    }

    /**
     * @param ConstraintViolationListInterface $validationErrors
     * @return array
     */
    public function parseFormErrors(ConstraintViolationListInterface $validationErrors): array
    {
        $result = [];

        foreach ($validationErrors as $error) {
            $result['invalid_fields'][$error->getPropertyPath()] = $error->getMessage();
        }

        return $result;
    }
}
