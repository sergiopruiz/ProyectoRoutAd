<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tablet
 *
 * @ORM\Table(name="tablet", uniqueConstraints={@ORM\UniqueConstraint(name="tablet_matricula_uindex", columns={"matricula"})}, indexes={@ORM\Index(name="FK_tablet_usuario", columns={"id_usuario"})})
 * @ORM\Entity(repositoryClass="App\Repository\TabletRepository")
 */
class Tablet
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
     * @var string
     * @ORM\OneToMany(targetEntity="Servicio",mappedBy="tabletId")
     * @ORM\Column(name="matricula", type="string", length=7, nullable=true)
     */
    private $matricula;

    /**
     * @var string|null
     *
     * @ORM\Column(name="id_one_signal", type="string", length=256, nullable=true)
     */
    private $idOneSignal;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_update", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $fechaUpdate = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="imagen_corporativa", type="string", length=256, nullable=true)
     */
    private $imagenCorporativa;

    /**
     * @var Usuario
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="tablets")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     */
    private $idUsuario;

    public function __construct(){
        $this->fechaUpdate = new \DateTime();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatricula(): ?string
    {
        return $this->matricula;
    }

    public function setMatricula(string $matricula): self
    {
        $this->matricula = $matricula;

        return $this;
    }

    public function getIdOneSignal(): ?string
    {
        return $this->idOneSignal;
    }

    public function setIdOneSignal(?string $idOneSignal): self
    {
        $this->idOneSignal = $idOneSignal;

        return $this;
    }

    public function getFechaUpdate(): ?\DateTimeInterface
    {
        return $this->fechaUpdate;
    }

    public function setFechaUpdate(?\DateTimeInterface $fechaUpdate): self
    {
        $this->fechaUpdate = $fechaUpdate;

        return $this;
    }

    public function getImagenCorporativa(): ?string
    {
        return $this->imagenCorporativa;
    }

    public function setImagenCorporativa(string $imagenCorporativa): self
    {
        $this->imagenCorporativa = $imagenCorporativa;

        return $this;
    }

    public function getIdUsuario(): ?Usuario
    {
        return $this->idUsuario;
    }

    public function setIdUsuario(?Usuario $idUsuario): self
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }


}
