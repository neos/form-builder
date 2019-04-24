<?php
namespace Neos\Form\Builder\Fusion\Helper;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Eel\ProtectedContextAwareInterface;

class NodeHelper implements ProtectedContextAwareInterface
{

    public function mergeProperties(array $properties, NodeInterface $node)
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
