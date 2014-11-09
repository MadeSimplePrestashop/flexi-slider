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
$sql[] = 'DROP TABLE `' . _DB_PREFIX_ . 'flexislider`';
$sql[] = 'DROP TABLE `' . _DB_PREFIX_ . 'flexislider_shop`';
$sql[] = 'DROP TABLE `' . _DB_PREFIX_ . 'flexislider_slides`';
$sql[] = 'DROP TABLE `' . _DB_PREFIX_ . 'flexislider_slides_lang`';
$sql[] = 'DROP TABLE `' . _DB_PREFIX_ . 'flexislider_slides_shop`';

foreach ($sql as $s) {
    if (!Db::getInstance()->Execute($s)) {
        return false;
    }
}
?>