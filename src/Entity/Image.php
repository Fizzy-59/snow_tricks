<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use App\Service\ImageManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *  fields={"name"},
 *  message="File Name already use")
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $caption;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    private $file;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="images")
     */
    private $trick;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @ORM\PrePersist
     *
     * @return $this
     */
    public function setPath(): self
    {
        $this->path = 'img/tricks';

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getPathCropped(): ?string
    {
        return $this->path . '/cropped';
    }

    public function getPathThumbnail(): ?string
    {
        return $this->path . '/thumbnail';
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     *
     * @return $this
     */
    public function saveImage(): self
    {
        ImageManager::saveImage($this);
        return $this;
    }

    /**
     * @ORM\PreRemove
     *
     * @return $this
     */
    public function deleteImage(): self
    {
        ImageManager::deleteImage($this);
        return $this;
    }

    /**
     * @ORM\PreUpdate
     *
     * @return $this
     */
    public function preUpdate(): self
    {
        if (!empty($this->file)) {
            ImageManager::deleteImage($this);
            $this->name = md5(uniqid()) . '.' . $this->file->guessExtension();
        }

        return $this;
    }

    /**
     * @ORM\PrePersist
     *
     * @return $this
     */
    public function prePersist(): self
    {
        $this->name = md5(uniqid()) . '.' . $this->file->guessExtension();
        return $this;
    }

}
