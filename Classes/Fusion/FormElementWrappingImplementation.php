<?php
namespace Wwwision\Neos\Form\Fusion;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;
use Neos\Form\Core\Model\Page;
use Neos\Form\Core\Model\Renderable\RootRenderableInterface;
use Neos\Form\Core\Runtime\FormRuntime;
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

        // TODO error handling if "formRuntime" is not available
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
                    $output = $this->contentElementWrappingService->wrapContentObject($elementsNode, $output, $fusionPath);
                }
                // first page? add finisher collection and return the wrapped content
                if ($node->getNodeType()->isOfType('Wwwision.Neos.Form:NodeBasedForm')) {
                    $finishersNode = $node->getNode('finishers');
                    if ($finishersNode !== null) {
                        $output = $this->wrapNodeRecursively($finishersNode, '', $fusionPath . '/finishers') . $output;
                    }
                    if (!$renderable->getRootForm()->hasPageWithIndex(1)) {
                        $output = $output . $this->contentElementWrappingService->wrapContentObject($node->getNode('furtherPages'), '', $fusionPath . '/furtherPages');
                    }
                    return $output;
                }

                // otherwise store wrapped page content until last page
                $this->pendingOutput .= $this->contentElementWrappingService->wrapContentObject($node, $output, $fusionPath);
                if (!$this->isLastPageNode($node)) {
                    return '';
                }
                return $this->contentElementWrappingService->wrapContentObject($node->getParent(), $this->pendingOutput, $this->parentFusionPath($fusionPath));
            }
            return $this->wrapNodeRecursively($node, $output, $fusionPath);
        });

    }

    private function isLastPageNode(NodeInterface $node): bool
    {
        $flowQuery = new FlowQuery([$node]);
        return $flowQuery->next('[instanceof Wwwision.Neos.Form:FormPage]')->get(0) === null;
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
        return $this->contentElementWrappingService->wrapContentObject($node, $output, $fusionPath);
    }

}