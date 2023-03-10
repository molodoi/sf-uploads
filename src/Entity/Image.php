<?php

namespace App\Entity;

use App\Entity\Traits\HasIdTrait;
use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[Vich\Uploadable]
class Image
{
    use HasIdTrait;
    use TimestampableEntity;

    #[Vich\UploadableField(mapping: 'post_gallery', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $imageSize = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Post $post = null;

    /**
     * Si vous téléchargez manuellement un fichier (c'est-à-dire sans utiliser Symfony Form), assurez-vous qu'une instance de 'UploadedFile' est injectée dans ce setter pour déclencher la mise à jour. Si le paramètre de configuration de ce bundle 'inject_on_load' est défini sur 'true' ce setter doit pouvoir accepter une instance de 'File' car le bundle en injectera une ici pendant l'hydratation Doctrine.
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // Il faut qu'au moins un champ change si vous utilisez la doctrine
            // sinon les écouteurs d'événements ne seront pas appelés et le fichier sera perdu
            // $this->createdAt = new \DateTime();
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function __toString(): string
    {
        return (string) $this->imageName;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
