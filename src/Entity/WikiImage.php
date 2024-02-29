<?php

namespace App\Entity;

use App\Repository\WikiImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: WikiImageRepository::class)]
#[Vich\Uploadable]
class WikiImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $wikiImageName = "";

    #[Vich\UploadableField(mapping: 'textures', fileNameProperty: 'wikiImageName')]
    private ?File $wikiImage = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?WikiPage $wikiPage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWikiImageName(): ?string
    {
        return $this->wikiImageName;
    }

    public function setWikiImageName(?string $wikiImageName): static
    {
        $this->wikiImageName = $wikiImageName;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|null $wikiImage
     */
    public function setWikiImage(?File $wikiImage = null): void
    {
        $this->wikiImage = $wikiImage;

        if (null !== $wikiImage) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getWikiImage(): ?File
    {
        return $this->wikiImage;
    }

    public function getWikiPage(): ?WikiPage
    {
        return $this->wikiPage;
    }

    public function setWikiPage(?WikiPage $wikiPage): static
    {
        $this->wikiPage = $wikiPage;

        return $this;
    }
}
