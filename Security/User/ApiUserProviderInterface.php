<?php
// $Id:  $
// $HeadURL:  $
/**
 * Created by JetBrains PhpStorm.
 * User: eapbachman
 * Date: 25/10/13
 * Time: 21:19
 * To change this template use File | Settings | File Templates.
 */

namespace Tesla\Bundle\ApiKeySecurityBundle\Security\User;


use Symfony\Component\Security\Core\User\UserProviderInterface;
use Tesla\Bundle\ApiKeySecurityBundle\Security\ApiUser;

interface ApiUserProviderInterface extends UserProviderInterface
{

    /**
     * @param $key
     * @return ApiUser
     */
    public function loadUserByKey($key);

}