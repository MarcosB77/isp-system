<?php
namespace App\Domains\Client\DTOs;

readonly class CreateClientDTO
{
    public function __construct(
        public string  $name,
        public string  $email,
        public string  $cpf,
        public ?string $phone   = null,
        public ?string $address = null,
        public ?string $city    = null,
        public ?string $state   = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:    $data['name'],
            email:   $data['email'],
            cpf:     $data['cpf'],
            phone:   $data['phone']   ?? null,
            address: $data['address'] ?? null,
            city:    $data['city']    ?? null,
            state:   $data['state']   ?? null,
        );
    }
}
