<?php

declare(strict_types=1);


namespace App\Common\Dto;


use Illuminate\Http\Request;

class CreateUserRequest extends JsonRequestBody
{
    private string $name;
    private string $password;
    private string $email;

    /**
     * @param string $name
     * @param string $password
     * @param string $email
     */
    public function __construct(string $name, string $password, string $email)
    {
        $this->name = $name;
        $this->password = $password;
        $this->email = $email;
    }

    public static function ofRequest(Request $request): static
    {
        $data = self::getValidData($request, [
            'name' => ['required'],
            'password' => ['required'],
            'email' => ['required', 'email'],
        ]);

        return new CreateUserRequest(
            $data['name'],
            $data['password'],
            $data['email'],
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
