<?php
namespace Neos\Form\Builder\Fusion;

use Neos\ContentRepository\Domain\Model\NodeInterface;
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

    /**
     * @Flow\Inject
     * @var ContentElementWrappingService
     */
    protected $contentElementWrappingService;

    /**
     * We don't render further pages immediately so we can properly wrap them with the page collection
     *
     * @var string
     */
    private $pendingOutput = '';

    public function evaluate()
    {
        $context = $this->runtime->getCurrentContext();

        if (!isset($context['formRuntime'])) {
            throw new FusionException(sprintf('Missing "formRuntime" in context for Form Element Wrapping Fusion object "%s" at "%s"', $this->fusionObjectName, $this->path), 1522829151);
        }
        /** @var FormRuntime $formRuntime */
        $formRuntime = $context['formRuntime'];
        $formRuntime->registerRenderCallback(function (string $output, RootRenderableInterface $renderable) {
            $renderingOptions = $renderable->getRenderingOptions();
            if (!isset($renderingOptions['_node']) || !isset($renderingOptions['_fusionPath'])) {
                // TODO error/log?
                return $output;
            }
            /** @var NodeInterface $node */
            $node = $renderingOptions['_node'];
            /** @var string $fusionPath */
            $fusionPath = $renderingOptions['_fusionPath'];

            if ($renderable instanceof Page) {
                $elementsNode = $node->getNode('elements');
                if ($elementsNode !== null) {
                    $output = $this->wrapNode($elementsNode, $output, $fusionPath);
                }
                // first page? add finisher collection and return the wrapped content
                if ($node->getNodeType()->isOfType('Neos.Form.Builder:NodeBasedForm')) {
                    $finishersNode = $node->getNode('finishers');
                    if ($finishersNode !== null) {
                        $output = $this->wrapNodeRecursively($finishersNode, '', $fusionPath . '/finishers') . $output;
                    }
                    if (!$renderable->getRootForm()->hasPageWithIndex(1)) {
                        $furtherPagesNode = $node->getNode('furtherPages');
                        if ($furtherPagesNode !== null) {
                            $output = $output . $this->wrapNode($furtherPagesNode, '', $fusionPath . '/furtherPages');
                        }
                    }
                    return $output;
                }

                // otherwise store wrapped page content until last page
                $this->pendingOutput .= $this->wrapNode($node, $output, $fusionPath);
                if (!$this->isLastPageNode($node)) {
                    return '';
                }
                return $this->wrapNode($node, $this->pendingOutput, $this->parentFusionPath($fusionPath));
            }
            return $this->wrapNodeRecursively($node, $output, $fusionPath);
        });

    }

    private function isLastPageNode(NodeInterface $node): bool
    {
        $flowQuery = new FlowQuery([$node]);
        /** @noinspection PhpUndefinedMethodInspection */
        return $flowQuery->next('[instanceof Neos.Form.Builder:FormPage]')->get(0) === null;
    }

    public function parentFusionPath(string $fusionPath): string
    {
        return substr($fusionPath, 0, strrpos($fusionPath, '/'));
    }

    private function wrapNodeRecursively(NodeInterface $node, string $output, string $fusionPath): string
    {
        /** @var NodeInterface $childNode */
        foreach ($node->getChildNodes() as $childNode) {
            $output .= $this->wrapNodeRecursively($childNode, '', $fusionPath . '/' . $childNode->getIdentifier());
        }
        return $this->wrapNode($node, $output, $fusionPath);
    }

    private function wrapNode(NodeInterface $node, string $output, string $fusionPath): string
    {
        $additionalAttributes = [
            'data-_neos-form-builder-type' => $node->getNodeType()->getName()
        ];
        if ($node->getNodeType()->isOfType('Neos.Neos:ContentCollection') && count($node->getChildNodes()) === 0) {
            $additionalAttributes['data-_neos-form-builder-empty-collection'] = true;
        }
        return $this->contentElementWrappingService->wrapContentObject($node, $output, $fusionPath, $additionalAttributes);
    }

}
