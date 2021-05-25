<?php

namespace App\Entity;

use App\Repository\ArticlesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArticlesRepository::class)
 */
class Articles
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Veuillez saisir un prix pour l'article")
     * @Assert\Type(type={"float","integer"}, message="Le prix de l'article doit être un nombre")
     * @Assert\Length(min=0, minMessage="Le prix doit être un nombre positif")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="articles")
     * @Assert\NotBlank(message="Veuillez choisir une catégorie")
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir un nom d'article")
     * @Assert\Type(type="string", message="Ce champs doit être rempli par une chaine de caractère")
     * @Assert\Length(max=255, maxMessage="Le nom ne peut dépasser 255 caractères")
     */
    private $nom;

    public $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\NotBlank(message="Veuillez saisir une description de l'article")
     * @Assert\Length(max=500, min=15, maxMessage="La description de l'article ne doit pas dépasser 500 caractères", minMessage="Veuillez remplir une description d'un minimum de 15 caractères")
     * @Assert\Type(type="string", message="Ce champs doit être rempli par une chaine de caractère")
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default" : 0})
     * @Assert\NotBlank(message="Veuillez saisir une quantité pour l'article")
     * @Assert\Type(type="integer", message="La valeur doit être un nombre entier")
     * @Assert\Length(min=0, minMessage="Le stock ne peut être négatif")
     */
    private $stock;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }
}
