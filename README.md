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