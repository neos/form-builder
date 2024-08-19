<?php
namespace Neos\Form\Builder\Fusion\Helper;

use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\Projection\ContentGraph\PropertyCollection;
use Neos\Eel\ProtectedContextAwareInterface;

/**
 * Custom Eel Helper for node related functions
 */
class NodeHelper implements ProtectedContextAwareInterface
{

    /**
     * Merge properties of the specified $node to the given $properties (with precedence to node properties)
     *
     * @param array $properties
     * @param Node $node
     * @return array
     */
    public function mergeProperties(array $properties, Node $node): array
    {
        $nodeProperties = $node->properties;
        if ($nodeProperties instanceof PropertyCollection) {
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
