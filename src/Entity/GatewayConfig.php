<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\GatewayConfig as BaseGatewayConfig;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class GatewayConfig extends BaseGatewayConfig
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    protected $id;
}
