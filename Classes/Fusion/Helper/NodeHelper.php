<?php
namespace Neos\Form\Builder\Fusion\Helper;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Eel\ProtectedContextAwareInterface;

/**
 * Custom Eel Helper for node related functions
 */
class NodeHelper implements ProtectedContextAwareInterface
{

    /**
     * Merge properties of the specified $node to the given $properties (with precedence to node properties)
     *
     * Note: This is required since NodeInterface::getProperties() does no longer return an array but an instance of PropertyCollectionInterface
     *
     * @param array $properties
     * @param NodeInterface $node
     * @return array
     */
    public function mergeProperties(array $properties, NodeInterface $node): array
    {
        $nodeProperties = $node->getProperties();
        if ($nodeProperties instanceof \Traversable) {
            $nodeProperties = iterator_to_array($nodeProperties);
        }
        return array_merge($properties, $nodeProperties);
    }

    /**
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName): bool
    {
        return true;
    }
}
