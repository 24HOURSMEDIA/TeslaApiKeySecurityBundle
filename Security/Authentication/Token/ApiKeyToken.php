<?php
// $Id:  $
// $HeadURL:  $
/**
 * Created by JetBrains PhpStorm.
 * User: eapbachman
 * Date: 25/10/13
 * Time: 18:53
 * To change this template use File | Settings | File Templates.
 *
 * @see symfony.com/doc/current/cookbook/security/custom_authentication_provider.html
 *
 */

namespace Tesla\Bundle\ApiKeySecurityBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class ApiKeyToken extends AbstractToken
{

    public $created;
    public $digest;
    public $nonce;

    public $apiKey;

    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    /**
     * @param mixed $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getCredentials()
    {
        return '';
    }


}