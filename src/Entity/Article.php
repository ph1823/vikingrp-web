<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[Vich\Uploadable]
class Article implements \Serializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(length: 255)]
    private ?string $articleImageName = "";

    #[Vich\UploadableField(mapping: 'textures', fileNameProperty: 'skin')]
    private ?File $articleImage = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getArticleImageName(): ?string
    {
        return $this->articleImageName;
    }

    public function setArticleImageName(?string $articleImageName): static
    {
        $this->articleImageName = $articleImageName;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|null $articleImage
     */
    public function setArticleImage(?File $articleImage = null): void
    {
        $this->articleImage = $articleImage;

        if (null !== $articleImage) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getArticleImage(): ?File
    {
        return $this->articleImage;
    }

    public function serialize()
    {
        return $this->__serialize();
    }

    public function unserialize(string $data)
    {
        return $this->unserialize($data);
    }

    public function __serialize(): array
    {
        return ["id" => $this->getId(),
            "title" => $this->getTitle(),
            "content" => $this->getContent()];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data["id"];
        $this->setTitle($data["title"]);
        $this->setContent($data["content"]);
    }
}
