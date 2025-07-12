INTRODUCTION
------------
This module extends the functionality that the http://drupal.org/project/samlauth module provides.

INSTALLATION
------------
Install as you would normally install a contributed drupal module. See:
https://www.drupal.org/documentation/install/modules-themes/modules-8
for further information.

REQUIREMENTS
------------
This module depends on the samlauth Drupal module
https://drupal.org/project/samlauth. This is automatically installed if you
installed the module using Composer.

CONFIGURATION AND TESTING
-------------------------

Start at the "User Interface" part of /admin/config/people/saml/idp;

A default IdP will be created for you. If you previously used the samlauth module,
the default IdP will inherit that IdPs initial values.

Add or edit additional IdPs via the "User Interface".

The original /saml/acs and /saml/login links remain unchanged, and work for all of
the configured IdPs.

## Login links

Each IdP is capable of generating it's own login link.
If enabled and configured for any given IdP, the link will
appear on the default /user/login route for users to access.

A new route /saml/login/idp will be available, which will also
list any configured and enabled IdP links. If multiple IdPs are
configured and their links enabled, the default 'Login Link' will
be replaced with this new route.

## Manual links

To manually link to individual IdP login links, append ?saml_idp=machine_name
to the default /saml/login route (e.g. /saml/login?saml_idp=default)

The default IdP is used as a fallback for any misconfigured or incorrect IdP
links.
