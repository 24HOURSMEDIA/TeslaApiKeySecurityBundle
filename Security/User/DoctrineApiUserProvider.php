<?php
// $Id:  $
// $HeadURL:  $
/**
 * Created by JetBrains PhpStorm.
 * User: eapbachman
 * Date: 26/10/13
 * Time: 01:43
 * To change this template use File | Settings | File Templates.
 */

namespace Tesla\Bundle\ApiKeySecurityBundle\Security\User;


use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class DoctrineApiUserProvider implements ApiUserProviderInterface
{

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $om;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;
    private $cacheTtl;

    public function __construct(ObjectManager $om, $cache = null, $cacheTtl = 300)
    {

        $this->om = $om;
        $this->cache = $cache;
        $this->cacheTtl = $cacheTtl;
    }

    /**
     * @param $key
     * @throws UsernameNotFoundException if the user is not found
     * @return ApiUser
     */
    public function loadUserByKey($key)
    {
        // cache support
        if ($this->cache) {
            $cacheKey = sha1('abcwe.' . $key);
            if ($user = $this->cache->fetch($cacheKey)) {
                return $user;
            }
        }
        $dbUser = $this->om->getRepository('TeslaApiKeySecurityBundle:ApiKey')->findOneByApiKey((string)$key);
        if (!$dbUser) {
            throw new UsernameNotFoundException('API user not found');
        }
        // keep track of last access date
        // not really the place, but...
        $la = $dbUser->getLastAccessDate();
        $now = new \DateTime();
        if (!$la || $la->format('Y-m-d') != $now->format('Y-m-d')) {

            $dbUser->setLastAccessDate(new \DateTime());
            $this->om->flush($dbUser);
        }
        $user = new ApiUser();
        $user
            ->setKey($dbUser->getApiKey())
            ->setName($dbUser->getName())
            ->setRoles($dbUser->getRoles())
            ->setExpires($dbUser->getExpires())
            ->setEnvironments($dbUser->getEnvironments())
            ->setActive($dbUser->getActive());

        // cache support
        if ($this->cache) {
            $cacheKey = sha1('abcwe.' . $key);
            $this->cache->save($cacheKey, $user, $this->cacheTtl);
        }

        return $user;
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
        // TODO: Implement refreshUser() method.
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
        // TODO: Implement supportsClass() method.
    }


}