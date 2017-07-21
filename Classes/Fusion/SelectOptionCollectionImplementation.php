<?php
namespace Wwwision\Neos\Form\Fusion;

use Neos\Fusion\FusionObjects\AbstractArrayFusionObject;
use Neos\Utility\ObjectAccess;

class SelectOptionCollectionImplementation extends AbstractArrayFusionObject
{

    protected $ignoreProperties = ['keyPropertyPath', 'valuePropertyPath'];

    public function evaluate()
    {
        $collection = $this->getCollection();
        $options = [];
        if ($collection === null) {
            foreach ($this->properties as $propertyName => $propertyValue) {
                if (in_array($propertyName, $this->ignoreProperties)) {
                    continue;
                }
                $options[$propertyName] = $propertyValue;
            }
        } else {
            foreach ($collection as $item) {
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
    private function getCollection()
    {
        return $this->fusionValue('collection');
    }

    private function getValuePropertyPath(): string
    {
        return $this->fusionValue('valuePropertyPath');
    }

    private function getLabelPropertyPath(): string
    {
        return $this->fusionValue('labelPropertyPath');
    }
}