# Themed Forms for Laminas (with optional Tailwind/Alpine Goodies)

I built this rig to create a standardized "theme" approach to skinning Laminas forms. In a large team, people can
often do things differently, rolling their own CSS. This puts a stop to this.

Design goals were:

* make the system as transparent as possible (no thick overrides) so that it survives Laminas/Form changes.
* allow you to define the look of a form with a single parameter (theme)
* enforce that themes are created only at the config level, in PHP
* don't interfere when classes are explicitly set, in other words, get out of the way if need be
* smallest footprint possible

Optional, since very opinionated:

* TODO: automate binding reactive models with AlpineJS
* TODO: automate writing XHR fetch scripts for forms, along with error display

# Installation

Coming Soon.

# Usage

Coming Soon.

## Tested Elements

You can see these behaviors in the unit tests. So far, I am happy with the support it offers:

* text fields
* buttons
* checkbox
* toggle (new)

## Defining Default Styles

Take a look at config/module.config.php and look at the `form_themes/default-form-element` key. Those are the class names that you will want to define in your app's Tailwind configuration (e.g., with @apply). You can also override them at the element level when you define your forms (within init()).

To get you started, here some that I use in a project that leverages this library:

```
  .default-form-element {
      @apply shadow focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md;
  }
  
  .default-form-element.error {
      @apply focus:ring-red-500 placeholder-red-300 focus:outline-none focus:ring-red-500 block w-full sm:text-sm border-red-300 rounded-md text-red-900;
  }
  
  .default-form-error {
      @apply mt-2 text-sm text-red-600;
  }
  
  .default-form-label {
      @apply block text-sm font-medium text-gray-700;
  }
  
  .default-form-button {
      @apply inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
  }
  
  .default-form-button-primary {
      @apply inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
  }
  
  .default-form-button {
      @apply bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500;
  }
  
  .default-form-help-block {
      @apply mt-2 text-sm text-gray-500;
  }
  
  .default-form-toggle {
      @apply self-start mt-2 bg-gray-200 relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2;
  }
  
  .default-form-toggle.active {
      @apply self-start mt-2 bg-indigo-600 relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2;
  }
  
  .default-form-toggle .toggle-control {
      @apply translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out;
  }
  
  .default-form-toggle.active .toggle-control {
      @apply translate-x-5 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out;
  }
  
  .default-radio-option {
      @apply h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500;
  }
   
  .default-radio-label {
      @apply ml-3 block text-sm font-medium text-gray-700;
  }
  
  .default-file-element {
      @apply block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-violet-100;
  }
```

#### Implementation Notes

* Delegators weren't an option for the forms, since the form names that are seen by FormElementManager::doCreate have varied 'resolvedName' values. Therefore,
  a delegator was instead used to "steal" FormElementManager out from under the ServiceManager. You will see that the ThemedFormDelegatorFactory does not use the callback provided by the service manager, instead, it returns something of a proxy object. In
  tests, there is no perceptible or deterministic performance impact.
* Themes are pushed into the Form Elements as they are added to the Forms provided by the library. This was necessary, since there is no
  link from the Element to the parent. It would have been more efficient to leave the theme data in the Form and have the helpers rely on that, but
  it is not possible without exhaustive modification to laminas/laminas-forms.  