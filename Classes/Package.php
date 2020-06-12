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
    const NODE_TYPE_IDENTIFIER_MIXIN = 'Neos.Form.Builder:IdentifierMixin';

    /**
     * @param Bootstrap $bootstrap The current bootstrap
     * @return void
     */
    public function boot(Bootstrap $bootstrap)
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();

        $dispatcher->connect(Node::class, 'nodePropertyChanged', function (NodeInterface $node, $propertyName, $_, $newValue) use ($bootstrap) {
            if ($propertyName !== 'identifier' || empty($newValue) || !$node->getNodeType()->isOfType(self::NODE_TYPE_IDENTIFIER_MIXIN)) {
                return;
            }

            $this->setUniqueFormElementIdentifier($node, $newValue);
        });

        $dispatcher->connect(Node::class, 'nodeAdded', function (NodeInterface $node) use ($bootstrap) {
            try {
                $identifier = $node->getProperty('identifier');

                if (empty($identifier) || !$node->getNodeType()->isOfType(self::NODE_TYPE_IDENTIFIER_MIXIN)) {
                    return;
                }
            } catch (\Neos\ContentRepository\Exception\NodeException $e) {
                return;
            }

            $this->setUniqueFormElementIdentifier($node, $identifier);
        });
    }

    /**
     * @param NodeInterface $node
     * @param string $identifier
     * @throws \Neos\Eel\Exception
     */
    protected function setUniqueFormElementIdentifier(NodeInterface $node, string $identifier): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $flowQuery = (new FlowQuery([$node]))->context([
            'invisibleContentShown' => true,
            'removedContentShown' => true,
            'inaccessibleContentShown' => true
        ]);
        $possibleIdentifier = $identifier;
        $i = 1;
        /** @noinspection PhpUndefinedMethodInspection */
        while ($flowQuery
                ->closest('[instanceof Neos.Form.Builder:NodeBasedForm]')
                // [identifier=".."] matches the Form Element identifier, [_identiier!="..."] excludes the current node
                ->find(sprintf('[instanceof %s][identifier="%s"][_identifier!="%s"]',
                    self::NODE_TYPE_IDENTIFIER_MIXIN ,$possibleIdentifier, $node->getIdentifier()))
                ->count() > 0) {
            $possibleIdentifier = $identifier . '-' . $i++;
        }
        $node->setProperty('identifier', $possibleIdentifier);
    }
}
