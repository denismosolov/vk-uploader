<?php

namespace VkTool\Entiry\Vk;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Video
 *
 * @author denis
 * @ORM\Entity
 * @ORM\Table(name="vk_videos")
 */
class Video
{
    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length="128")
     */
    protected $youtubeId;

    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $votubeGroupId;

    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", length=4096)
     */
    protected $description;
}
