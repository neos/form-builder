<?php
namespace Neos\Form\Builder\Fusion;

use Neos\Fusion\FusionObjects\AbstractArrayFusionObject;
use Neos\Utility\ObjectAccess;

class SelectOptionCollectionImplementation extends AbstractArrayFusionObject
{

    protected $ignoreProperties = [
        'prependOptionLabel',
        'prependOptionValue',
        'labelPropertyPath',
        'valuePropertyPath'
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
            foreach ($items as $item) {
                $value = ObjectAccess::getPropertyPath($item, $this->getValuePropertyPath());
                $label = ObjectAccess::getPropertyPath($item, $this->getLabelPropertyPath());
                if (strlen($label) === 0) {
                    $label = $value;
                }
                $options[$value] = $label;
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

    private function getValuePropertyPath(): string
    {
        return $this->fusionValue('valuePropertyPath');
    }

    private function getLabelPropertyPath(): string
    {
        return $this->fusionValue('labelPropertyPath');
    }

    private function getPrependOptionLabel(): string
    {
        return $this->fusionValue('prependOptionLabel') ?? '';
    }

    private function getPrependOptionValue(): string
    {
        return $this->fusionValue('prependOptionValue') ?? '';
    }
}
