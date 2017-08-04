<?php
namespace Neos\Form\Builder\Fusion;

use Neos\Form\Core\Model\Renderable\AbstractRenderable;
use Neos\Fusion\FusionObjects\AbstractFusionObject;

class ValidatorImplementation extends AbstractFusionObject
{
    public function evaluate()
    {
        $context = $this->runtime->getCurrentContext();
        // TODO error handling if "element" is not available
        /** @var AbstractRenderable $element */
        $element = $context['element'];

        $element->createValidator($this->getFormElementType(), $this->getOptions());
    }

    private function getFormElementType(): string
    {
        return $this->fusionValue('formElementType');
    }

    private function getOptions(): array
    {
        return $this->fusionValue('options');
    }

}