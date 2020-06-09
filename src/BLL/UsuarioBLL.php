<?php


namespace App\BLL;


use App\Entity\Usuario;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsuarioBLL extends BaseBLL
{

    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    /**
     * @var JWTTokenManagerInterface
     */
    private $jwtManager;

    /**
     * @required
     * @param UserPasswordEncoderInterface $encoder
     */
    public function setEncoder(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @required
     * @param JWTTokenManagerInterface $jwtManager
     */
    public function setJWTManager(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    public function nuevo(array $data): array
    {
        if (!isset($data['username']) || !isset($data['password']))
            throw new BadRequestHttpException('No se ha recibido el nombre y/o contraseña');
        $usuario = new Usuario();

        $usuario->setUsername($data['username']);
        $usuario->setPassword($this->encoder->encodePassword($usuario, $data['password']));

        return $this->guardaValidando($usuario);
    }
    public function cambiarRol(Usuario $usuario,string $role): array
    {
        $usuario->setRole($role);

        return $this->guardaValidando($usuario);
    }

    public function getAll(): array
    {
        $usuarios = $this->entityManager->getRepository(Usuario::class)->findAll();

        return $this->entitiesToArray($usuarios);
    }

    public function toArray($usuario): array
    {
        return [
            'id' => $usuario->getId(),
            'username' => $usuario->getUsername(),
            'role' => $usuario->getRole()
        ];
    }
}