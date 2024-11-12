<?php
namespace Neos\Form\Builder\Fusion;

use Neos\ContentRepository\Core\Projection\ContentGraph\ContentSubgraphInterface;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindChildNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\ContentRepository\Core\SharedModel\Node\NodeName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;
use Neos\Form\Core\Model\Page;
use Neos\Form\Core\Model\Renderable\RootRenderableInterface;
use Neos\Form\Core\Runtime\FormRuntime;
use Neos\Fusion\Exception as FusionException;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use Neos\Neos\Service\ContentElementWrappingService;

class FormElementWrappingImplementation extends AbstractFusionObject
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    #[Flow\Inject]
    protected ContentElementWrappingService $contentElementWrappingService;

    /**
     * We don't render further pages immediately so we can properly wrap them with the page collection
     */
    private string $pendingOutput = '';

    public function evaluate()
    {
        $context = $this->runtime->getCurrentContext();

        /** @var FormRuntime $formRuntime */
        if (!isset($context['formRuntime'])) {
            throw new FusionException(sprintf('Missing "formRuntime" in context for Form Element Wrapping Fusion object "%s" at "%s"', $this->fusionObjectName, $this->path), 1522829151);
        }
        $formRuntime = $context['formRuntime'];
        $formRuntime->registerRenderCallback(function (string $output, RootRenderableInterface $renderable) {
            $renderingOptions = $renderable->getRenderingOptions();
            if (!isset($renderingOptions['_node']) || !isset($renderingOptions['_fusionPath'])) {
                // TODO error/log?
                return $output;
            }
            /** @var Node $node */
            $node = $renderingOptions['_node'];
            $subgraph = $this->contentRepositoryRegistry->subgraphForNode($node);

            /** @var string $fusionPath */
            $fusionPath = $renderingOptions['_fusionPath'];

            if ($renderable instanceof Page) {
                $elementsNode = $subgraph->findNodeByPath(NodeName::fromString('elements'), $node->aggregateId);
                if ($elementsNode !== null) {
                    $output = $this->wrapNode($subgraph, $elementsNode, $output, $fusionPath);
                }
                // first page? add finisher collection and return the wrapped content
                $nodeTypeManager = $this->contentRepositoryRegistry->get($node->contentRepositoryId)->getNodeTypeManager();
                if ($nodeTypeManager->getNodeType($node->nodeTypeName)->isOfType('Neos.Form.Builder:NodeBasedForm')) {
                    $finishersNode = $subgraph->findNodeByPath(NodeName::fromString('finishers'), $node->aggregateId);
                    if ($finishersNode !== null) {
                        $output = $this->wrapNodeRecursively($subgraph, $finishersNode, '', $fusionPath . '/finishers') . $output;
                    }
                    if (!$renderable->getRootForm()->hasPageWithIndex(1)) {
                        $furtherPages = $subgraph->findNodeByPath(NodeName::fromString('furtherPages'), $node->aggregateId);
                        $output = $output . $this->wrapNode($subgraph, $furtherPages, '', $fusionPath . '/furtherPages');
                    }
                    return $output;
                }

                // otherwise store wrapped page content until last page
                $this->pendingOutput .= $this->wrapNode($subgraph, $node, $output, $fusionPath);
                if (!$this->isLastPageNode($node)) {
                    return '';
                }
                return $this->wrapNode($subgraph, $node, $this->pendingOutput, $this->parentFusionPath($fusionPath));
            }
            return $this->wrapNodeRecursively($subgraph, $node, $output, $fusionPath);
        });

    }

    private function isLastPageNode(Node $node): bool
    {
        $flowQuery = new FlowQuery([$node]);
        /** @noinspection PhpUndefinedMethodInspection */
        return $flowQuery->next('[instanceof Neos.Form.Builder:FormPage]')->get(0) === null;
    }

    public function parentFusionPath(string $fusionPath): string
    {
        return substr($fusionPath, 0, strrpos($fusionPath, '/'));
    }

    private function wrapNodeRecursively(ContentSubgraphInterface $subgraph, Node $node, string $output, string $fusionPath): string
    {
        /** @var Node $childNode */
        foreach ($subgraph->findChildNodes($node->aggregateId, FindChildNodesFilter::create()) as $childNode) {
            $output .= $this->wrapNodeRecursively($subgraph, $childNode, '', $fusionPath . '/' . $childNode->aggregateId->value);
        }
        return $this->wrapNode($subgraph, $node, $output, $fusionPath);
    }

    private function wrapNode(ContentSubgraphInterface $subgraph, Node $node, string $output, string $fusionPath): string
    {
        $additionalAttributes = [
            'data-_neos-form-builder-type' => $node->nodeTypeName
        ];
        $nodeTypeManager = $this->contentRepositoryRegistry->get($node->contentRepositoryId)->getNodeTypeManager();
        if ($nodeTypeManager->getNodeType($node->nodeTypeName)->isOfType('Neos.Neos:ContentCollection') && $subgraph->findChildNodes($node->aggregateId, FindChildNodesFilter::create())->count() === 0) {
            $additionalAttributes['data-_neos-form-builder-empty-collection'] = true;
        }
        return $this->contentElementWrappingService->wrapContentObject($node, $output, $fusionPath, $additionalAttributes);
    }
}