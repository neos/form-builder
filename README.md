# Flow Form Framework integration into Neos CMS

This package adds a builder for the [Flow Form Framework](https://github.com/neos/form)
to the [Neos CMS](https://neos.io) backend.
It also comes with [Fusion](https://neos.readthedocs.io/en/stable/CreatingASite/Fusion/index.html)
prototypes that allow for dynamic Fusion based Form definitions.

## Usage

Install this package using the [Composer Dependency Manager](https://getcomposer.org/):

```
composer require wwwision/neos-form
```

**Note:** This package requires the `neos/neos` package in version 3.1 or higher

In the Neos backend there's now a new Content Element type that can be
used:

![Create Wizard](Documentation/Images/CreateWizard.png "New \"Form\" Content Element")

Now, *Form Elements* can be added to the Form:

![Add Form Element](Documentation/Images/AddFormElements.png "Adding Form Elements")

To each Form Element *Validators* can be added and some elements allow to create child Form Elements or
*Select Options*.
Besides, every form allows to create *Further Form Pages* that can contain elements themselves.
And, of course, *Form Finishers* can be added to the Form.

So there are quite a lot of Content Collections and they are easily confused.
One solution is to use the *Structure Tree* when working on complex forms:

![Structure Tree](Documentation/Images/StructureTree.png "Form in the Structure Tree")

In addition this package comes with some custom StyleSheet that should make
the Form Builder more accessible:

## Adjust appearance of the Form Builder

This package provides some CSS that can be included in order to adjust the
styling of the Form Builder within the Neos Backend.
Considering the `Neos.Neos:Page` Fusion object is defined as `page`, the
following Fusion snippet can be added in order to include the custom CSS
when in the Neos Backend:

```fusion
page.head.formBuilderStyles = Neos.Fusion:Tag {
    tagName = 'link'
    attributes {
        rel = 'stylesheet'
        href = Neos.Fusion:ResourceUri {
            path = 'resource://Wwwision.Neos.Form/Public/Styles/Backend.css'
        }
    }
    @position = 'end'
    @if.isInBackend = ${documentNode.context.inBackend}
}
```

(Note: There's also a version for the "new" Neos UI, but it's not yet supported)

As a result the form will look something like this in the Backend:

![Custom Styles](Documentation/Images/CustomStyles.png "Form Builder with custom styles")