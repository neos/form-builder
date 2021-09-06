import { connect } from "react-redux";
import { SelectBox } from "@neos-project/react-ui-components";
import React, { PureComponent } from "react";
import { neos } from "@neos-project/neos-ui-decorators";
import { $transform } from "plow-js";
import { selectors } from "@neos-project/neos-ui-redux-store";

export const parentNodeContextPath = contextPath => {
  if (typeof contextPath !== "string") {
    console.error("`contextPath` must be a string!"); // tslint:disable-line
    return null;
  }
  const [path, context] = contextPath.split("@");

  if (path.length === 0) {
    // We are at top level; so there is no parent anymore!
    return null;
  }

  return `${path.substr(0, path.lastIndexOf("/"))}@${context}`;
};

@connect(
  $transform({
    nodesByContextPath: selectors.CR.Nodes.nodesByContextPathSelector,
    focusedNode: selectors.CR.Nodes.focusedSelector
  })
)
@neos(globalRegistry => ({
  i18nRegistry: globalRegistry.get("i18n"),
  nodeTypeRegistry: globalRegistry.get(
    "@neos-project/neos-ui-contentrepository"
  )
}))
export default class PlaceholderInsertDropdown extends PureComponent {
  handleOnSelect = value => {
    this.props.executeCommand("placeholderInsert", value);
  };

  render() {
    const [formPath, workspace] = parentNodeContextPath(
      parentNodeContextPath(this.props.focusedNode.contextPath)
    ).split("@");

    const elementsPath = `${formPath}/elements@${workspace}`;

    const elementsNode = this.props.nodesByContextPath[elementsPath];
    if (!elementsNode) {
      return null;
    }
    const options = this.getOptionsRecursively(elementsNode.children);

    if (options.length === 0) {
      return null;
    }

    const placeholderLabel = this.props.i18nRegistry.translate(
      "Neos.Form.Builder:Main:placeholder",
      "Insert placeholder"
    );

    return (
      <SelectBox
        placeholder={placeholderLabel}
        options={options}
        onValueChange={this.handleOnSelect}
        value={null}
      />
    );
  }

  getOptionsRecursively(elements) {
    const returnValues = [];
    const excludeSettings = this.props.inlineEditorOptions.excludeNodeTypes;

    elements.forEach((childNode) => {
      const currentNode = this.props.nodesByContextPath[childNode.contextPath];
      const childChildNodes = this.props.nodesByContextPath[childNode.contextPath].children;
      let skipMode = 'includeAll';

      if (excludeSettings) {
        if (excludeSettings.hasOwnProperty(childNode.nodeType)) {
          const nodeTypeSettings = excludeSettings[childNode.nodeType];

          if (typeof nodeTypeSettings === 'boolean') {
            if (nodeTypeSettings) {
              // exclude all
              return;
            }
          }
          else if (nodeTypeSettings.hasOwnProperty('exclude') || nodeTypeSettings.hasOwnProperty('excludeChildren')) {
            if (nodeTypeSettings.exclude && nodeTypeSettings.excludeChildren) {
              // exclude all
              return;
            }
            else if (nodeTypeSettings.exclude && !nodeTypeSettings.excludeChildren) {
              // exclude only current element, not children
              skipMode = 'includeChildren'
            }
            else if (!nodeTypeSettings.exclude && nodeTypeSettings.excludeChildren) {
              // exclude only children
              skipMode = 'includeParent'
            }
          }
        }
      }

      if (skipMode === 'includeAll' || skipMode === 'includeParent') {
        returnValues.push({
          value: currentNode.properties.identifier || currentNode.identifier,
          label:
              currentNode.properties.label || currentNode.properties.identifier || currentNode.identifier
        });
      }

      if (skipMode === 'includeAll' || skipMode === 'includeChildren') {
        const childOptions = this.getOptionsRecursively(childChildNodes);

        if (Array.isArray(childOptions)) {
          childOptions.forEach(childOption => {
            returnValues.push(childOption);
          });
        }
      }
    });

    return returnValues;
  }
}
