<?php

/**
 * Description of DealService
 *
 * @author Your Name <your.name at your.org>
 */

namespace AppBundle\Service;

/**
 * Description of EmpireService
 *
 * @author Josh Murphy
 */
class DealService
{
    /**
     *
     * @param unknown $cache
     */
    public function __construct($container)
    {
        $this->logger = $container->get('logger');
    }

  
}
