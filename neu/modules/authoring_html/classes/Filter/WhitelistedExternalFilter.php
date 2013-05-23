<?php

/**
 * Provides a filter for element that loads external contents only if the
 * host is whitelisted.
 */
abstract class AuthoringHtml_Filter_WhitelistedExternalFilter extends HTMLPurifier_Filter
{
  protected $config;
  protected $context;

  /**
   * The element that the filter filters.
   *
   * @return
   *   The element name.
   */
  abstract public function getElement();

  /**
   * If the element is an empty element.
   *
   * @return
   *   Boolean.
   */
  public function isElementEmpty() {
    return FALSE;
  }

  /**
   * Returns the allowed attributes for the element being filtered.
   *
   * @return
   *   An array of allowed attributes.
   */
  abstract public function getAttributes();

  /**
   * Returns an associative array of attribute and default values.
   *
   * @return
   *   An associative array of attribute and default values. If an attribute is
   *   not available in the sanitizeAttributes() method, it will get the
   *   definition from this array.
   */
  public function getDefaultAttributes() {
    return array();
  }

  /**
   * The configuration key for the host whitelist for the current filter.
   *
   * @return
   *   A string key.
   */
  abstract function getHostWhitelistKey();

  /**
   * A whitelist of hosts allowed to load external resources.
   *
   * @return
   *   An array of whitelisted hosts.
   */
  public function getHostWhitelist() {
    return array();
  }

  /**
   * Returns a class name using the return from getHostWhitelistKey method
   *
   * @return
   *   A valid CSS class name.
   */
  public function getClassName() {
    return preg_replace('/[^0-9A-Za-z]/', '', $this->getHostWhitelistKey());
  }

  /**
   * All the trick is to replace the original tag with the img tag and then
   * post filter it.
   *
   * @see HTMLPurifier_Filter::preFilter
   * @see http://htmlpurifier.org/phorum/read.php?3,4646,5038#msg-5038
   */
  public function preFilter($html, $config, $context) {
    $this->config = $config;
    $this->context = $context;

    $element = $this->getElement();
    $class = $this->getClassName();

    $html = preg_replace('/<\/'. $element .'>/', '', $html);
    $html = preg_replace('/<'. $element .'/', '<img class="'. $class .'" ', $html);

    return $html;
  }

  /**
   * @see HTMLPurifier_Filter::preFilter
   */
  public function postFilter($html, $config, $context) {
    $this->config = $config;
    $this->context = $context;

    $post_regex = '#<img class="'. $this->getClassName() .'" ([^>]+)/>#';

    return preg_replace_callback($post_regex, array($this, 'postFilterCallback'), $html);
  }

  /**
   * Replaces the temporary placed img element with the filtered element HTML.
   * If the refered host is not in the whitelisted hosts, returns an empty
   * string.
   *
   * @param $matches
   *   Matches from a RegExp.
   *
   * @param $matchIndex
   *   The expected index in $matches that contains the HTML to populate the
   *   element HTML.
   *
   * @return
   *   The element HTML. If any.
   */
  public function postFilterCallback($matches, $matchIndex = 1) {
    $hostWhitelist = @$this->config->get($this->getHostWhitelistKey());

    if (NULL === $hostWhitelist) {
      $hostWhitelist = $this->getHostWhitelist();

      if (0 === count($hostWhitelist)) {
        return '';
      }
    }

    if (!isset($matches[$matchIndex])) {
      return '';
    }

    // Check the str attribute using DOM.
    $dom = new DOMDocument();
    $html = $this->getElementHTML($matches[$matchIndex]);
    $dom->loadHTML($html);

    if (FALSE !== ($data = $this->getElementDataIfHostIsWhitelisted($hostWhitelist, $dom))) {
      return $this->sanitizeAttributes($data['attributes']);
    }

    return '';
  }

