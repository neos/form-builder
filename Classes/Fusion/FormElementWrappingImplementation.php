<?php
namespace Wwwision\Neos\Form\Fusion;

use Neos\ContentRepository\Domain\Model\NodeInterface;
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

                if ($node->getParent()->getNodeType()->isOfType('Wwwision.Neos.Form:PageCollection')) {
                    $output = $this->contentElementWrappingService->wrapContentObject($node, $output, $fusionPath);

                    $output = $this->contentElementWrappingService->wrapContentObject($node->getParent(), $output, $fusionPath);
                    $this->pendingOutput .= $output;
                    return '';
                }
                $finishersNode = $node->getNode('finishers');
                if ($finishersNode !== null) {
                    $output = $this->wrapNodeRecursively($finishersNode, '', $fusionPath . '/finishers') . $output;
                }
                $furtherPagesNode = $node->getNode('furtherPages');
                if ($furtherPagesNode !== null) {
                    $output .= $this->wrapNodeRecursively($furtherPagesNode, $this->pendingOutput, $fusionPath . '/furtherPages');
                }
                return $output;
            }

            return $this->wrapNodeRecursively($node, $output, $fusionPath);
        });
    }

    private function wrapNodeRecursively(NodeInterface $node, string $output, string $fusionPath): string
    {
        /** @var NodeInterface $childNode */
        foreach ($node->getChildNodes() as $childNode) {
            $output .= $this->wrapNodeRecursively($childNode, '', $fusionPath . '/' . $node->getIdentifier());
        }
        return $this->contentElementWrappingService->wrapContentObject($node, $output, $fusionPath);
    }

}