# Themed Forms for Laminas (with optional Tailwind Goodies)

I built this rig to create a standardized "theme" approach to skinning Laminas forms.  In a large team, people can 
often do things differently, rolling their own CSS.  This puts a stop to this.

Design goals were:

* make the system as transparent as possible (no thick overrides) so that it survives Laminas/Form changes.
* allow you to define the look of a form with a single parameter (theme)
* enforce that themes are created only at the config level, in PHP
* don't interfere when classes are explicitly set, in other words, get out of the way if need be
* smallest footprint possible

# Installation

Coming Soon.

# Usage

Coming Soon.

#### Implementation Notes

* Delegators weren't an option for the forms, since the form names that are seen by FormElementManager::doCreate have varied 'resolvedName' values.  Therefore,
a delegator was instead used to "steal" FormElementManager out from under the ServiceManager.  You will see that the ThemedFormDelegatorFactory does not use the callback provided by the service manager, instead, it returns something of a proxy object. In
  tests, there is no perceptible or deterministic performance impact.
* Themes are pushed into the Form Elements as they are added to the Forms provided by the library.  This was necessary, since there is no
link from the Element to the parent.  It would have been more efficient to leave the theme data in the Form and have the helpers rely on that, but
it is not possible without exhaustive modification to laminas/laminas-forms.  