<?php

declare(strict_types=1);


namespace App\Common\Dto;


use App\Common\Exception\InvalidJsonBodyException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class JsonRequestBody
{
    /**
     * @param Request $request
     * @return static
     * @throws InvalidJsonBodyException
     */
    abstract public static function ofRequest(Request $request): static;

    /**
     * @throws InvalidJsonBodyException
     */
    protected static function getValidData(Request $request, array $rules): array
    {
        $data = self::parseJsonResponseBody($request);
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            throw new InvalidJsonBodyException((string)$validator->errors());
        }

        try {
            return $validator->validated();
        } catch (ValidationException $e) {
            throw new InvalidJsonBodyException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws InvalidJsonBodyException
     */
    private static function parseJsonResponseBody(Request $request): array
    {
        $requestBody = $request->getContent();

        try {
            return json_decode($requestBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidJsonBodyException(
                sprintf('Invalid json. Details: %s', $e->getMessage()), $e->getCode(), $e
            );
        }
    }
}
