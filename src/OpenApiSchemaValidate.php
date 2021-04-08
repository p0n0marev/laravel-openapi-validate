<?php

namespace P0n0marev\Testing\OpenApi;

use Illuminate\Http\JsonResponse;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Assert as PHPUnit;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;

trait OpenApiSchemaValidate
{

    private $responseValidator;

    public function buildResponseValidatorFromJson(string $json)
    {
        $validatorBuilder = new ValidatorBuilder;
        $validatorBuilder->fromJson($json);

        $this->responseValidator = $validatorBuilder->getResponseValidator();
    }

    private function convertResponse(JsonResponse $response)
    {
        $psr17Factory = new Psr17Factory;
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        return $psrHttpFactory->createResponse($response);
    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $response = parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);

        if($response instanceof JsonResponse) {
            $response = $this->convertResponse($response);

            $operation = new OperationAddress($uri, strtolower($method));

            $schemaValid = false;
            try {
                $this->responseValidator->validate($operation, $response);
                $schemaValid = true;
            } catch ( ValidationFailed $e ) {
                return PHPUnit::fail($e->getMessage());
            }
            $this->assertTrue($schemaValid);
        }
    }
}