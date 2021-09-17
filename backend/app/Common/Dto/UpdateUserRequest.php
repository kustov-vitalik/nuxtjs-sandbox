<?php

declare(strict_types=1);


namespace App\Common\Dto;


use Illuminate\Http\Request;

class UpdateUserRequest extends JsonRequestBody
{
    private string $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function ofRequest(Request $request): static
    {
        $validatedData = self::getValidData($request, [
            'name' => ['required'],
        ]);

        return new UpdateUserRequest($validatedData['name']);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
