<?php

namespace App\Entity;

use App\Repository\WikiPageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: WikiPageRepository::class)]
#[Vich\Uploadable]
class WikiPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\OneToMany(mappedBy: 'wikiPage', targetEntity: WikiImage::class, cascade: ['persist', 'remove'])]
    private Collection $images;

    #[ORM\ManyToOne(inversedBy: 'pages')]
    private ?WikiCategory $wikiCategory = null;

    #[ORM\Column(length: 255)]
    private ?string $iconImageName = "";

    #[Vich\UploadableField(mapping: 'wiki_icon', fileNameProperty: 'iconImageName')]
    private ?File $iconImage = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getIconImageName(): ?string
    {
        return $this->iconImageName;
    }

    public function setIconImageName(?string $iconImageName): static
    {
        $this->iconImageName = $iconImageName;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|null $iconImage
     */
    public function setIconImage(?File $iconImage = null): void
    {
        $this->iconImage = $iconImage;

        if (null !== $iconImage) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getIconImage(): ?File
    {
        return $this->iconImage;
    }

    /**
     * @return Collection<int, WikiImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(WikiImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setWikiPage($this);
        }

        return $this;
    }

    public function removeImage(WikiImage $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getWikiPage() === $this) {
                $image->setWikiPage(null);
            }
        }

        return $this;
    }

    public function getWikiCategory(): ?WikiCategory
    {
        return $this->wikiCategory;
    }

    public function setWikiCategory(?WikiCategory $wikiCategory): static
    {
        $this->wikiCategory = $wikiCategory;

        return $this;
    }

    public function __toString(): string
    {
        return $this->url;
    }
}
