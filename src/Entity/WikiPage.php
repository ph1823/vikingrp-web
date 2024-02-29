<?php

namespace App\Entity;

use App\Repository\WikiPageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WikiPageRepository::class)]
class WikiPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\OneToMany(mappedBy: 'wikiPage', targetEntity: WikiImage::class, cascade: ['persist'])]
    private Collection $images;

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
}
