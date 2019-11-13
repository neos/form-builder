import { ModelText, Command, Plugin } from "ckeditor5-exports";

class PlaceholderInsertCommand extends Command {
  execute(value) {
    const model = this.editor.model;
    const doc = model.document;
    const selection = doc.selection;
    const placeholder = new ModelText("{" + value + "}");
    model.insertContent(placeholder, selection);
  }
}

export default class PlaceholderInsertPlugin extends Plugin {
  static get pluginName() {
    return "PlaceholderInsert";
  }
  init() {
    const editor = this.editor;

    editor.commands.add(
      "placeholderInsert",
      new PlaceholderInsertCommand(this.editor)
    );
  }
}
