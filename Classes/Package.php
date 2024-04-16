<?php
namespace Neos\Form\Builder;

use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;

/**
 * The Package class, wiring signal/slot during boot.
 */
class Package extends BasePackage
{

    /**
     * @param Bootstrap $bootstrap The current bootstrap
     * @return void
     */
    public function boot(Bootstrap $bootstrap)
    {
        # BREAKING in Neos 9: No node signals anymore
        # Missing here: Setting of identifier for Neos.Form.Builder:NodeBasedForm on nodePropertyChanged and nodeAdded
    }
}
