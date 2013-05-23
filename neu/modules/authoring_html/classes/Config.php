<?php

/**
 * Overrides methods of the HTMLPurifier library. This way, custom filters class
 * names can be inserted freely as text in the filter configuration form. A
 * patch for the htmlpurifier.module uses this class instead of the default
 * HTMLPurifier_Config.
 *
 * @see htmlpurifier.module
 */
class AuthoringHtml_Config extends HTMLPurifier_Config
{
  /**
   * Convenience constructor that creates a default configuration object. We
   * need to override this method to return our class instead of the
   * HTMLPurifier one.
   *
   * @return Default AuthoringHtml_Config object.
   * @see HTMLPurifier_Config::createDefault()
   */
  public static function createDefault() {
    $definition = HTMLPurifier_ConfigSchema::instance();
    $config = new AuthoringHtml_Config($definition);
    return $config;
  }

  /**
   * Retrieves all directives, organized by namespace. It forces the array
   * recreation, with squash(TRUE).
   *
   * @see HTMLPurifier_Config::getAll()
   */
  public function getAll() {
    if (!$this->finalized) $this->autoFinalize();
    $ret = array();
    foreach ($this->plist->squash(TRUE) as $name => $value) {
        list($ns, $key) = explode('.', $name, 2);
        $ret[$ns][$key] = $value;
    }
    return $ret;
  }
}
