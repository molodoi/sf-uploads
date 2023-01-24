<?php

/*
 * This file is part of the Symfony package.
 * (c) Fabien Potencier <fabien@symfony.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Entity\Traits\HasIdTrait;
use App\Entity\Traits\HasTitleSlugTrait;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[Vich\Uploadable]
class Post
{
    use HasIdTrait;
    use TimestampableEntity;
    use HasTitleSlugTrait;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'posts', targetEntity: Category::class)]
    private ?Category $category = null;

    #[ORM\OneToOne(inversedBy: 'post', targetEntity: Image::class, cascade: ['persist', 'remove'])]
    private ?Image $featuredImage = null;

    #[ORM\OneToMany(mappedBy: 'gpost', targetEntity: Image::class, cascade: ['persist'])]
    private Collection $galleryImages;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->galleryImages = new ArrayCollection();
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getFeaturedImage(): ?Image
    {
        return $this->featuredImage;
    }

    public function setFeaturedImage(?Image $featuredImage): self
    {
        $this->featuredImage = $featuredImage;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getGalleryImages(): Collection
    {
        return $this->galleryImages;
    }

    public function addGalleryImage(Image $galleryImage): self
    {
        if (!$this->galleryImages->contains($galleryImage)) {
            $this->galleryImages->add($galleryImage);
            $galleryImage->setGpost($this);
        }

        return $this;
    }

    public function removeGalleryImage(Image $galleryImage): self
    {
        if ($this->galleryImages->removeElement($galleryImage)) {
            // set the owning side to null (unless already changed)
            if ($galleryImage->getGpost() === $this) {
                $galleryImage->setGpost(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->title;
    }
}
