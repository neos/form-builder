<?php
namespace Neos\Form\Builder\NodeType;

use Neos\ContentRepository\Domain\Model\NodeType;
use Neos\ContentRepository\NodeTypePostprocessor\NodeTypePostprocessorInterface;
use Neos\Flow\Annotations as Flow;

/**
 * Node Type post processor that populates the "resourceCollection" property with all configured resource collections
 */
class ResourceCollectionsPostprocessor implements NodeTypePostprocessorInterface
{

    /**
     * @Flow\InjectConfiguration(package="Neos.Flow", path="resource.collections")
     * @var array
     */
    protected $resourceCollectionSettings;

    public function process(NodeType $nodeType, array &$configuration, array $options)
    {
        $resourceCollectionOptions = [];
        foreach ($this->resourceCollectionSettings as $collectionName => $_) {
            $resourceCollectionOptions[] = [
                'value' => $collectionName,
                'label' => $collectionName,
            ];
        }
        $configuration['properties']['resourceCollection']['ui']['inspector']['editorOptions']['values'] = $resourceCollectionOptions;
    }
}
