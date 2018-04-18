<?php
namespace Neos\Form\Builder\NodeType;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Neos\Ui\NodeCreationHandler\NodeCreationHandlerInterface;

class SelectOptionsCreationHandler implements NodeCreationHandlerInterface
{
    /**
     * @param NodeInterface $node The newly created node
     * @param array $data incoming data from the creationDialog
     * @return void
     */
    public function handle(NodeInterface $node, array $data)
    {
        if (!$node->getNodeType()->isOfType('Neos.Form.Builder:SelectOption')) {
            return;
        }
        if (isset($data['value'])) {
            $node->setProperty('value', $data['value']);
        }
        if (isset($data['label'])) {
            $node->setProperty('label', $data['label']);
        }
    }
}
