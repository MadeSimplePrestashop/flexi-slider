<?php

/**
 * Module Flexi Slider
 * 
 * @author    kuzmany.biz
 * @copyright    kuzmany.biz/prestashop
 * @license    kuzmany.biz/prestashop
 * Reminder: You own a single production license. It would only be installed on one online store (or multistore)
 */
require_once(_PS_MODULE_DIR_ . 'flexislider/models/FlexiSliders.php');
require_once(_PS_MODULE_DIR_ . 'flexislider/models/FlexiSlides.php');

class AdminFlexiSlidesController extends ModuleAdminController {

    protected $position_identifier = 'id_flexislider_slides';
    protected static $parent_definition;

    public function __construct() {

        self::$parent_definition = FlexiSliders::$definition;

        $this->bootstrap = true;

        $this->table = FlexiSlides::$definition['table'];
        $this->className = 'FlexiSlides';

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            )
        );

        $this->lang = true;
        parent::__construct();
    }

    private function get_image_path($id_dir) {
        return _PS_MODULE_DIR_ . $this->module->name . '/img/' . $id_dir . '/';
    }

    public function initContent() {
        parent::initContent();
    }

    public function postProcess() {
        $obj = $this->loadObject(true);
//reload object is bulk action
        if (Tools::getIsset('submitFilter' . $this->table) && Tools::getValue('submitFilter' . $this->table) == 0) {
            $checked = Tools::getValue($this->table . 'Box');
            if (isset($checked[0]))
                $obj = new FlexiSlides($checked[0]);
        }
        $par = self::$parent_definition['primary'];

        parent::postProcess();
        if (Tools::getIsset('submitFilter' . $this->table)) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminFlexiSlides') . '&' . self::$parent_definition['primary'] . '=' . $obj->$par);
        } elseif (Tools::getIsset('status' . $this->table))
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminFlexiSlides') . '&' . self::$parent_definition['primary'] . '=' . $obj->$par);
        elseif (Tools::getIsset('delete' . $this->table))
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminFlexiSlides') . '&' . self::$parent_definition['primary'] . '=' . $obj->$par);
        elseif (Tools::isSubmit('submitAdd' . $this->table))
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminFlexiSlides') . '&' . self::$parent_definition['primary'] . '=' . Tools::getValue(self::$parent_definition['primary']));
    }

    public function renderForm() {

        if (!$obj = $this->loadObject(true))
            return;
        if ($obj->image) {
            $par = self::$parent_definition['primary'];
            $dir = _PS_MODULE_DIR_ . $this->module->name . '/img/' . $obj->$par . '/';
            $image = $dir . $obj->image;
        } else
            $image = '';

        if (is_object($obj))
            $options = Tools::jsonDecode($obj->options);
        else
            $options = '';

        $this->fields_form = array(
            'legend' => array(
                'tinymce' => true,
                'title' => $this->l('Slide'),
                'icon' => 'icon-cogs'
            ),
            'tabs' => array(
                'options' => $this->l('Slide'),
                'caption' => $this->l('Caption'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => self::$parent_definition['primary']
                ),
                array(
                    'tab' => 'options',
                    'type' => 'file',
                    'label' => $this->l('Image'),
                    'name' => 'image',
                    'display_image' => true,
                    'image' => $image ? ImageManager::thumbnail($image, 'thumb_detail_' . $obj->image, 200) : ''
                ),
                array(
                    'tab' => 'options',
                    'type' => 'color',
                    'label' => $this->l('Background color'),
                    'name' => 'backgroundColor',
                    'default_value' => isset($options->backgroundColor) ? $options->backgroundColor : '#000000',
                ),
                array(
                    'tab' => 'options',
                    'type' => 'select',
                    'label' => $this->l('Image size/resize'),
                    'name' => 'size',
                    'default_value' => (isset($options->size) ? $options->size : 'background-size:contain;'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'background-size:auto;',
                                'name' => $this->l('normal')
                            ),
                            array(
                                'id' => 'background-size:cover;',
                                'name' => $this->l('Scale the background image to be as large as possible')
                            ),
                            array(
                                'id' => 'background-size:contain;',
                                'name' => $this->l('Scale the image to the largest size such that both its width and its height can fit inside the content area')
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'tab' => 'options',
                    'type' => 'select',
                    'label' => $this->l('Image position'),
                    'name' => 'imagePosition',
                    'default_value' => (isset($options->imagePosition) ? $options->imagePosition : 'background-position: center center;'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'background-position: center center;',
                                'name' => $this->l('center center')
                            ),
                            array(
                                'id' => 'background-position: left top;',
                                'name' => $this->l('left top')
                            ),
                            array(
                                'id' => 'background-position: left bottom;',
                                'name' => $this->l('left bottom')
                            ),
                            array(
                                'id' => 'background-position: right top;',
                                'name' => $this->l('right top')
                            ),
                            array(
                                'id' => 'background-position: right bottom;',
                                'name' => $this->l('right bottom')
                            ),
                            array(
                                'id' => 'background-position: center top;',
                                'name' => $this->l('center top')
                            ),
                            array(
                                'id' => 'background-position: center bottom;',
                                'name' => $this->l('center bottom')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'tab' => 'options',
                    'type' => 'text',
                    'label' => $this->l('Url'),
                    'hint' => $this->l('Associate    url (optional)'),
                    'lang' => true,
                    'name' => 'url'
                ),
                'target' => array(
                    'tab' => 'options',
                    'type' => 'select',
                    'label' => $this->l('Target'),
                    'name' => 'target',
                    'hint' => $this->l('Target open window for url'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => '',
                                'name' => $this->l('None')
                            ),
                            array(
                                'id' => '_blank',
                                'name' => $this->l('_blank')
                            ),
                            array(
                                'id' => '_parent',
                                'name' => $this->l('_parent')
                            ),
                            array(
                                'id' => '_self',
                                'name' => $this->l('_self')
                            ),
                            array(
                                'id' => '_top',
                                'name' => $this->l('_top')
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    )
                ),
                array(
                    'tab' => 'options',
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'default_value' => 1
                ),
                array(
                    'tab' => 'caption',
                    'type' => 'textarea',
                    'wysiwyg' => 1,
                    'lang' => true,
                    'label' => $this->l('Caption'),
                    'name' => 'caption',
                    'autoload_rte' => true
                ),
                array(
                    'tab' => 'caption',
                    'type' => 'select',
                    'label' => $this->l('Display'),
                    'name' => 'displayCaption',
                    'default_value' => isset($options->displayCaption) ? $options->displayCaption : 'width:auto;',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'width:auto;',
                                'name' => $this->l('Auto')
                            ),
                            array(
                                'id' => 'width:100%;',
                                'name' => $this->l('Full width')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'tab' => 'caption',
                    'type' => 'color',
                    'label' => $this->l('Background color'),
                    'name' => 'captionBackgroundColor',
                    'default_value' => isset($options->captionBackgroundColor) ? $options->captionBackgroundColor : '#ffffff',
                ),
                array(
                    'tab' => 'caption',
                    'type' => 'color',
                    'label' => $this->l('Font color'),
                    'name' => 'captionFontColor',
                    'default_value' => isset($options->captionFontColor) ? $options->captionFontColor : '#555555',
                ),
                array(
                    'tab' => 'caption',
                    'type' => 'select',
                    'label' => $this->l('Position'),
                    'name' => 'captionPosition',
                    'default_value' => isset($options->captionPosition) ? $options->captionPosition : 'left:0; bottom:0;',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'left:0; top:0;',
                                'name' => $this->l('left top')
                            ),
                            array(
                                'id' => 'left:0; bottom:0;',
                                'name' => $this->l('left bottom')
                            ),
                            array(
                                'id' => 'right:0; top:0;',
                                'name' => $this->l('right top')
                            ),
                            array(
                                'id' => 'right:0; bottom:0;',
                                'name' => $this->l('right bottom')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'tab' => 'caption',
                    'type' => 'text',
                    'label' => $this->l('Opacity'),
                    'name' => 'captionOpacity',
                    'suffix' => $this->l('from 0 to 1'),
                    'class' => 'fixed-width-sm',
                    'default_value' => isset($options->captionOpacity) ? $options->captionOpacity : '0.8',
                ),
                array(
                    'tab' => 'caption',
                    'type' => 'text',
                    'label' => $this->l('Padding'),
                    'name' => 'captionPadding',
                    'class' => 'fixed-width-lg',
                    'desc'=> $this->l('top right bottom left'),
                    'default_value' => isset($options->captionPadding) ? $options->captionPadding : '0px 0px 0px 0px',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submit',
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'tab' => 'options',
                'type' => 'shop',
                'label' => $this->l('Shop association:'),
                'name' => 'checkBoxShopAsso',
            );
        }

//back button
        $this->content.=

                '<script>
         $(document).ready(function(){
            $(\'.panel-footer a\').click(function(e){e.preventDefault(); window.history.back();})
        })
                </script>';

        return parent::renderForm();
    }

    public function renderList() {

        if (!Tools::getValue(self::$parent_definition['primary'])) {
            $this->page_header_toolbar_btn['save'] = array(
                'href' => $this->context->link->getAdminLink('AdminFlexiSlides', true),
                'icon' => 'process-icon-cancel',
                'desc' => $this->l('Back to sliders list'),
            );
            $this->errors[] = Tools::displayError('Can\'t identify slider. Please <a href="' . $this->context->link->getAdminLink('AdminFlexiSliders', true) . '">go back</a> to sliders.', false);
            return parent::renderList();
        }

        $this->fields_list = array(
            'image' => array(
                'title' => $this->l('Image'),
                'type' => 'text',
                'orderby' => false,
                'search' => false,
                'callback' => 'getImage'
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'width' => 40,
                'position' => 'position',
                'orderby' => false,
                'search' => false
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'search' => false
            )
        );

        $this->_join = 'LEFT JOIN ' . _DB_PREFIX_ . self::$parent_definition ['table'] . ' AS c ON a.`' . self::$parent_definition['primary'] . '` = c.`' . self::$parent_definition['primary'] . '`';
        $this->_where = 'AND a.`' . self::$parent_definition['primary'] . '` = ' . (int) Tools::getValue(self::$parent_definition['primary']);
        $this->_orderBy = 'position';
        $this->page_header_toolbar_btn['new'] = array(
            'href' => $this->context->link->getAdminLink('AdminFlexiSlides', true) . '&add' . FlexiSlides::$definition['table'] . '&' . self::$parent_definition['primary'] . '=' . Tools::getValue(self::$parent_definition['primary']),
            'desc' => $this->l('Add new slide'),
            'icon' => 'process-icon-new'
        );
        $this->page_header_toolbar_btn['newField'] = array(
            'href' => $this->context->link->getAdminLink('AdminFlexiSliders', true) . '&update' . self::$parent_definition['table'] . '&' . self::$parent_definition['primary'] . '=' . Tools::getValue(self::$parent_definition['primary']),
            'icon' => 'process-icon-edit',
            'desc' => $this->l('Edit slider'),
        );
        $this->page_header_toolbar_btn['edit'] = array(
            'href' => 'javascript:$("#previewslider").toggle(); flexislider.reloadSlider();',
            'icon' => 'process-icon-preview',
            'desc' => $this->l('Preview toggle'),
        );
        $this->page_header_toolbar_btn['save'] = array(
            'href' => $this->context->link->getAdminLink('AdminFlexiSliders', true),
            'icon' => 'process-icon-back',
            'desc' => $this->l('Back to sliders list'),
        );

        $this->toolbar_btn['new'] = array(
            'href' => $this->context->link->getAdminLink('AdminFlexiSlides', true) . '&add' . FlexiSlides::$definition['table'] . '&' . self::$parent_definition['primary'] . '=' . Tools::getValue(self::$parent_definition['primary']),
            'desc' => $this->l('Add slide')
        );

        //TODO $this->content .= '<div id="previewslider" style="display:none">' . FlexiSliders::get_slider(array('id' => Tools::getValue(self::$parent_definition['primary']))) . '</div>';
      /*  $this->content.= '<script>
         $(document).ready(function(){
         if(location.hash == "#preview")
            $("#previewslider").show(); flexislider.reloadSlider();
        })
                </script>';*/
// set new title
        $slider = new FlexiSliders(Tools::getValue(self::$parent_definition ['primary']));
        $this->tpl_list_vars['title'] = $this->l('Slides of ') . $slider->alias;
        return parent::renderList();
    }

    public function ajaxProcessUpdatePositions() {
        if ($this->tabAccess['edit'] === '1') {
            $id_to_move = (int) Tools::getValue('id');
            $way = (int) Tools::getValue('way');
            $object = new FlexiSlides($id_to_move);
            $positions = Tools::getValue(FlexiSlides::$definition['table']);

            if (is_array($positions)) {
                foreach ($positions as $key => $value) {
                    $pos = explode('_', $value);
                    if ((isset($pos [1]) && isset($pos[2])) && ($pos [2] == $id_to_move)) {
                        $position = $key;
                        break;
                    }
                }
            }
            if (Validate::isLoadedObject($object)) {

                if (isset($position) && $object->updatePosition(
                                $way, $position))
                    die(true);
                else
                    die(
                            '{"hasError" : true, "errors" : "Can not update categories position"}' . $position);
            } else
                die(
                        '{"hasError" : true, "errors" : "This category can not be loaded"}');
        }
    }

//render image at renderList
    public function
    getImage($echo, $row) {
        if (isset($row['image']) && $row['image'])
            return ImageManager ::thumbnail($this->get_image_path($row[self::$parent_definition['primary']]) . $echo, 'thumb_' . $echo, 50);
    }

}
