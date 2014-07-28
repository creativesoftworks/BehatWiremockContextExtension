<?php

namespace CreativeSoftworks\BehatWiremockContextExtension;

use CreativeSoftworks\BehatWiremockContextExtension\WiremockContext;
use Behat\Behat\Context\ContextInterface;

interface WiremockContextAware extends ContextInterface
{
    /**
     * @param \CreativeSoftworks\BehatWiremockContextExtension\WiremockContext $wiremockContext
     */
    public function setWiremockContext(WiremockContext $wiremockContext);
    
    /**
     * @return \CreativeSoftworks\BehatWiremockContextExtension\WiremockContext
     */
    public function getWiremockContext();
}
