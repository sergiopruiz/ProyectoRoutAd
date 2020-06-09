<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TabletAnuncio
 *
 * @ORM\Table(name="tablet_anuncio", indexes={@ORM\Index(name="id", columns={"id"}), @ORM\Index(name="FK_tablet_anuncio_tablet", columns={"tablet"}), @ORM\Index(name="FK_tablet_anuncio_anuncio", columns={"anuncio"})})
 * @ORM\Entity
 */
class TabletAnuncio
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_descarga", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $fechaDescarga = 'CURRENT_TIMESTAMP';

    /**
     * @var Anuncio
     *
     * @ORM\ManyToOne(targetEntity="Anuncio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="anuncio", referencedColumnName="id")
     * })
     */
    private $anuncio;

    /**
     * @var Tablet
     *
     * @ORM\ManyToOne(targetEntity="Tablet")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tablet", referencedColumnName="id")
     * })
     */
    private $tablet;

    public function __construct(){
        $this->fechaDescarga = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaDescarga(): ?\DateTimeInterface
    {
        return $this->fechaDescarga;
    }

    public function setFechaDescarga(\DateTimeInterface $fechaDescarga): self
    {
        $this->fechaDescarga = $fechaDescarga;

        return $this;
    }

    public function getAnuncio(): ?Anuncio
    {
        return $this->anuncio;
    }

    public function setAnuncio(?Anuncio $anuncio): self
    {
        $this->anuncio = $anuncio;

        return $this;
    }

    public function getTablet(): ?Tablet
    {
        return $this->tablet;
    }

    public function setTablet(?Tablet $tablet): self
    {
        $this->tablet = $tablet;

        return $this;
    }


}
