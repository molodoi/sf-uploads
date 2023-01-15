<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\Validator\Constraints as Assert;

trait HasTitleSlugTrait
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Minimum 2 characters', maxMessage: 'Maximum 255 characters')]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Length(min: 2, max: 255)]
    #[Gedmo\Slug(fields: ['title'])]
    private ?string $slug = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}