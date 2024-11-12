<?php
namespace Neos\Form\Builder\NodeType;

use Neos\ContentRepository\Core\NodeType\NodeTypePostprocessorInterface;
use Neos\ContentRepository\Core\NodeType\NodeType;
use Neos\Flow\Annotations as Flow;

class FormNodeTypePostprocessor implements NodeTypePostprocessorInterface
{

    /**
     * @Flow\InjectConfiguration(package="Neos.Form", path="presets")
     * @var array
     */
    protected $formPresetSettings;

    public function process(NodeType $nodeType, array &$configuration, array $options)
    {
        $presetOptions = [];
        foreach ($this->formPresetSettings as $presetName => $presetSettings) {
            if (isset($options['skipPresets'][$presetName]) && $options['skipPresets'][$presetName] === true) {
                continue;
            }
            $presetOptions[] = [
                'value' => $presetName,
                'label' => isset($presetSettings['title']) ? sprintf('%s [%s]', $presetSettings['title'], $presetName) : $presetName
            ];
        }
        $configuration['properties']['preset']['ui']['inspector']['editorOptions']['values'] = $presetOptions;

        // The following line is a preparation for the "new Neos UI"
        // $configuration['ui']['creationDialog']['elements']['preset']['ui']['editorOptions']['values'] = $presetOptions;
    }
}
