# UI Tweaks

This is an example custom module to showcase some common tweaks to stock Drupal behavior.

When creating a new Drupal project, this module can be enabled as is, changed or cloned to fulfill any kind of similar purposes - mind the namespaces!

Most of the boilerplate code can be created via `drush generate`.

## Declaring a custom module

_See `.info.yml`_

## Implementing `hook_form_alter()`

_See `.module` file_

A fairly common use case when building a Drupal site is to change the way certain forms are rendered. This can be easily accomplished by implementing [hook_form_alter](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Form%21form.api.php/function/hook_form_alter/9.3.x) or one of its more specific variations.

This module ships with a change to the User form enabled by default. It consists of wrapping a form element (_User name and password_) in a `details` element, which is closed by default in the _Edit_ form but is open and marked as required in the _Register_ form. This alteration is conditional on a configuration setting which can be managed by any user with sufficient permissions.

## Implementing custom configuration with defaults

_See `.schema.yml` in `config/schema`_
_See `.settings.yml` in `config/install`_

This module defines a custom configuration key, therefore it requires a custom schema to be declared. It also ships with a default value for that configuration, loaded into the database upon installation.

## Implementing a custom form on a custom route

_See `.routing.yml` file_
_See `SettingsForm.php` in `src/Form`_

This module implements a custom route to provide a Settings form where the conditional configuration can be managed. The route definition depends on a particular permission which already exists in core. Custom permissions can also be declared in a `.permissions.yml` file and then leveraged in route definitions.

## Implementing custom menu links

_See `.links.menu.yml` file_
_See `.routing.yml` file_

For easy access to the Settings form, this module declares admin menu links based on the custom routes. Menu link definitions are very simple to create, although finding the definitions of potential parent menu items usually requires much more digging into core code.

For illustration purposes, the Settings form link is nested under another custom menu link. The use cases for creating parent menu items are common, but defining a route that just lists menu children is not so obvious. As such, this module ships with one such parent menu item with its own custom route definition, using a core provided controller that yields the desired effect.

## Testing

_See `SettingsFormTest.php` in `tests/src/Functional`_
_See `UserFormTest.php` in `tests/src/Functional`_

This module includes a couple of Functional tests that cover both the Settings form and the altered User forms. There are Browser tests that allow to inspect the actual browser output, which is key to validate the `hook_form_alter` implementation.

Browser tests are especially heavy to run, but do allow to verify the desired effects of any module. Check the documentation at the top level of the EUF base installation profile for more information on how to run tests.
