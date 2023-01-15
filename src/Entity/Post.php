<?php

namespace App\Entity;

use App\Entity\Traits\HasTimestampTrait;
use App\Entity\Traits\HasTitleSlugTrait;
use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdTrait;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    use HasIdTrait, HasTitleSlugTrait, HasTimestampTrait;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;


    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?Category $category = null;

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
