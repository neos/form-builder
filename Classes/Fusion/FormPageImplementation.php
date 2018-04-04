<?php

namespace Neos\Form\Builder\Fusion;

use Neos\Form\Core\Model\FormDefinition;
use Neos\Fusion\Exception as FusionException;
use Neos\Fusion\FusionObjects\AbstractFusionObject;

class FormPageImplementation extends AbstractFusionObject
{

    public function getPath(): string
    {
        return $this->path;
    }

    public function evaluate()
    {
        $context = $this->runtime->getCurrentContext();
        if (!isset($context['form'])) {
            throw new FusionException(sprintf('Missing "form" in context for Form Page Fusion object "%s" at "%s"', $this->fusionObjectName, $this->path), 1522829233);
        }
        /** @var FormDefinition $formDefinition */
        $formDefinition = $context['form'];

        $page = $formDefinition->createPage($this->fusionValue('identifier'), $this->getFormElementType());
        $page->setLabel($this->getLabel());
        foreach ($this->getRenderingOptions() as $optionName => $optionValue) {
            $page->setRenderingOption($optionName, $optionValue);
        }

        $this->runtime->pushContext('parentRenderable', $page);
        $this->runtime->evaluate($this->path . '/elements');
        $this->runtime->popContext();
    }

    private function getRenderingOptions(): array
    {
        return $this->fusionValue('renderingOptions');
    }

    private function getFormElementType(): string
    {
        $formElementType = $this->fusionValue('formElementType');
        if ($formElementType === null) {
            throw new FusionException(sprintf('Missing formElementType for Form Page Fusion object "%s" at "%s"', $this->fusionObjectName, $this->path), 1502465840);
        }
        return $formElementType;
    }

    /**
     * @return string|null
     */
    private function getLabel()
    {
        return $this->fusionValue('label');
    }
}
