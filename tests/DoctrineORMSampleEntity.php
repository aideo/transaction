<?php

use Doctrine\ORM\Mapping AS ORM;

/**
 * Class DoctrineORMSampleEntity
 *
 * @ORM\Entity
 * @ORM\Table(name = "SAMPLE")
 */
class DoctrineORMSampleEntity
{

    /**
     *
     * @var int
     * @ORM\Id
     * @ORM\Column
     */
    public $id;

    /**
     *
     * @var string
     * @ORM\Column(type="string")
     */
    public $name;

}
