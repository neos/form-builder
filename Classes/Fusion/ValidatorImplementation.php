<?php
namespace Neos\Form\Builder\Fusion;

use Neos\Form\Core\Model\Renderable\AbstractRenderable;
use Neos\Fusion\Exception as FusionException;
use Neos\Fusion\FusionObjects\AbstractFusionObject;

class ValidatorImplementation extends AbstractFusionObject
{
    public function evaluate()
    {
        $context = $this->runtime->getCurrentContext();
        if (!isset($context['element'])) {
            throw new FusionException(sprintf('Missing "element" in context for Validator Fusion object "%s" at "%s"', $this->fusionObjectName, $this->path), 1522829281);
        }
        /** @var AbstractRenderable $element */
        $element = $context['element'];

        $element->createValidator($this->getFormElementType(), $this->getOptions());
    }

    private function getFormElementType(): string
    {
        $formElementType = $this->fusionValue('formElementType');
        if ($formElementType === null) {
            throw new FusionException(sprintf('Missing formElementType for Validator Fusion object "%s" at "%s"', $this->fusionObjectName, $this->path), 1502465855);
        }
        return $formElementType;
    }

    private function getOptions(): array
    {
        return $this->fusionValue('options');
    }

}