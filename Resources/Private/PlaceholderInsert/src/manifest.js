import manifest from "@neos-project/neos-ui-extensibility";
import PlaceholderInsertDropdown from "./PlaceholderInsertDropdown";
import placeholderInsertPlugin from "./placeholderInsertPlugin";

const addPlugin = (Plugin, isEnabled) => (ckEditorConfiguration, options) => {
  if (!isEnabled || isEnabled(options.editorOptions, options)) {
    ckEditorConfiguration.plugins = ckEditorConfiguration.plugins || [];
    return {
      ...ckEditorConfiguration,
      plugins: [
        ...(ckEditorConfiguration.plugins ?? []),
        Plugin
      ]
    };
  }
  return ckEditorConfiguration;
};

manifest("Neos.Form.Builder:PlaceholderInsert", {}, globalRegistry => {
  const richtextToolbar = globalRegistry
    .get("ckEditor5")
    .get("richtextToolbar");
  richtextToolbar.set("placeholderInsertt", {
    component: PlaceholderInsertDropdown,
    isVisible: editorOptions => editorOptions?.formatting?.placeholderInsert
  });

  const config = globalRegistry.get("ckEditor5").get("config");
  config.set(
    "placeholderInsert",
    addPlugin(placeholderInsertPlugin, editorOptions => editorOptions?.formatting?.placeholderInsert)
  );
});
