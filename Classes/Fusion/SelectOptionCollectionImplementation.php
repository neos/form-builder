<?php

namespace Neos\Form\Builder\Fusion;

use Neos\ContentRepository\Core\Projection\ContentGraph\Node;
use Neos\Fusion\FusionObjects\AbstractArrayFusionObject;

class SelectOptionCollectionImplementation extends AbstractArrayFusionObject
{

    protected $ignoreProperties = [
        'prependOptionLabel',
        'prependOptionValue',
        'items',
        'itemName',
        'itemRenderer'
    ];

    public function evaluate()
    {
        $items = $this->getItems();
        $options = [];
        if (!empty($prependLabel = $this->getPrependOptionLabel())) {
            $options[$this->getPrependOptionValue()] = $prependLabel;
        }
        if ($items === null) {
            foreach ($this->properties as $propertyName => $propertyValue) {
                if (in_array($propertyName, $this->ignoreProperties)) {
                    continue;
                }
                $options[$propertyName] = $propertyValue;
            }
        } else {
            $renderedItems = $this->renderItems($items);
            foreach ($renderedItems as $renderedItem) {
                $value = $renderedItem['value'] ?? null;
                $label = $renderedItem['label'] ?? null;

                if ($value === null) {
                    continue;
                }

                if (strlen($label) === 0) {
                    $label = $value;
                }
                $options[(string)$value] = $label;
            }
        }
        return $options;
    }

    /**
     * @return array|\Traversable
     */
    private function getItems()
    {
        return $this->fusionValue('items');
    }

    private function getItemName()
    {
        return $this->fusionValue('itemName');
    }

    private function getPrependOptionLabel(): string
    {
        return $this->fusionValue('prependOptionLabel') ?? '';
    }

    private function getPrependOptionValue(): string
    {
        return $this->fusionValue('prependOptionValue') ?? '';
    }

    private function renderItems(iterable $items): array
    {
        $itemName = $this->getItemName();
        $itemRenderPath = $this->path . '/itemRenderer';

        $result = [];
        if ($this->runtime->canRender($itemRenderPath) === true) {
            foreach ($items as $item) {
                $context = $this->runtime->getCurrentContext();
                $context[$itemName] = $item;

                $this->runtime->pushContextArray($context);

                $result[] = $this->runtime->render($itemRenderPath);

                $this->runtime->popContext();
            }
        }
        return $result;
    }
}
