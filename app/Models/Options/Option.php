<?php
/**
 * Options
 *
 * @author  Jiří Šifalda
 * @package Flame
 *
 * @date    09.07.12
 */

namespace Flame\CMS\Models\Options;

/**
 * @Entity(repositoryClass="OptionRepository")
 */
class Option extends \Flame\Doctrine\Entity
{
    /**
     * @Column(type="string", length=50, unique=true)
     */
    protected $title;

    /**
     * @Column(type="string", length=250)
     */
    protected $content;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = (string) $title;
        return $this;
    }

    public function getValue()
    {
        return $this->content;
    }

    public function setValue($content)
    {
        $this->content = (string) $content;
        return $this;
    }

}
