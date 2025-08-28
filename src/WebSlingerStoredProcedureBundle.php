<?php

namespace WebSlinger\StoredProcedureFactory;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WebSlinger\StoredProcedureFactory\DependencyInjection\WebSlingerStoredProcedureExtension;

class WebSlingerStoredProcedureBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new WebSlingerStoredProcedureExtension();
    }
}