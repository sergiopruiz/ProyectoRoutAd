<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Servicio
 *
 * @ORM\Table(name="servicio", indexes={@ORM\Index(name="FK_servicio_tablet", columns={"matricula"})})
 * @ORM\Entity(repositoryClass="App\Repository\ServicioRepository")
 */
class Servicio
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
     * @ORM\Column(name="latitud_origen", type="string", length=128, nullable=false)
     */
    private $latitudOrigen;

    /**
     * @var string
     *
     * @ORM\Column(name="longitud_origen", type="string", length=128, nullable=false)
     */
    private $longitudOrigen;

    /**
     * @var string
     *
     * @ORM\Column(name="latitud_destino", type="string", length=128, nullable=false)
     */
    private $latitudDestino;

    /**
     * @var string
     *
     * @ORM\Column(name="longitud_destino", type="string", length=128, nullable=false)
     */
    private $longitudDestino;

    /**
     * @var int
     *
     * @ORM\Column(name="duracion_ruta", type="integer", nullable=false, options={"comment"="En segundos"})
     */
    private $duracionRuta;

    /**
     * @var Tablet
     *
     * @ORM\ManyToOne(targetEntity="Tablet",inversedBy="matricula")
     * @ORM\JoinColumn(name="tablet_id", referencedColumnName="id")
     *
     */
    private $tabletId;

    /**
     * @param Tablet $tabletId
     */
    public function setTabletId(Tablet $tabletId): void
    {
        $this->tabletId = $tabletId;
    }

    /**
     * @return Tablet
     */
    public function getTabletId(): Tablet
    {
        return $this->tabletId;
    }

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

    public function getLatitudOrigen(): ?string
    {
        return $this->latitudOrigen;
    }

    public function setLatitudOrigen(string $latitudOrigen): self
    {
        $this->latitudOrigen = $latitudOrigen;

        return $this;
    }

    public function getLongitudOrigen(): ?string
    {
        return $this->longitudOrigen;
    }

    public function setLongitudOrigen(string $longitudOrigen): self
    {
        $this->longitudOrigen = $longitudOrigen;

        return $this;
    }

    public function getLatitudDestino(): ?string
    {
        return $this->latitudDestino;
    }

    public function setLatitudDestino(string $latitudDestino): self
    {
        $this->latitudDestino = $latitudDestino;

        return $this;
    }

    public function getLongitudDestino(): ?string
    {
        return $this->longitudDestino;
    }

    public function setLongitudDestino(string $longitudDestino): self
    {
        $this->longitudDestino = $longitudDestino;

        return $this;
    }

    public function getDuracionRuta(): ?int
    {
        return $this->duracionRuta;
    }

    public function setDuracionRuta(int $duracionRuta): self
    {
        $this->duracionRuta = $duracionRuta;

        return $this;
    }


}
