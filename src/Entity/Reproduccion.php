<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reproduccion
 *
 * @ORM\Table(name="reproduccion", indexes={@ORM\Index(name="FK_reproduccion_anuncio", columns={"id_anuncio"}), @ORM\Index(name="FK_reproduccion_servicio", columns={"id_servicio"})})
 * @ORM\Entity(repositoryClass="App\Repository\ReproduccionRepository")
 */
class Reproduccion
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
     * @ORM\Column(name="fecha", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $fecha = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="longitud", type="string", length=128, nullable=false)
     */
    private $longitud;

    /**
     * @var string
     *
     * @ORM\Column(name="latitud", type="string", length=128, nullable=false)
     */
    private $latitud;

    /**
     * @var Anuncio
     *
     * @ORM\ManyToOne(targetEntity="Anuncio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_anuncio", referencedColumnName="id")
     * })
     */
    private $idAnuncio;

    /**
     * @var Servicio
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_servicio", referencedColumnName="id")
     * })
     */
    private $idServicio;

    public function __construct(){
        $this->fecha = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getLongitud(): ?string
    {
        return $this->longitud;
    }

    public function setLongitud(string $longitud): self
    {
        $this->longitud = $longitud;

        return $this;
    }

    public function getLatitud(): ?string
    {
        return $this->latitud;
    }

    public function setLatitud(string $latitud): self
    {
        $this->latitud = $latitud;

        return $this;
    }

    public function getIdAnuncio(): ?Anuncio
    {
        return $this->idAnuncio;
    }

    public function setIdAnuncio(?Anuncio $idAnuncio): self
    {
        $this->idAnuncio = $idAnuncio;

        return $this;
    }

    public function getIdServicio(): ?Servicio
    {
        return $this->idServicio;
    }

    public function setIdServicio(?Servicio $idServicio): self
    {
        $this->idServicio = $idServicio;

        return $this;
    }


}
