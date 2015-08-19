<?php

namespace Bananamanu\MultipleUserBundle\Service;

use eZ\Publish\API\Repository\Repository as Repository;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserMatcherService
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
         $this->repository = $repository;
    }

    /**
     * Match user provided with an ezpublish API user
     * @param TokenInterface $token
     * @return false|APIUser
     */
    public function matchUser($token)
    {
        // Get Roles
        $roles = $token->getRoles();


        // Iterate over roles and try to match user
        foreach ($roles as $role)
        {
            switch ($role->getRole())
            {
                case 'ROLE_GUEST' :
                    return $this->repository->getUserService()->loadUserByLogin('guestmember');
                    break;
                case 'ROLE_FOSUSER':
                    return $this->repository->getUserService()->loadUserByLogin('fos_demouser');
                    break;
            }
        }
        return false;
    }
}