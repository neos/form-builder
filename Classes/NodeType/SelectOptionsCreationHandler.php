<?php
namespace Neos\Form\Builder\NodeType;


use Neos\Neos\Ui\NodeCreationHandler\NodeCreationHandlerInterface;
use Neos\ContentRepository\Core\ContentRepository;
use Neos\ContentRepository\Core\Feature\NodeCreation\Command\CreateNodeAggregateWithNode;

class SelectOptionsCreationHandler implements NodeCreationHandlerInterface
{

    /**
     * @param CreateNodeAggregateWithNode $command The original node creation command
     * @param array<string|int,mixed> $data incoming data from the creationDialog
     * @return CreateNodeAggregateWithNode the original command or a new creation command with altered properties
     */
    public function handle(CreateNodeAggregateWithNode $command, array $data, ContentRepository $contentRepository): CreateNodeAggregateWithNode
    {
        if (!$contentRepository->getNodeTypeManager()->getNodeType($command->nodeTypeName)->isOfType('Neos.Form.Builder:SelectOption')) {
            return $command;
        }

        if (isset($data['value'])) {
            $propertyValues = $propertyValues->withValue('value', $data['value']);
        }
        if (isset($data['label'])) {
            $propertyValues = $propertyValues->withValue('label', $data['label']);
        }

        return $command->withInitialPropertyValues($propertyValues);
    }
}
