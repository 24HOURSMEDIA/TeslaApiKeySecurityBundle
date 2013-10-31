<?php
// $Id:  $
// $HeadURL:  $
/**
 * Created by JetBrains PhpStorm.
 * User: eapbachman
 * Date: 25/10/13
 * Time: 19:44
 * To change this template use File | Settings | File Templates.
 */

namespace Tesla\Bundle\ApiKeySecurityBundle\Security\User;


use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiUserProvider implements ApiUserProviderInterface
{


    /**
     * @var ApiUser[]
     */
    private $users = array();

    public function __construct(array $users)
    {

        foreach ($users as $userDef) {
            foreach ($userDef as $attr) {

                $user = new ApiUser();
                $user
                    ->setKey($attr['api_key'])
                    ->setName($attr['name'])
                    ->setRoles($attr['roles'])
                    ->setExpires(new \DateTime($attr['expires']))
                    ->setEnvironments($attr['environments']);
                $this->createUser($user);
            }

        }

    }

    function createUser(ApiUser $user)
    {
        if (array_key_exists($user->getKey(), $this->users)) {
            throw new UnsupportedUserException('Cannot have double users in provider');
        }
        $this->users[$user->getKey()] = $user;
    }

    /**
     * @param $key
     * @return ApiUser
     */
    public function loadUserByKey($key)
    {

        if (isset($this->users[$key])) {
            $user = $this->users[$key];
            // var_dump($user->getRoles());
            return $user;
        }
        return null;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     *
     */
    public function loadUserByUsername($username)
    {
        // TODO: Implement loadUserByUsername() method.

    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {

        return $user;
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     *
     * @return Boolean
     */
    public function supportsClass($class)
    {
        return 'Tesla\Bundle\ApiKeySecurityBundle\Security\ApiUser';
    }


}