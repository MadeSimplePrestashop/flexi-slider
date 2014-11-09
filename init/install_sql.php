<?php

/**
 * Module Flexi Slider 
 * 
 * @author 	kuzmany.biz
 * @copyright 	kuzmany.biz/prestashop
 * @license 	kuzmany.biz/prestashop
 * Reminder: You own a single production license. It would only be installed on one online store (or multistore)
 */
$sql = array();
$sql[] = '
CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flexislider` (
  `id_flexislider` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(50),
  `position` int(3) NOT NULL,
  `active` int(1) NOT NULL,
  `options` TEXT,
  PRIMARY KEY (`id_flexislider`)
) ENGINE = ' . _MYSQL_ENGINE_ . '  ';


$sql[] = ''
        . 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flexislider_shop` (
      `id_flexislider` int(10)  NOT NULL,
      `id_shop` int(3) unsigned NOT NULL,
      PRIMARY KEY (`id_flexislider`, `id_shop`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;'
        . '';


$sql[] = '
CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flexislider_slides` (
  `id_flexislider_slides` int(11) NOT NULL AUTO_INCREMENT,
  `id_flexislider` int(11) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `target` VARCHAR(10) NOT NULL,
  `options` TEXT NOT NULL,
  `position` int(3) NOT NULL,
  `active` int(1) NOT NULL,  
  PRIMARY KEY (`id_flexislider_slides`)
) ENGINE = ' . _MYSQL_ENGINE_ . '  ';

$sql[] = '
CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flexislider_slides_lang` (
  `id_flexislider_slides` int(11),
  `id_lang` int(3) NOT NULL,
  `caption` TEXT NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id_flexislider_slides`,id_lang)
) ENGINE = ' . _MYSQL_ENGINE_ . '  ';

$sql[] = ''
        . 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'flexislider_slides_shop` (
      `id_flexislider_slides` int(10)  NOT NULL,
      `id_shop` int(3) unsigned NOT NULL,
      PRIMARY KEY (`id_flexislider_slides`, `id_shop`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;'
        . '';

foreach ($sql as $s) {
    if (!Db::getInstance()->Execute($s)) {
        return false;
    }
}
?>