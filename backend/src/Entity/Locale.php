<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\LocaleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LocaleRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(paginationEnabled: false)
    ],
    normalizationContext: ['groups' => ['locale:read']]
)]
class Locale
{
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column
    ]
    #[Groups(['locale:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 2)]
    #[Groups(['locale:read'])]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Groups(['locale:read'])]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
