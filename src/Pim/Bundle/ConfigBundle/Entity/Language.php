<?php
namespace Pim\Bundle\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language entity
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @ORM\Table(name="pim_lang")
 * @ORM\Entity
 */
class Language
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $code
     *
     * @ORM\Column(name="code", type="string", length=5)
     */
    protected $code;

    /**
     * @var boolean $activated
     *
     * @ORM\Column(name="is_activate", type="boolean")
     */
    protected $activated;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activated = true;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return \Pim\Bundle\ConfigBundle\Entity\Language
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return \Pim\Bundle\ConfigBundle\Entity\Language
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get activated
     *
     * @return boolean
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * Set activated
     *
     * @param boolean $activated
     *
     * @return \Pim\Bundle\ConfigBundle\Entity\Language
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;

        return $this;
    }
}
