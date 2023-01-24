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
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
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

    #[Vich\UploadableField(mapping: 'post_thumbnail', fileNameProperty: 'thumbName')]
    private ?File $thumbFile = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $thumbName = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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

    public function setThumbFile(?File $thumbFile = null): void
    {
        $this->thumbFile = $thumbFile;

        if (null !== $thumbFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime();
        }
    }

    public function getThumbFile(): ?File
    {
        return $this->thumbFile;
    }

    public function setThumbName(?string $thumbName): void
    {
        $this->thumbName = $thumbName;
    }

    public function getThumbName(): ?string
    {
        return $this->thumbName;
    }

    public function __toString(): string
    {
        return (string) $this->title;
    }
}
