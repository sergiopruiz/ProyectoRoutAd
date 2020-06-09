<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServicioAnuncio
 *
 * @ORM\Table(name="servicio_anuncio", indexes={@ORM\Index(name="FK_servicio_anuncio_servicio", columns={"id_servicio"}), @ORM\Index(name="FK_servicio_anuncio_anuncio", columns={"id_anuncio"})})
 * @ORM\Entity(repositoryClass="App\Repository\ServicioAnuncioRepository")
 */
class ServicioAnuncio
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
     * @var int
     *
     * @ORM\Column(name="segundo_reproduccion", type="integer", nullable=false)
     */
    private $segundoReproduccion;

    /**
     * @var Anuncio
     *
     * @ORM\ManyToOne(targetEntity="Anuncio",inversedBy="id")
     * @ORM\JoinColumn(name="id_anuncio", referencedColumnName="id")
     *
     */
    private $idAnuncio;

    /**
     * @var Servicio
     *
     * @ORM\ManyToOne(targetEntity="Servicio",inversedBy="id")
     * @ORM\JoinColumn(name="id_servicio", referencedColumnName="id")
     */
    private $idServicio;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSegundoReproduccion(): ?int
    {
        return $this->segundoReproduccion;
    }

    public function setSegundoReproduccion(int $segundoReproduccion): self
    {
        $this->segundoReproduccion = $segundoReproduccion;

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
