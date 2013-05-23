README
==

Introduction
--

This modules creates an input format suitable for WYSIWYG editors usage. It
adds support for the `iframe` HTML tag, making it friendly with the popular
iframe embeds available in popular video sites like YouTube and Vimeo.

It depends on the HTMLPurifier and Autoload modules. Quick installation
instructions can be found on the INSTALL.txt file. The text bellow describe
why is necessary to patch the HTMLPurifier module and why is necessary to
regenerate the HTMLPurifier configuration schema file.

This module is sponsored by Infranology (http://infranology.com) and is used
in the Drüpen distribution (http://drupen.org).


Better integration with the HTMLPurifier library
--

By default, the HTMLPurifier module does not have an easy way to enable custom
filters. The filter's settings form have the textfield for this but since the
underlying code never creates the objects, the execution of the filter stops,
since it trie to call a method using a string value (see the `HTMLPurifier`
class, method `purify()`, line 167 in the 4.2.0 version of the library).

The patch `htmlpurifier.patch` available in the `patches/` directory changes
the `htmlpurifier.module` to use the configuration class provided by the
Authoring HTML module. It overrides some methods of the `HTMLPurifier_Config`
class and the `htmlpurifier.module` use it to create the named filters classes
available in the Filter Custom textfield.

Install the module and patch the HTMLPurifier module (compatible with the
6.x-2.4 release):

    $ cd /path/to/modules/htmlpurifier
    $ patch -p0 --verbose < /path/to/modules/authoring_html/patches/htmlpurifier.patch

This is a MANDATORY step. Without it, the filters for the `iframe` and `script`
tags will not be loaded and it will not possible to customize the HTMLPurifier
library at `admin/settings/filters/INPUT_FORMAT_ID/configure`.


HTMLPurifier iframe and script hosts' whitelist configuration
--

The HTMLPurifier library uses a configuration schema that does not allow adding
more new options if the desired directive is not defined previously. This
module ships with two configuration directives:

 - `AuthoringHtml.Iframe.HostWhitelist`
 - `AuthoringHtml.Script.HostWhitelist`

To edit the values of these directives in the admin section, you will need to
regenerate the serialized config schema of HTMLPurifier:

    $ php /path/to/libraries/htmlpurifier/maintenance/generate-schema-cache.php \
    /path/to/modules/authoring_html/HTMLPurifier/schema

Both are whitelists were external resources can be downloaded. The first have
default values defined in the class `AuthoringHtml_Filter_Iframe` while the
second does not have default hosts.

This is an OPTIONAL step. Without it you will not be able to update the
whitelist of hosts that the `iframe` and `script` tags will can refer.

NOTE: Don't add hosts to `AuthoringHtml.Script.HostWhitelist` if the input
format will be used by untrusted users.
