<?php
namespace Wwwision\Neos\Form\Fusion;

use Neos\Form\Core\Model\FormDefinition;
use Neos\Fusion\FusionObjects\AbstractFusionObject;

class FinisherImplementation extends AbstractFusionObject
{

    public function evaluate()
    {
        $context = $this->runtime->getCurrentContext();
        // TODO error handling if "form" is not available
        /** @var FormDefinition $formDefinition */
        $formDefinition = $context['form'];

        $formDefinition->createFinisher($this->getFormElementType(), $this->getOptions());
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