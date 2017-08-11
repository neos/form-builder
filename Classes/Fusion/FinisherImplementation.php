<?php
namespace Neos\Form\Builder\Fusion;

use Neos\Form\Core\Model\FormDefinition;
use Neos\Fusion\Exception as FusionException;
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
        $formElementType = $this->fusionValue('formElementType');
        if ($formElementType === null) {
            throw new FusionException(sprintf('Missing formElementType for Form Finisher Fusion object "%s" at "%s"', $this->fusionObjectName, $this->path), 1502465820);
        }
        return $formElementType;
    }

    private function getOptions(): array
    {
        return $this->fusionValue('options');
    }
}