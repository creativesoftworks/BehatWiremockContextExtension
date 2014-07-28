<?php

namespace CreativeSoftworks\BehatWiremockContextExtension;

use CreativeSoftworks\BehatWiremockContextExtension\WiremockContext;

interface WiremockContextAware
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
