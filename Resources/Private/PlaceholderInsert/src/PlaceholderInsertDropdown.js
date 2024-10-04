import { connect } from "react-redux";
import { SelectBox } from "@neos-project/react-ui-components";
import React, { PureComponent } from "react";
import { neos } from "@neos-project/neos-ui-decorators";
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
  state => ({
    nodesByContextPath: selectors.CR.Nodes.nodesByContextPathSelector(state),
    focusedNode: selectors.CR.Nodes.focusedSelector(state)
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
    const options = elementsNode.children
      .map(node => this.props.nodesByContextPath[node.contextPath])
      .map(node => ({
        value: node.properties.identifier || node.identifier,
        label:
          node.properties.label || node.properties.identifier || node.identifier
      }));

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
}
