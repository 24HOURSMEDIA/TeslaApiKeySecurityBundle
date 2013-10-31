<?php
// $Id:  $
// $HeadURL:  $
/**
 * Created by JetBrains PhpStorm.
 * User: eapbachman
 * Date: 25/10/13
 * Time: 18:50
 * To change this template use File | Settings | File Templates.
 *
 * @see symfony.com/doc/current/cookbook/security/custom_authentication_provider.html
 */

namespace Tesla\Bundle\ApiKeySecurityBundle\Security\Firewall;


use Monolog\Logger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Tesla\Bundle\ApiKeySecurityBundle\Security\Authentication\Token\ApiKeyToken;

class ApiKeyFirewallListener implements ListenerInterface
{
    protected $securityContext;
    protected $methods = array();
    protected $fieldMap = array();
    protected $authenticationManager;

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, array $methods = array(), array $fieldMap = array())
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->methods = $methods;
        $this->fieldMap = $fieldMap;
    }

    public function setLogger(Logger $logger = null)
    {
        $this->logger = $logger;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $token = new ApiKeyToken();
        // get the token
        $key = null;
        foreach ($this->methods as $method) {
            switch ($method) {
                case 'request_get':
                    $var = $this->fieldMap['request_get'];
                    $key = $request->get($var);
                    break;
                case 'header':
                    $var = $this->fieldMap['header'];
                    $key = $request->headers->get($var);
                    if ($key) {
                        continue;
                    }
                    break;
                default:
                    throw new AuthenticationException('unrecognized method ' . $method);
            }
            if ($key) break;
        }
        $token->setApiKey($key);

        try {
            $authToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authToken);
            return;
        } catch (AuthenticationException $failed) {

            // logging
            if ($this->logger) {

                $this->logger->addAlert(
                    'Authentication Exception (ApiKeyFireWallListener): ' . $failed->getMessage(), array($event->getRequest(), $failed)
                );
            }


            // To deny the authentication clear the token. This will redirect to the login page.
            // Make sure to only clear your token, not those of other authentication listeners.
            // $token = $this->securityContext->getToken();
            // if ($token instanceof WsseUserToken && $this->providerKey === $token->getProviderKey()) {
            //     $this->securityContext->setToken(null);
            // }
            // return;

            $response = new Response();
            $response->setStatusCode(403);
            $event->setResponse($response);
        }
        // By default deny authorization
        $response = new Response();
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}