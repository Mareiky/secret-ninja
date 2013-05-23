<?php

/**
 * Provides a filter for the script element.
 */
class AuthoringHtml_Filter_Script extends AuthoringHtml_Filter_WhitelistedExternalFilter
{
  public $name = 'Script';

  /**
   * @see AuthoringHtml_Filter_WhitelistedExternalFilter::getAttributes()
   */
  public function getElement() {
    return 'script';
  }

  /**
   * The supported attributes for this element. Based on the XHTML 1.0
   * Transitional DTD.
   *
   * @see AuthoringHtml_Filter_WhitelistedExternalFilter::getAttributes()
   * @see http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd
   */
  public function getAttributes() {
    return array(
      'id',
      'charset',
      'type',
      'language',
      'src',
      'defer',
    );
  }

  /**
   * @see AuthoringHtml_Filter_WhitelistedExternalFilter::getHostWhitelistKey()
   */
  public function getHostWhitelistKey() {
    return 'AuthoringHtml.Script.HostWhitelist';
  }
}
