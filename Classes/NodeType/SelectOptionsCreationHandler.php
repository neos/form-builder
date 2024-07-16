<?php
namespace Neos\Form\Builder\NodeType;

use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationHandlerFactoryInterface;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationHandlerInterface;
use Neos\ContentRepository\Core\ContentRepository;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationCommands;
use Neos\Neos\Ui\Domain\NodeCreation\NodeCreationElements;
use Neos\ContentRepository\Core\NodeType\NodeTypeManager;

final class SelectOptionsCreationHandler implements NodeCreationHandlerFactoryInterface
{

	public function build(ContentRepository $contentRepository): NodeCreationHandlerInterface
	{
		return new class ($contentRepository->getNodeTypeManager()) implements NodeCreationHandlerInterface {
			public function __construct(
				private readonly NodeTypeManager $nodeTypeManager
			) {
			}

			public function handle(NodeCreationCommands $commands, NodeCreationElements $elements): NodeCreationCommands
			{
				$nodeType = $this->nodeTypeManager->getNodeType($commands->first->nodeTypeName);

				if (!$nodeType->isOfType('Neos.Form.Builder:SelectOption')) {
					return $commands;
				}

				$propertyValues = $commands->first->initialPropertyValues;

				foreach ($elements as $elementName => $elementValue) {
					if ($elementName === 'value') {
						$propertyValues = $propertyValues->withValue('value', $elementValue);
					}
					if ($elementName === 'label') {
						$propertyValues = $propertyValues->withValue('label', $elementValue);
					}
				}

				return $commands
					->withInitialPropertyValues($propertyValues)
				;
			}
		};
	}
}