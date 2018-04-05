<?php
namespace Neos\Form\Builder;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Core\Bootstrap;
use Neos\ContentRepository\Domain\Model\Node;
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
        $dispatcher = $bootstrap->getSignalSlotDispatcher();

        $dispatcher->connect(Node::class, 'nodePropertyChanged', function (NodeInterface $node, $propertyName, $_, $newValue) use ($bootstrap) {
            if ($propertyName !== 'identifier' || empty($newValue) || !$node->getNodeType()->isOfType('Neos.Form.Builder:IdentifierMixin')) {
                return;
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $flowQuery = (new FlowQuery([$node]))->context(['invisibleContentShown' => true, 'removedContentShown' => true, 'inaccessibleContentShown' => true]);
            $possibleIdentifier = $initialIdentifier = $newValue;
            $i = 1;
            /** @noinspection PhpUndefinedMethodInspection */
            while ($flowQuery->closest('[instanceof Neos.Form.Builder:NodeBasedForm]')->find(sprintf('[instanceof Neos.Form.Builder:IdentifierMixin][%s="%s"]', 'identifier', $possibleIdentifier))->count() > 0) {
                $possibleIdentifier = $initialIdentifier . '-' . $i++;
            }
            $node->setProperty('identifier', $possibleIdentifier);
        });
    }
}
