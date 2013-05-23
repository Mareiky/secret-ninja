<?php

/**
 * Provides a filter for the iframe element.
 */
class AuthoringHtml_Filter_Iframe extends AuthoringHtml_Filter_WhitelistedExternalFilter
{
  public $name = 'Iframe';

  /**
   * @see AuthoringHtml_Filter_WhitelistedExternalFilter::getElement()
   */
  public function getElement() {
    return 'iframe';
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
      'class',
      'style',
      'title',
      'longdesc',
      'name',
      'src',
      'frameborder',
      'marginwidth',
      'marginheight',
      'scrolling',
      'align',
      'height',
      'width',
    );
  }

  /**
   * @see AuthoringHtml_Filter_WhitelistedExternalFilter::getHostWhitelistKey()
   */
  public function getHostWhitelistKey() {
    return 'AuthoringHtml.Iframe.HostWhitelist';
  }

  /**
   * We want at least these services. This way, if the customized serialized
   * configuration was not generated, we garantee that at least videos from these
   * sites can embeded.
   */
  public function getHostWhitelist() {
    return array(
      'archive.org',
      'blip.tv',
      'brightcove.com',
      'dailymotion.com',
      'youtube.com',
      'vimeo.com',
    );
  }
}
