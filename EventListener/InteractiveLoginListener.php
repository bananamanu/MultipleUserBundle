<?php

namespace Bananamanu\MultipleUserBundle\EventListener;

use eZ\Publish\Core\MVC\Symfony\Event\InteractiveLoginEvent;
use eZ\Publish\Core\MVC\Symfony\MVCEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Bananamanu\MultipleUserBundle\Service\UserMatcherService;

class InteractiveLoginListener implements EventSubscriberInterface
{

    /**
     * @var \Bananamanu\MultipleUserBundle\Service\UserMatcherService
     */
    private $userMatcherService;

    public function __construct( UserMatcherService $userMatcherService)
    {
        $this->userMatcherService = $userMatcherService;
    }

    public static function getSubscribedEvents()
    {
        return array(
            MVCEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin'
        );
    }

    public function onInteractiveLogin( InteractiveLoginEvent $event )
    {

        // Call userMatcher service and try to match user
        $apiUser = $this->userMatcherService->matchUser( $event->getAuthenticationToken() );
        if ($apiUser)
        {
            $event->setApiUser($apiUser);
        }
    }


}