<?php

namespace App\BLL;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseBLL
{
    /** @var  EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var ValidatorInterface $validator */
    protected $validator;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var ParameterBagInterface $params */
    protected $params;

    protected $BASE_URL = 'http://wallapush.sergiopr.tech/';
    protected $VIDEO_DIR = 'uploads/anuncios/';

//    abstract public function toArray($entity): array;

    /**
     * @required
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @required
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @required
     * @param TokenStorageInterface $tokenStorage
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @required
     * @param ParameterBagInterface $params
     */
    public function setParameterBag(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function entitiesToArray(array $entities)
    {
        if (is_null($entities))
            return null;

        $arr = [];
        foreach ($entities as $entity)
            $arr[] = $this->toArray($entity);

        return $arr;
    }

    private function validate($entity)
    {
        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            $strError = '';
            foreach ($errors as $error) {
                if (!empty($strError))
                    $strError .= '\n';
                $strError .= $error->getMessage();
            }
            throw new BadRequestHttpException($strError);
        }
    }

    protected function guardaValidando($entity)
    {
        $this->validate($entity);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toArray($entity);
    }

    public function delete($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * @return object|string|Usuario
     */
    protected function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}