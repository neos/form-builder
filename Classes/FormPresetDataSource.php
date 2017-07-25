<?php
namespace Wwwision\Neos\Form;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\Service\DataSource\DataSourceInterface;
use Neos\Utility\Arrays;

final class FormPresetDataSource implements DataSourceInterface
{
    /**
     * @Flow\InjectConfiguration(package="Neos.Form", path="presets")
     * @var array
     */
    protected $formPresetSettings;

    public static function getIdentifier(): string
    {
        return 'wwwision-neos-form-presets';
    }

    public function getData(NodeInterface $node = null, array $arguments): array
    {
        $skipPresets = isset($arguments['skipPresets']) ? Arrays::trimExplode(',', $arguments['skipPresets']) : [];
        $options = [];
        foreach ($this->formPresetSettings as $presetName => $presetSettings) {
            if (in_array($presetName, $skipPresets)) {
                continue;
            }
            $presetTitle = isset($presetSettings['title']) ? sprintf('%s [%s]', $presetSettings['title'], $presetName) : $presetName;
            $options[] = ['value' => $presetName, 'label' => $presetTitle];
        }
        return $options;

    }
}