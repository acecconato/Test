<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\ClientRepository;
use App\State\ClientStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            normalizationContext: ['groups' => ['client.read']],
            processor: ClientStateProcessor::class
        ),
        new Get(normalizationContext: ['groups' => ['client.read']]),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['client.read']],
    denormalizationContext: ['groups' => ['client.write']]
)]
class Client
{
    #[ORM\Id]
    #[ORM\Column(unique: true, nullable: false)]
    #[ORM\GeneratedValue()]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Groups(['client.read', 'client.write'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Groups(['client.read', 'client.write'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    #[Groups(['client.read', 'client.write'])]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    #[Groups(['client.read', 'client.write'])]
    private ?string $phoneNumber = null;

    #[ORM\Column]
    #[Groups(['admin.client.read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'clients')]
    #[Groups(['client.read'])]
    private ?User $owner = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
