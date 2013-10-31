<?php
// $Id:  $
// $HeadURL:  $
/**
 * Created by JetBrains PhpStorm.
 * User: eapbachman
 * Date: 26/10/13
 * Time: 00:53
 * To change this template use File | Settings | File Templates.
 */

namespace Tesla\Bundle\ApiKeySecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Tesla\Bundle\ApiKeySecurityBundle\Entity\Helpers\UidGenerator;

/**
 * Class ApiKey
 * @ORM\Entity
 * @ORM\Table("tesla_api_key",
 *      indexes={
 *          @ORM\Index(name="api_key", columns={"api_key"}),
 *          @ORM\Index(name="active", columns={"active"})
 *      }
 * )
 * @ORM\HasLifecycleCallbacks()
 * @package Tesla\Bundle\ApiKeySecurityBundle\Entity
 */
class ApiKey
{


    /**
     * @var string
     * @Assert\Length({"min"=8, "max"=34})
     * @ORM\Column(name="api_key", type="string", length=34, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $apiKey;

    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\NotNull()
     * @Assert\Length({"min"=1, "max"=128})
     * @ORM\Column(name="name", type="string", length=128, nullable=false, options={"default" = ""})
     */
    private $name;

    /**
     * @var array
     * @ORM\Column(name="roles", type="json_array", nullable=false)
     */
    private $roles = array("*");

    /**
     * @var array
     * @ORM\Column(name="env", type="json_array", nullable=false)
     */
    private $environments = array();

    /**
     * @var boolean
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires", type="datetime", nullable=false)
     */
    private $expires;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_access_date", type="date", nullable=true)
     */
    private $lastAccessDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    public function __construct()
    {

    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param \DateTime $lastAccessDate
     * @return $this
     */
    public function setLastAccessDate($lastAccessDate)
    {
        $this->lastAccessDate = $lastAccessDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastAccessDate()
    {
        return $this->lastAccessDate;
    }

    /**
     * @param boolean $active
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param \DateTime $created
     * @return $this
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param array $environments
     * @return $this
     */
    public function setEnvironments($environments)
    {
        $this->environments = $environments;
        return $this;
    }

    /**
     * @return array
     */
    public function getEnvironments()
    {
        return $this->environments;
    }

    /**
     * @param \DateTime $expires
     * @return $this
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Generates an ODM like id
     * @ORM\PrePersist
     */
    public function _generateKey()
    {
        $this->apiKey = UidGenerator::uid();
    }

    /**
     * Generates a creation date
     * @ORM\PrePersist
     */
    public function _setCreated()
    {
        $this->created = new \DateTime();
    }

    /**
     * Generates a creation date
     * @ORM\PrePersist
     */
    public function _setExpires()
    {
        $this->expires = new \DateTime('2099-12-31 23:59');
    }

}