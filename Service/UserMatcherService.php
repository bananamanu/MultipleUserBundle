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
            $user = false;
            switch ($role->getRole())
            {
                case 'ROLE_GUEST' :
                    return $this->repository->getUserService()->loadUserByLogin('guestmember');
                    break;
                case 'ROLE_FOSUSER':
                    try
                    {
                        // Try to load user if exist
                        // All FOS users will be prefixed by 'fos_' for their API username
                        $username = 'fos_' . $token->getUsername();

                        //@TODO Check if user belong to fos_user group
                        $user = $this->repository->getUserService()->loadUserByLogin($username);
                    }
                    catch (\Exception $e)
                    {
                        // Create user with token info
                        $fosUser = $token->getUSer();
                        $this->repository->setCurrentUser( $this->repository->getUserService()->loadUser( 14 ) );

                        $contentTypeService = $this->repository->getContentTypeService();
                        $userType = $contentTypeService->loadContentTypeByIdentifier( 'user' );
                        $userService = $this->repository->getUserService();
                        $userCreateStruct = $userService->newUserCreateStruct( 'fos_' . $token->getUsername(), $fosUser->getEmail(), md5( $token->getUsername() . time()), 'eng-GB', $userType );
                        $userCreateStruct->setField('first_name', 'FOS ');
                        $userCreateStruct->setField('last_name', $token->getUsername());
                        $userGroup = $userService->loadUserGroup('137');
                        $user = $this->repository->getUserService()->createUser($userCreateStruct,array($userGroup));
                    }
                    break;
            }
        }
        return $user;
    }
}