<?php

/**
 * @file
 *   HTMLPurifier filter configuration tasks.
 */

/**
 * Configure the HTML Purifier for the Authoring HTML input format.
 *
 * @param $authoring_html_format
 *   The input format id.
 *
 * @param $config
 *   The configuration array. Optional, uses the array from
 *   authoring_html_htmlpurifier_config() if not defined.
 *
 * @param $help
 *   If the help will be displayed for the users.
 */
function authoring_html_htmlpurifier_save_config($authoring_html_format, $config = NULL, $help = 1) {
  if (NULL === $config) {
    $config = authoring_html_htmlpurifier_config();
  }

  variable_set('htmlpurifier_config_' . $authoring_html_format, $config);
  variable_set('htmlpurifier_help_' . $authoring_html_format, $help);
}

/**
 * Returns the configuration array for the HTMLPurifier. Important:
 *
 * - Enables Attr.IDPrefix (anchor links). The default prefix is "c_".
 * - Configures custom filters that adds supports for the <iframe> and <script>
 *   tags. See Filter.Custom.
 * - Configures a whitelist of hosts that the filters must check. <script>
 *   does no ship with suggestions. See INSTALL.txt for more information.
 *
 * @return
 *   A configuration array for the HTMLPurifier filter.
 */
function authoring_html_htmlpurifier_config()  {
  $config = array(
    'Null_Attr.AllowedClasses' => '1',
    'Attr.AllowedFrameTargets' => '',
    'Attr.AllowedRel' => '',
    'Attr.AllowedRev' => '',
    'Null_Attr.ClassUseCDATA' => '1',
    'Null_Attr.DefaultImageAlt' => '1',
    'Attr.DefaultInvalidImage' => '',
    'Attr.DefaultInvalidImageAlt' => t('Invalid image'),
    'Attr.DefaultTextDir' => 'ltr',
    'Attr.EnableID' => '1',
    'Attr.ForbiddenClasses' => '',
    'Attr.IDBlacklist' => '',
    'Null_Attr.IDBlacklistRegexp' => '1',
    'Attr.IDPrefix' => 'c_',
    'Attr.IDPrefixLocal' => '',
    'AutoFormat.AutoParagraph' => '1',
    'AutoFormat.Custom' => '',
    'AutoFormat.DisplayLinkURI' => '0',
    'AutoFormat.Linkify' => '1',
    'AutoFormat.PurifierLinkify.DocURL' => '#%s',
    'AutoFormat.PurifierLinkify' => '0',
    'AutoFormat.RemoveEmpty.RemoveNbsp.Exceptions' => 'td
th',
    'AutoFormat.RemoveEmpty.RemoveNbsp' => '0',
    'AutoFormat.RemoveEmpty' => '0',
    'AutoFormat.RemoveSpansWithoutAttributes' => '0',
    'CSS.AllowImportant' => '0',
    'CSS.AllowTricky' => '0',
    'Null_CSS.AllowedProperties' => '1',
    'CSS.ForbiddenProperties' => '',
    'CSS.MaxImgLength' => '1200px',
    'CSS.Proprietary' => '0',
    'Cache.DefinitionImpl' => 'Drupal',
    'Null_Cache.SerializerPath' => '1',
    'Core.AggressivelyFixLt' => '1',
    'Core.CollectErrors' => '0',
    'Core.ColorKeywords' => 'maroon:#800000
red:#FF0000
orange:#FFA500
yellow:#FFFF00
olive:#808000
purple:#800080
fuchsia:#FF00FF
white:#FFFFFF
lime:#00FF00
green:#008000
navy:#000080
blue:#0000FF
aqua:#00FFFF
teal:#008080
black:#000000
silver:#C0C0C0
gray:#808080
  ',
    'Core.ConvertDocumentToFragment' => '1',
    'Core.DirectLexLineNumberSyncInterval' => '0',
    'Core.Encoding' => 'utf-8',
    'Core.EscapeInvalidChildren' => '0',
    'Core.EscapeInvalidTags' => '0',
    'Core.EscapeNonASCIICharacters' => '0',
    'Core.HiddenElements' => 'script
style',
    'Core.Language' => 'en',
    'Null_Core.LexerImpl' => '1',
    'Null_Core.MaintainLineNumbers' => '1',
    'Core.NormalizeNewlines' => '1',
    'Core.RemoveInvalidImg' => '1',
    'Core.RemoveProcessingInstructions' => '0',
    'Null_Core.RemoveScriptContents' => '1',
    'Filter.Custom' => 'AuthoringHtml_Filter_Iframe
AuthoringHtml_Filter_Script',
    'Filter.ExtractStyleBlocks.Escaping' => '1',
    'Null_Filter.ExtractStyleBlocks.Scope' => '1',
    'Null_Filter.ExtractStyleBlocks.TidyImpl' => '1',
    'Filter.ExtractStyleBlocks' => '0',
    'Filter.YouTube' => '0',
    'Null_HTML.Allowed' => '1',
    'Null_HTML.AllowedAttributes' => '1',
    'Null_HTML.AllowedElements' => '1',
    'Null_HTML.AllowedModules' => '1',
    'HTML.Attr.Name.UseCDATA' => '0',
    'HTML.BlockWrapper' => 'p',
    'HTML.CoreModules' => 'Structure
Text
Hypertext
List
NonXMLCommonAttributes
XMLCommonAttributes
CommonAttributes',
    'Null_HTML.CustomDoctype' => '1',
    'HTML.Doctype' => 'XHTML 1.0 Transitional',
    'HTML.FlashAllowFullScreen' => '0',
    'HTML.ForbiddenAttributes' => '',
    'HTML.ForbiddenElements' => '',
    'HTML.MaxImgLength' => '1200',
    'HTML.Parent' => 'div',
    'HTML.Proprietary' => '0',
    'HTML.SafeEmbed' => '1',
    'HTML.SafeObject' => '1',
    'HTML.Strict' => '0',
    'HTML.TidyAdd' => '',
    'HTML.TidyLevel' => 'medium',
    'HTML.TidyRemove' => '',
    'HTML.Trusted' => '0',
    'HTML.XHTML' => '1',
    'Output.CommentScriptContents' => '1',
    'Output.FlashCompat' => '1',
    'Null_Output.Newline' => '1',
    'Output.SortAttr' => '0',
    'Output.TidyFormat' => '0',
    'Test.ForceNoIconv' => '0',
    'URI.AllowedSchemes' => 'http
https
mailto
ftp
nntp
news',
    'Null_URI.Base' => '1',
    'URI.DefaultScheme' => 'http',
    'URI.Disable' => '0',
    'URI.DisableExternal' => '0',
    'URI.DisableExternalResources' => '0',
    'URI.DisableResources' => '0',
    'URI.Host' => 'localhost',
    'URI.HostBlacklist' => '',
    'URI.MakeAbsolute' => '0',
    'Null_URI.Munge' => '1',
    'URI.MungeResources' => '0',
    'Null_URI.MungeSecretKey' => '1',
    'URI.OverrideAllowedSchemes' => '1',
  );

  // These well known sites offer the iframe embed.
  $config['AuthoringHtml.Iframe.HostWhitelist'] = 'archive.org
blip.tv
brightcove.com
dailymotion.com
youtube.com
vimeo.com';

  // This is dangerous, even allowing high profile sites like GitHub's Gist
  // can open your site to malicious scripts. We don't ship with suggestions.
  $config['AuthoringHtml.Script.HostWhitelist'] = '';

  return $config;
}
