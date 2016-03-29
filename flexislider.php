<?php
/**
 * Module Flexi Slider 
 * 
 * @author 	kuzmany.biz
 * @copyright 	kuzmany.biz/prestashop
 * @license 	kuzmany.biz/prestashop
 * Reminder: You own a single production license. It would only be installed on one online store (or multistore)
 */
if (!defined('_PS_VERSION_'))
    exit;

require_once(dirname(__FILE__) . '/models/FlexiSliders.php');
require_once(dirname(__FILE__) . '/models/FlexiSlides.php');

class flexislider extends Module
{

    public $hooks = array('displayFooter');

    public function __construct()
    {
        $this->name = 'flexislider';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'kuzmany.biz/prestashop';
        $this->need_instance = 0;
        $this->module_key = 'b7165ebb44418e5cdc0d99e1079914a7';

        parent::__construct();

        $this->displayName = $this->l('Flexi Slider');
        $this->description = $this->l('Responsible and mobile friendly slider with many options and few cute effects.');
    }

    public function install()
    {

        if (!parent::install() || !$this->registerHook('displayHeader') || !$this->registerHook('displayBackOfficeHeader'))
            return false;

        foreach ($this->hooks as $hook)
            $this->registerHook($hook);

        include_once(dirname(__FILE__) . '/init/install_sql.php');

        //tabs
        $this->context->controller->getLanguages();
        $lang_array = array();
        $id_parent = 0;
        foreach ($this->context->controller->_languages as $language) {
            $lang_array[(int) $language['id_lang']] = $this->displayName;
        }
        $tab = $this->installAdminTab($lang_array, 'AdminFexiSliders', $id_parent);
        $id_parent = $tab->id;
        //slides
        $lang_array = array();
        foreach ($this->context->controller->_languages as $language) {
            $lang_array[(int) $language['id_lang']] = 'Sliders';
        }
        $this->installAdminTab($lang_array, 'AdminFlexiSliders', $id_parent);
        //slides
        $lang_array = array();
        foreach ($this->context->controller->_languages as $language) {
            $lang_array[(int) $language['id_lang']] = 'Slides';
        }
        $this->installAdminTab($lang_array, 'AdminFlexiSlides', $id_parent);

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !$this->unregisterHook('displayHeader') || !$this->unregisterHook('displayBackOfficeHeader')
        )
            return false;

        foreach ($this->hooks as $hook)
            $this->unregisterHook($hook);

        include_once(dirname(__FILE__) . '/init/uninstall_sql.php');

        $this->uninstallAdminTab('AdminFlexiSliders');
        $this->uninstallAdminTab('AdminFlexiSlides');

        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin('index.php?controller=AdminFlexiSliders&token=' . Tools::getAdminTokenLite('AdminFlexiSliders'));
    }

    private function installAdminTab($name, $className, $parent)
    {
        $tab = new Tab();
        $tab->name = $name;
        $tab->class_name = $className;
        $tab->id_parent = $parent;
        $tab->module = $this->name;
        $tab->add();
        return $tab;
    }

    private function uninstallAdminTab($className)
    {
        $tab = new Tab((int) Tab::getIdFromClassName($className));
        $tab->delete();
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        if (in_array(Dispatcher::getInstance()->getController(), array('AdminFlexiSliders', 'AdminFlexiSlides'))) {
            $this->context->controller->addJS($this->_path . '/views/js/admin.js');
            $this->context->controller->addCSS($this->_path . '/views/css/admin.css');
        }
    }

    public function hookHeader($params)
    {
        $this->context->controller->addCSS($this->getPathUri() . 'views/css/flexslider.css');
        $this->context->controller->addJS($this->getPathUri() . 'views/js/moodular.js');
        if (!isset($this->context->smarty->registered_plugins['function'][$this->name])) {
            $this->context->smarty->registerPlugin('function', $this->name, array('FlexiSliders', 'get_slider'));
        }
        if (!isset($this->context->smarty->registered_plugins['modifier']['truefalse'])) {
            $this->context->smarty->registerPlugin('modifier', 'truefalse', array('FlexiSliders', 'truefalse'));
        }

        if ($this->is_inspector()) {
            $this->context->controller->addJS($this->_path . '/views/js/inspector.js');
            $this->context->controller->addCSS($this->_path . '/views/css/inspector.css');
        }
    }

    private function is_inspector()
    {
        return Tools::getValue('fs_live_edit_token') && Tools::getValue('fs_live_edit_token') == FlexiSliders::getLiveEditToken() && Tools::getIsset('id_employee') ? true : false;
    }

    private function load_sliders()
    {
        $sliders = Cache::retrieve(__CLASS__ . __FUNCTION__);
        if (!$sliders) {
            $sliders = FlexiSliders::getAll(array('c.active' => 1));
            if ($sliders) {
                Cache::store(__CLASS__ . __FUNCTION__, $sliders);
            } else {
                Cache::store(__CLASS__ . __FUNCTION__, -1);
            }
        }
        if ($sliders == -1) {
            return;
        }
        $html = '';
        foreach ($sliders as $slider) {
            $options = Tools::jsonDecode($slider['options']);
            if (isset($options->element) && !empty($options->element)) {
                $html .= FlexiSliders::get_slider(array('id' => $slider[FlexiSliders::$definition['primary']]));
            }
        }

        return $html;
    }

    public function hookDisplayFooter($params)
    {
        $html = '';
        if ($this->is_inspector()) {
            $html = $this->display(__FILE__, 'views/templates/hook/inspector.tpl');
        }
        return $html . $this->load_sliders();
    }
}
