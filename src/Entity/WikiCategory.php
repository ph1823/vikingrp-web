<?php

namespace App\Entity;

use App\Repository\WikiCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: WikiCategoryRepository::class)]
#[Vich\Uploadable]
class WikiCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $cat_url = null;

    #[ORM\Column(length: 255)]
    private ?string $catImageName = "";

    #[Vich\UploadableField(mapping: 'wiki_icon', fileNameProperty: 'catImageName')]
    private ?File $catImage = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'wikiCategory', targetEntity: WikiPage::class, cascade: ['remove'])]
    private Collection $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCatUrl(): ?string
    {
        return $this->cat_url;
    }

    public function setCatUrl(string $cat_url): static
    {
        $this->cat_url = $cat_url;

        return $this;
    }

    public function getCatImageName(): ?string
    {
        return $this->catImageName;
    }

    public function setCatImageName(?string $catImageName): static
    {
        $this->catImageName = $catImageName;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|null $catImage
     */
    public function setCatImage(?File $catImage = null): void
    {
        $this->catImage = $catImage;

        if (null !== $catImage) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getCatImage(): ?File
    {
        return $this->catImage;
    }

    /**
     * @return Collection<int, WikiPage>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(WikiPage $page): static
    {
        if (!$this->pages->contains($page)) {
            $this->pages->add($page);
            $page->setWikiCategory($this);
        }

        return $this;
    }

    public function removePage(WikiPage $page): static
    {
        if ($this->pages->removeElement($page)) {
            // set the owning side to null (unless already changed)
            if ($page->getWikiCategory() === $this) {
                $page->setWikiCategory(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->cat_url;
    }
}
