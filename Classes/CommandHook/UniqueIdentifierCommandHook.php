<?php

namespace Neos\Form\Builder\CommandHook;

use Neos\ContentRepository\Core\CommandHandler\CommandHookInterface;
use Neos\ContentRepository\Core\CommandHandler\CommandInterface;
use Neos\ContentRepository\Core\Feature\NodeCreation\Command\CreateNodeAggregateWithNode;
use Neos\ContentRepository\Core\Feature\NodeModification\Command\SetNodeProperties;
use Neos\ContentRepository\Core\NodeType\NodeTypeManager;
use Neos\ContentRepository\Core\NodeType\NodeTypeName;
use Neos\ContentRepository\Core\NodeType\NodeTypeNames;
use Neos\ContentRepository\Core\Projection\ContentGraph\ContentGraphReadModelInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\ContentSubgraphInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindClosestNodeFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindDescendantNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\NodeType\NodeTypeCriteria;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\PropertyValue\Criteria\PropertyValueEquals;
use Neos\ContentRepository\Core\Projection\ContentGraph\VisibilityConstraints;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAggregateId;
use Neos\ContentRepository\Core\SharedModel\Node\PropertyName;

class UniqueIdentifierCommandHook implements CommandHookInterface
{
    const NEOS_FORM_BUILDER_NODE_BASED_FORM = 'Neos.Form.Builder:NodeBasedForm';
    const NEOS_FORM_BUILDER_IDENTIFIER_MIXIN = 'Neos.Form.Builder:IdentifierMixin';

    public function __construct(
        protected ContentGraphReadModelInterface $contentGraphReadModel,
        protected NodeTypeManager $nodeTypeMananger
    ) {
    }

    public function onBeforeHandle(CommandInterface $command): CommandInterface
    {
        return match (true) {
            $command instanceof SetNodeProperties => $this->handleSetNodeProperties($command),
            $command instanceof CreateNodeAggregateWithNode => $this->handleCreateNodeAggregateWithNode($command),
            default => $command
        };
    }

    private function handleSetNodeProperties(SetNodeProperties $command): CommandInterface
    {
        if (isset($command->propertyValues->values['identifier'])) {
            $contentGraph = $this->contentGraphReadModel->getContentGraph($command->workspaceName);
            $subgraph = $contentGraph->getSubgraph($command->originDimensionSpacePoint->toDimensionSpacePoint(), VisibilityConstraints::withoutRestrictions());
            $node = $subgraph->findNodeById($command->nodeAggregateId);
            if ($node === null || !$this->nodeTypeMananger->getNodeType($node->nodeTypeName)->isOfType(NodeTypeName::fromString(self::NEOS_FORM_BUILDER_IDENTIFIER_MIXIN))) {
                return $command;
            }
            $identifier = $this->findUniqueIdentifier($subgraph, $command->nodeAggregateId, $command->propertyValues->values['identifier']);
            return SetNodeProperties::create(
                $command->workspaceName,
                $command->nodeAggregateId,
                $command->originDimensionSpacePoint,
                $command->propertyValues->withValue('identifier', $identifier),
            );
        }
        return $command;
    }

    private function handleCreateNodeAggregateWithNode(CreateNodeAggregateWithNode $command): CommandInterface
    {
        if (isset($command->initialPropertyValues->values['identifier'])) {
            $contentGraph = $this->contentGraphReadModel->getContentGraph($command->workspaceName);
            $subgraph = $contentGraph->getSubgraph($command->originDimensionSpacePoint->toDimensionSpacePoint(), VisibilityConstraints::withoutRestrictions());

            $identifier = $this->findUniqueIdentifier($subgraph, $command->nodeAggregateId, $command->initialPropertyValues->values['identifier']);
            $command = $command->withInitialPropertyValues(
                $command->initialPropertyValues->withValue('identifier', $identifier)
            );
        }

        return $command;
    }

    private function findUniqueIdentifier(ContentSubgraphInterface $subgraph, NodeAggregateId $currentNodeAggregateId, string $identifier): string
    {
        $form = $subgraph->findClosestNode(
            $currentNodeAggregateId,
            FindClosestNodeFilter::create(
                NodeTypeCriteria::createWithAllowedNodeTypeNames(
                    NodeTypeNames::with(NodeTypeName::fromString(self::NEOS_FORM_BUILDER_NODE_BASED_FORM)),
                )
            )
        );

        $uniqueIdentifier = null;
        $possibleIdentifier = $identifier;
        $i = 1;
        while ($uniqueIdentifier === null) {
            $descendants = $subgraph->findDescendantNodes(
                $form->aggregateId,
                FindDescendantNodesFilter::create(
                    nodeTypes: NodeTypeCriteria::createWithAllowedNodeTypeNames(
                        NodeTypeNames::with(NodeTypeName::fromString(self::NEOS_FORM_BUILDER_IDENTIFIER_MIXIN)),
                    ),
                    propertyValue: PropertyValueEquals::create(PropertyName::fromString('identifier'), $possibleIdentifier, false),
                )
            );

            if ($descendants->count() === 0
                || $descendants->count() === 1 && $descendants->first()->aggregateId->equals($currentNodeAggregateId)) {
                $uniqueIdentifier = $possibleIdentifier;
            } else {
                $possibleIdentifier = $identifier . '-' . $i++;
            }
        }
        return $uniqueIdentifier;
    }
}
