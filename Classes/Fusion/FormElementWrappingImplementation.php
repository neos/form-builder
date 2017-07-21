<?php
namespace Wwwision\Neos\Form\Fusion;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
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

    private function getFormNode(): NodeInterface
    {
        return $this->fusionValue('formNode');
    }

    public function evaluate()
    {
        $context = $this->runtime->getCurrentContext();
        // TODO error handling if "formRuntime" is not available
        /** @var FormRuntime $formRuntime */
        $formRuntime = $context['formRuntime'];
        $formRuntime->registerRenderCallback(function (string $output, RootRenderableInterface $renderable) {
            $renderingOptions = $renderable->getRenderingOptions();
            if ($renderable instanceof FormRuntime) {
                $node = $this->getFormNode();
                $fusionPath = $this->path;
            } elseif (!isset($renderingOptions['_node']) || !isset($renderingOptions['_fusionPath'])) {
                // TODO error/log?
                return $output;
            } else {
                $node = $renderingOptions['_node'];
                $fusionPath = $renderingOptions['_fusionPath'];
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