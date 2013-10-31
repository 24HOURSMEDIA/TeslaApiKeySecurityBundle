<?php
// $Id:  $
// $HeadURL:  $
/**
 * Created by JetBrains PhpStorm.
 * User: eapbachman
 * Date: 25/10/13
 * Time: 18:52
 * To change this template use File | Settings | File Templates.
 *
 * @see symfony.com/doc/current/cookbook/security/custom_authentication_provider.html
 */

namespace Tesla\Bundle\ApiKeySecurityBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
//use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Tesla\Bundle\ApiKeySecurityBundle\Security\User\ApiUserProviderInterface;
use Tesla\Bundle\ApiKeySecurityBundle\Security\Authentication\Token\ApiKeyToken;

/**
 * Class ApiKeyAuthenticationProvider
 * Provides the authentication for a token and returns a new authenticated token on success.
 * Authentication is based on:
 * - presence of api key embedded in token
 * - active state / expiration of api key
 * - allowed kernel environment for api key
 *
 * @package Tesla\Bundle\ApiKeySecurityBundle\Security\Authentication\Provider
 */
class ApiKeyAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var ApiUserProviderInterface
     */
    private $userProvider;

    /**
     * @var string
     */
    private $kernelEnvironment;


    public function __construct(ApiUserProviderInterface $userProvider, $kernelEnvironment)
    {

        $this->userProvider = $userProvider;
        $this->kernelEnvironment = $kernelEnvironment;
    }

    /**
     * @param TokenInterface $token
     * @return null|ApiKeyToken
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function authenticate(TokenInterface $token)
    {
        /**
         * @var $token ApiKeyToken
         */
        try {
            $apiUser = $this->userProvider->loadUserByKey($token->getApiKey());
            if (!$apiUser) {
                throw new AuthenticationException('API Key not found for token ' . $token->getApiKey());
            }
        } catch (UsernameNotFoundException $e) {

            throw new AuthenticationException('API Key / user not found for token ' . $token->getApiKey());
        }
        if ($apiUser->getEnvironments() !== null) {
            if (!in_array($this->kernelEnvironment, $apiUser->getEnvironments()) && (!in_array('*', $apiUser->getEnvironments()))) {
                throw new AuthenticationException('API Key not allowed in kernel environment ' . $this->kernelEnvironment . ' token:' . $token->getApiKey());
            }
        }
        if (!$apiUser->getActive()) {
            throw new AuthenticationException('API Key deactivated token:' . $token->getApiKey());
        }
        $authenticatedToken = new ApiKeyToken($apiUser->getRoles());
        if ($apiUser->getExpires() < new \DateTime()) {
            throw new AuthenticationException('API Key expired token:' . $token->getApiKey());
        }
        $authenticatedToken->setUser($apiUser);
        $authenticatedToken->setAuthenticated(true);
        return $authenticatedToken;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof ApiKeyToken;
    }
}