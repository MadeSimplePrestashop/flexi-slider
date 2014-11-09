<?php

/**
 * Module Flexi Slider
 * 
 * @author 	kuzmany.biz
 * @copyright 	kuzmany.biz/prestashop
 * @license 	kuzmany.biz/prestashop
 * Reminder: You own a single production license. It would only be installed on one online store (or multistore)
 */
class FlexiSliders extends ObjectModel {

    public $id_flexislider;
    public $alias;
    public $position;
    public $active;
    public $options;

    public function __construct($id = null, $id_lang = null, $id_shop = null) {
        self::_init();
        parent::__construct($id, $id_lang, $id_shop);
    }

    private static function _init() {
        if (Shop::isFeatureActive())
            Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'flexislider',
        'primary' => 'id_flexislider',
        'fields' => array(
            'alias' => array('type' => self::TYPE_STRING, 'required' => true),
            'options' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'position' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_INT)
        )
    );

    public static function getAll($parms = array()) {
        self::_init();
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::$definition['table'], 'c');
        if (Shop::isFeatureActive())
            $sql->innerJoin(self::$definition['table'] . '_shop', 's', 'c.' . self::$definition['primary'] . ' = s.' . self::$definition['primary'] . ' AND s.id_shop = ' . (int) Context::getContext()->shop->id);
        if (empty($parms) == false)
            foreach ($parms as $k => $p)
                $sql->where('' . $k . ' =\'' . $p . '\'');
        $sql->orderby('position');
        return Db::getInstance()->executeS($sql);
    }

    public function update($null_values = false) {
        $this->alias = Tools::strtolower(str_replace(' ', '', Tools::replaceAccentedChars($this->alias)));
        $options = $this->transform_options();
        if ($options != false)
            $this->options = $options;
        parent::update($null_values);

//reload images
        $parms = array();
        $parms[self::$definition['primary']] = $this->id;
        $slides = FlexiSlides::getAll($parms);
        $source_path = FlexiSlides::get_image_path($this->id);
        if ($slides) {
            foreach ($slides as $slide) {
                ImageManager::thumbnail($source_path . $slide['image'], '/pager_' . $slide['image'], Tools::getValue('thumbnailWidth'), 'jpg', true, true);
            }
        }
    }

    public function add($autodate = true, $null_values = false) {

        $last_position = self::getLastPosition();
        $this->position = $last_position + 1;

        $options = $this->transform_options();
        if ($options != false)
            $this->options = $options;

        parent::add($autodate, $null_values);
    }

    public static function findIdByAlias($alias) {
        $sql = 'SELECT ' . self::$definition['primary'] . '
			FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`
			WHERE `alias` = \'' . (string) $alias . '\'';
        return (Db::getInstance()->getValue($sql));
    }

    public static function load_slider($id) {

// find children
        $parms = array();
        $parms[self::$definition['primary']] = $id;
        $parms['active'] = 1;
        $slides = FlexiSlides::getAll($parms);
        if (empty($slides))
            return;

        //echo ImageManagr::resize($source_path . $slide['image'], _PS_TMP_IMG_DIR_. '/web_' . $slide['image'],300,200);
        $slider = new FlexiSliders($id, null, Context::getContext()->shop->id);
        $slider->options = Tools::jsonDecode($slider->options);

        $view = array();
        if (Dispatcher::getInstance()->getController() != 'AdminFlexiSliders') {
            (isset($slider->options->categories) && empty($slider->options->categories) == false ? array_push($view, 'category') : '');
            (isset($slider->options->cms) && empty($slider->options->cms) == false ? array_push($view, 'cms') : '');
        }

        if (empty($view) == false) {
            if (!in_array(Dispatcher::getInstance()->getController(), $view))
                return;

            if (Dispatcher::getInstance()->getController() == 'category')
                if (in_array(Tools::getValue('id_category'), $slider->options->categories) == false)
                    return;

            if (Dispatcher::getInstance()->getController() == 'cms') {
                $categories = array();
                $cms = array();
                foreach ($slider->options->cms as $c) {
                    if (strpos($c, 'category_') !== false)
                        $categories[] = str_replace('category_', '', $c);
                    if (strpos($c, 'cms_') !== false)
                        $cms[] = str_replace('cms_', '', $c);
                }
                if (!in_array(Tools::getValue('id_cms'), $cms) && !in_array(Tools::getValue('id_cms_category'), $categories))
                    return;
            }
        }
        $slider->options->thumbnailWidth = 150;
        //slides
        foreach ($slides as $key => $slide) {
            $slides[$key]['options'] = Tools::jsonDecode($slide['options']);
            if ($slide['image']) {
                $source_path = FlexiSlides::get_image_path($id);
                $image_temp = $image = $slide['image'];
            } else {
                $source_path = FlexiSlides::get_image_path();
                $image = 'empty.jpg';
                $image_temp = $slider->options->thumbnailWidth . '_' . $image;
            }
            $slides[$key]['image_helper']['thumb'] = ImageManager::thumbnail($source_path . $image, '/pager_' . $image_temp, $slider->options->thumbnailWidth);
            $slides[$key]['image_helper']['dir'] = _MODULE_DIR_ . self::$definition['table'] . '/img/' . $id . '/';
            list($w, $h) = @getimagesize($source_path . $image);
            $slides[$key]['image_helper']['width'] = $w;
            $slides[$key]['image_helper']['height'] = $h;
        }

        Context::getContext()->smarty->smarty->assign(array(
            'slider' => $slider,
            'slides' => $slides
        ));
        return Context::getContext()->smarty->fetch(
                        dirname(__FILE__) . '/../views/templates/hook/slider.tpl');
    }

    public function delete() {
        parent::delete();

        $this->cleanPositions();

        $slides = FlexiSlides::getAll(array(self::$definition['primary'] => $this->id));
        if ($slides) {
            foreach ($slides as $slide) {
                $slide_obj = new FlexiSlides($slide[FlexiSlides::$definition['primary']]);
                $slide_obj->delete();
            }
        }
    }

// smarty
    public static function get_slider($params) {
        $id = '';
        if (isset($params['alias'])) {
            $alias = $params['alias'];
            $id = self::findIdByAlias($alias);
        } elseif (isset($params['id']))
            $id = $params['id'];

        if (empty($id))
            return;

        $result = self::load_slider($id);
        if (isset($params['assign'])) {
            Context::getContext()->smarty->assign(trim($params['assign']), $result);
            return;
        }
        return $result;
    }

    public static function truefalse($truefalse, $assign = null) {

        if ($truefalse)
            $result = 'true';
        else
            $result = 'false';

        if ($assign != null) {
            Context::getContext()->smarty->assign(trim($assign), $result);
            return;
        }
        return $result;
    }

    public static function duplicate() {
        $slider = new FlexiSliders(Tools::getValue(self::$definition['primary']));
        if (!is_object($slider))
            return;
        unset($slider->id);
        $slider->save();
        $parms = array();
        $parms[FlexiSliders::$definition['primary']] = Tools::getValue(self::$definition['primary']);
        $slides = FlexiSlides::getAll($parms);
        $par = self::$definition['primary'];
        foreach ($slides as $slide_old) {
            $slide = new FlexiSlides($slide_old[FlexiSlides::$definition['primary']]);
            unset($slide->id);
            $slide->$par = $slider->id;
            $slide->save();
            //copy images
            $source = FlexiSlides::get_image_path(Tools::getValue(self::$definition['primary']));
            $dest = FlexiSlides::get_image_path($slider->id);
            @mkdir($dest);
            copy($source . $slide->image, $dest . $slide->image);
        }
    }

    /* Get all CMS blocks */

    public static function getAllCMSStructure($id_shop = false) {
        $categories = self::getCMSCategories();
        $id_shop = ($id_shop !== false) ? $id_shop : Context::getContext()->shop->id;
        $all = array();
        foreach ($categories as $value) {
            $array_key = 'category_' . $value['id_cms_category'];
            $value['name'] = str_repeat("- ", $value['level_depth']) . $value['name'];
            $value['id'] = $array_key;
            $all[$array_key] = $value;
            $pages = self::getCMSPages($value['id_cms_category'], $id_shop);
            foreach ($pages as $page) {
                $array_key = 'cms_' . $page['id_cms'];
                $page['name'] = str_repeat("&nbsp;&nbsp;", $value['level_depth']) . $page['meta_title'];
                $page['id'] = $array_key;
                $all[$array_key] = $page;
            }
        }
        return $all;
    }

    public static function getCMSPages($id_cms_category, $id_shop = false) {
        $id_shop = ($id_shop !== false) ? $id_shop : Context::getContext()->shop->id;

        $sql = 'SELECT c.`id_cms`, cl.`meta_title`, cl.`link_rewrite`
			FROM `' . _DB_PREFIX_ . 'cms` c
			INNER JOIN `' . _DB_PREFIX_ . 'cms_shop` cs
			ON (c.`id_cms` = cs.`id_cms`)
			INNER JOIN `' . _DB_PREFIX_ . 'cms_lang` cl
			ON (c.`id_cms` = cl.`id_cms`)
			WHERE c.`id_cms_category` = ' . (int) $id_cms_category . '
			AND cs.`id_shop` = ' . (int) $id_shop . '
			AND cl.`id_lang` = ' . (int) Context::getContext()->language->id . '
			AND c.`active` = 1
			ORDER BY `position`';

        return Db::getInstance()->executeS($sql);
    }

    public static function getCMSCategories($recursive = false, $parent = 0) {
        if ($recursive === false) {
            $sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
					FROM `' . _DB_PREFIX_ . 'cms_category` bcp
					INNER JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl
					ON (bcp.`id_cms_category` = cl.`id_cms_category`)
					WHERE cl.`id_lang` = ' . (int) Context::getContext()->language->id;
            if ($parent)
                $sql .= ' AND bcp.`id_parent` = ' . (int) $parent;

            return Db::getInstance()->executeS($sql);
        }
        else {
            $sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
					FROM `' . _DB_PREFIX_ . 'cms_category` bcp
					INNER JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl
					ON (bcp.`id_cms_category` = cl.`id_cms_category`)
					WHERE cl.`id_lang` = ' . (int) Context::getContext()->language->id;
            if ($parent)
                $sql .= ' AND bcp.`id_parent` = ' . (int) $parent;

            $results = Db::getInstance()->executeS($sql);
            $categories = array();
            foreach ($results as $result) {
                $sub_categories = self::getCMSCategories(true, $result['id_cms_category']);
                if ($sub_categories && count($sub_categories) > 0)
                    $result['sub_categories'] = $sub_categories;
                $categories[] = $result;
            }

            return isset($categories) ? $categories : false;
        }
    }

    public function updatePosition($way, $position) {
        $sql = 'SELECT cp.`' . self::$definition['primary'] . '`, cp.`position` 
			FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` cp 
			ORDER BY cp.`position` ASC';
        if (!$res = Db::getInstance()->executeS($sql))
            return false;

        $moved_field = $res[0];

        if (!isset($moved_field) || !isset($position))
            return false;

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
       
        if (Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . self::$definition['table'] . '`
				SET `position`= `position` ' . ($way ? '- 1 ' : '+ 1 ') . '
				 WHERE `position`
					' . ($way ? ' > ' . (int) $moved_field['position'] . ' AND `position` <= ' . (int) $position : ' < ' . (int) $moved_field['position'] . ' AND `position` >= ' . (int) $position) . ';') && Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . self::$definition['table'] . '`
				SET `position` = ' . (int) $position . ' 
				 WHERE `' . self::$definition['primary'] . '` = ' . (int) $moved_field[self::$definition['primary']] . ';'))
            return self::cleanPositions();
        return false;
    }

    public static function cleanPositions() {
        $sql = 'SELECT `' . self::$definition['primary'] . '` 
			FROM `' . _DB_PREFIX_ . self::$definition ['table'] . '` 
			ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);
        for ($i = 0, $total = count($result); $i < $total; ++$i) {
            $sql = 'UPDATE `' . _DB_PREFIX_ . self::$definition['table'] . '` 
				SET `position` = ' . (int) $i . ' WHERE `' . self::$definition['primary'] . '` = ' . (int) $result[$i][self::$definition['primary']];
            Db::getInstance()->execute($sql);
        }
        return true;
    }

    public static function getLastPosition() {
        $sql = 'SELECT MAX(position) 
			FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`';
        return (Db::getInstance()->getValue($sql));
    }

    private function transform_options() {
        if (!Tools::getIsset('submitUpdate' . self::$definition['table']) && !Tools::getIsset('submitAdd' . self::$definition['table']))
            return false;
        $parms = array();
        foreach (self::get_option_fields() as $option)
            $parms[$option] = Tools::getValue($option);
        return Tools::jsonEncode($parms);
    }

    public static function get_options(&$options) {
        if (!empty($options))
            $options = Tools::jsonDecode($options);
    }

    public static function get_option_fields() {
        return array('categories', 'cms', 'hooks', 'effect', 'keys', 'buttons', 'touch', 'pagination', 'startOnMouseOver', 'stopOnMouseOver', 'view', 'move',
            'slicesx', 'slicesy', 'mode', 'direction', 'stripes', 'orientation', 'easing', 'speed', 'timer', 'height');
    }

}

?>