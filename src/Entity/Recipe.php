<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\OneToMany;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    function __construct() {
        // cannot do it as default value cause it require execution
        $this->instructions = new ArrayCollection([]);
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(length: 255)]
    private ?string $description;

    #[ORM\Column(length: 255)]
    public ?string $image = null;  //image name

    #[OneToMany(targetEntity: Instruction::class, mappedBy: 'recipe', cascade: ['remove','persist'])]
    public Collection $instructions;
        
    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function addInstruction(Instruction $instruction): void
    {
        $this->instructions->add($instruction);
    }

    public function removeInstruction(Instruction $instruction): void
    {
        $this->instructions->removeElement($instruction);
    }
}