  /**
   * Returns an associative array with the element data only if the
   * $hostAttribute is a whitelisted host.
   *
   * @param $whitelistedHosts
   *   An array of whitelisted hosts.
   *
   * @param $dom
   *   A DOMDocument object with the HTML to be evaluated.
   *
   * @param $hostAttribute
   *   The attribute that holds the host to be evaluated with the whitelist.
   *
   * @return
   *   An associative array with the $hostAttribute URL and host extracted,
   *   the whitelisted host matched and a DOMNamedNodeMap of the element's
   *   attributes.
   *
   *   Array keys: url, host, whitelistedHost, attributes.
   */
  protected function getElementDataIfHostIsWhitelisted(array $whitelistedHosts, DOMDocument $dom, $hostAttribute = 'src') {
    $nodeList = $dom->getElementsByTagName($this->getElement());

    if (0 === $nodeList->length) {
      return FALSE;
    }

    // URL and host for the "src" attribute.
    $url = '';
    $host = '';

    // We expect only one node
    $attributes = $nodeList->item(0)->attributes;

    foreach ($attributes as $name => $attr) {
      if ($hostAttribute === $name) {
        $url = $attr->value;
        $host = parse_url($attr->value, PHP_URL_HOST);
        break;
      }
    }

    // Returns the script tag only if src is in the whitelisted hosts.
    foreach ($whitelistedHosts as $whitelistedHost) {
      $host = strtolower($host);
      $whitelistedHost = strtolower($whitelistedHost);

      if ($whitelistedHost === $host || $whitelistedHost === substr($host, strrpos($host, '.'.$whitelistedHost) + 1)) {
        return array(
          'url'             => $url,
          'host'            => $host,
          'whitelistedHost' => $whitelistedHost,
          'attributes'      => $attributes,
        );
      }
    }

    return FALSE;
  }

  /**
   * Returns the HTML of the element with only the allowed attributes.
   *
   * @param $attributes
   *   A DOMNamedNodeMap object of the element attributes
   *
   * @return
   *   The element with the sanitized attributes
   */
  protected function sanitizeAttributes($attributes) {
    $html = '<%s %s></%s>';

    $attributesHtml = array();
    foreach ($attributes as $attribute) {
      if ($this->isAttributeAllowed($attribute)) {
        $attributesHtml[$attribute->name] = sprintf('%s="%s"', $attribute->name, $attribute->value);
      }
    }
    $this->mergeDefaultsIfNotPresent($attributesHtml);

    return $this->getElementHTML(implode(' ', $attributesHtml));
  }

  /**
   * Merge the defaults attributes if not present in the passed $attributes array.
   *
   * @param &$attributes
   *   An associative array with the attribute name as key
   */
  protected function mergeDefaultsIfNotPresent(&$attributes) {
    $availableAttributes = array_keys($attributes);

    foreach ($this->getDefaultAttributes() as $attributeName => $defaultValue) {
      if (!in_array($attributeName, $availableAttributes)) {
        $attributes[$attributeName] = sprintf('%s="%s"', $attributeName, $defaultValue);
      }
    }
  }

  /**
   * Returns the right HTML for the element, with the contained attributes.
   *
   * @param $attributes
   *   The HTML of the element's attributes
   *
   * @return
   *   The HTML for the element
   */
  private function getElementHTML($attributes = '') {
    $element = $this->getElement();

    if ('' !== $attributes) {
      $attributes = ' ' . $attributes;
    }

    if ($this->isElementEmpty()) {
      return sprintf('<%s%s />', $element, $attributes);
    }

    return sprintf('<%s%s></%s>', $element, $attributes, $element);
  }

  /**
   * Checks if an attribute is allowed for the current filter.
   *
   * @param $attribute
   *   A DOMAttr object
   *
   * @return
   *   Boolean
   */
  private function isAttributeAllowed($attribute) {
    if (!in_array($attribute->name, $this->getAttributes())) {
      return FALSE;
    }

    return TRUE;
  }
}
