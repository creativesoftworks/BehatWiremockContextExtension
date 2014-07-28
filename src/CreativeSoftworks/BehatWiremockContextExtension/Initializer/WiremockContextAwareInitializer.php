<?php

namespace CreativeSoftworks\BehatWiremockContextExtension\Initializer;

use Behat\Behat\Context\Initializer\InitializerInterface;
use Behat\Behat\Context\ContextInterface;
use CreativeSoftworks\BehatWiremockContextExtension\WiremockContextAware;
use CreativeSoftworks\BehatWiremockContextExtension\WiremockContext;

class WiremockContextAwareInitializer implements InitializerInterface
{
    /**
     * @var \CreativeSoftworks\BehatWiremockContextExtension\WiremockContext
     */
    private $wiremockContext;
    
    /**
     * @param \CreativeSoftworks\BehatWiremockContextExtension\WiremockContext $wiremockContext
     */
    public function __construct(WiremockContext $wiremockContext)
    {
        $this->wiremockContext = $wiremockContext;
    }
    
    /**
     * @param \CreativeSoftworks\BehatWiremockContextExtension\WiremockContextAware $context
     */
    public function initialize(WiremockContextAware $context)
    {
        $context->setWiremockContext($this->wiremockContext);
    }

    /**
     * @param \Behat\Behat\Context\ContextInterface $context
     * 
     * @return type
     */
    public function supports(ContextInterface $context)
    {
        return $context instanceof WiremockContextAware;
    }

}
