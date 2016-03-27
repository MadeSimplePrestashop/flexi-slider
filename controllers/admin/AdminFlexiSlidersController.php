<?php
/**
 * Module Flexi Slider
 * 
 * @author 	kuzmany.biz
 * @copyright 	kuzmany.biz/prestashop
 * @license 	kuzmany.biz/prestashop
 * Reminder: You own a single production license. It would only be installed on one online store (or multistore)
 */
require_once(_PS_MODULE_DIR_ . 'flexislider/models/FlexiSliders.php');

class AdminFlexiSlidersController extends ModuleAdminController
{

    protected $position_identifier = 'id_flexislider';

    public function __construct()
    {

        $this->bootstrap = true;
        $this->show_toolbar = true;
        $this->show_toolbar_options = true;
        $this->show_page_header_toolbar = true;

        $this->table = FlexiSliders::$definition['table'];
        $this->className = 'FlexiSliders';

        Shop::addTableAssociation($this->table, array('type' => 'shop'));

        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');

        parent::__construct();
    }

    public function initContent()
    {

        if (Tools::getIsset('duplicate' . $this->table))
            FlexiSliders::duplicate();
        elseif (Tools::getIsset('view' . $this->table))
            if (Tools::getIsset(FlexiSliders::$definition['primary']))
                Tools::redirectAdmin('index.php?controller=AdminFlexiSlides&' . FlexiSliders::$definition['primary'] . '=' . (int) Tools::getValue(FlexiSliders::$definition['primary']) . '&token=' . Tools::getAdminTokenLite('AdminFlexiSlides'));
            else
                $this->errors[] = Tools::displayError('Can\'t identify slider. Please repeat your choice.');
        parent::initContent();
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::getIsset('delete' . $this->table))
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminFlexiSliders'));
        elseif (Tools::isSubmit('submitAdd' . $this->table))
            if (Tools::getIsset('submitPreview'))
                Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminFlexiSlides') . '&' . FlexiSliders::$definition['primary'] . '=' . $this->object->id . '#preview');
            elseif (Tools::getIsset('submitStay'))
                Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminFlexiSliders') . '&' . FlexiSliders::$definition['primary'] . '=' . $this->object->id . '&update' . $this->table);
            else
                Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminFlexiSliders'));
    }

    public function renderForm()
    {

        $obj = $this->loadObject(true);
        if (!$obj)
            return;

        $par = FlexiSliders::$definition['primary'];

        if (is_object($obj))
            $options = Tools::jsonDecode($obj->options);
        else
            $options = '';

        $easing = array("linear", "swing", "easeInQuad", "easeOutQuad", "easeInOutQuad", "easeInCubic", "easeOutCubic", "easeInOutCubic", "easeInQuart", "easeOutQuart", "easeInOutQuart", "easeInQuint", "easeOutQuint", "easeInOutQuint", "easeInSine", "easeOutSine", "easeInOutSine", "easeInExpo", "easeOutExpo", "easeInOutExpo", "easeInCirc", "easeOutCirc", "easeInOutCirc", "easeInElastic", "easeOutElastic", "easeInOutElastic", "easeInBack", "easeOutBack", "easeInOutBack", "easeInBounce", "easeOutBounce", "easeInOutBounce");
        foreach ($easing as $key => $easin) {
            unset($easing[$key]);
            $easing[$key]['name'] = $easin;
        }

        $selected_categories = array();
        if (isset($options->categories) && empty($options->categories) == false) {
            $selected_categories = $options->categories;
        }

        $root_category = Category::getRootCategory();
        $root_category = array('id_category' => $root_category->id, 'name' => $root_category->name);

        array_unshift($easing, array('name' => ''));
        $this->fields_form = array(
            'legend' => array(
                'tinymce' => true,
                'title' => $this->l('Slider'),
                'icon' => 'icon-cogs'
            ),
            'tabs' => array(
                'options' => $this->l('Slider'),
                'controls' => $this->l('Controls'),
                'effects' => $this->l('Effects'),
                'display' => $this->l('Display'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => FlexiSliders::$definition['primary'],
                    'tab' => 'options'
                ),
                array(
                    'tab' => 'options',
                    'type' => 'text',
                    'label' => $this->l('Alias'),
                    'name' => 'alias',
                    'required' => true
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
                    'default_value' => isset($obj->active) ? $obj->active : true
                ),
                array(
                    'tab' => 'options',
                    'type' => 'text',
                    'label' => $this->l('Width'),
                    'desc' => $this->l('You can use px, %, em or whatever you want.'),
                    'name' => 'width',
                    'class' => 'fixed-width-sm',
                    'default_value' => isset($options->width) ? $options->width : '100%'
                ),
                array(
                    'tab' => 'options',
                    'type' => 'text',
                    'label' => $this->l('Height'),
                    'name' => 'height',
                    'suffix' => 'px',
                    'class' => 'fixed-width-sm',
                    'desc' => $this->l('Required'),
                    'default_value' => isset($options->height) ? $options->height : 500
                ),
                array(
                    'tab' => 'options',
                    'type' => 'text',
                    'hint' => $this->l('If timer is 0 the carrousel isn\'t automatic, else it\'s the interval in ms between each step'),
                    'label' => $this->l('Timer'),
                    'name' => 'timer',
                    'suffix' => 'ms',
                    'class' => 'fixed-width-sm',
                    'default_value' => isset($options->timer) ? $options->timer : 7000
                ),
                array(
                    'tab' => 'options',
                    'type' => 'text',
                    'hint' => $this->l('Speed is the time in ms of the transition'),
                    'label' => $this->l('Speed'),
                    'name' => 'speed',
                    'suffix' => 'ms',
                    'class' => 'fixed-width-sm',
                    'default_value' => isset($options->speed) ? $options->speed : 500
                ),
                array(
                    'tab' => 'effects',
                    'class' => 'effect',
                    'type' => 'select',
                    'label' => $this->l('Slide mode'),
                    'name' => 'effect',
                    'hint' => $this->l('Type of transition between slides'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'fade',
                                'name' => $this->l('Fade')
                            ),
                            array(
                                'id' => 'slide',
                                'name' => $this->l('Slide')
                            ),
                            array(
                                'id' => 'mosaic',
                                'name' => $this->l('Mosaic')
                            ),
                            array(
                                'id' => 'stripes',
                                'name' => $this->l('Stripes')
                            ),
                            array(
                                'id' => 'carousel',
                                'name' => $this->l('Carousel')
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'default_value' => isset($options->effect) ? $options->effect : 'fade'
                ),
                array(
                    'tab' => 'controls',
                    'type' => 'switch',
                    'label' => $this->l('Left/Right key support'),
                    'name' => 'keys',
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
                    'default_value' => isset($options->keys) ? $options->keys : true
                ),
                array(
                    'tab' => 'controls',
                    'type' => 'switch',
                    'label' => $this->l('Left/Right buttons'),
                    'name' => 'buttons',
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
                    'default_value' => isset($options->buttons) ? $options->buttons : 1
                ),
                array(
                    'tab' => 'controls',
                    'type' => 'switch',
                    'label' => $this->l('Touch support'),
                    'name' => 'touch',
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
                    'default_value' => isset($options->touch) ? $options->touch : true
                ),
                /*       array(
                  'tab' => 'controls',
                  'type' => 'switch',
                  'label' => $this->l('Pagination'),
                  'name' => 'pagination',
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
                  'default_value' => isset($options->pagination) ? $options->pagination : 1
                  ), */
                array(
                    'tab' => 'controls',
                    'type' => 'switch',
                    'label' => $this->l('Change slide on mouse over'),
                    'name' => 'startOnMouseOver',
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
                    'default_value' => isset($options->startOnMouseOver) ? $options->startOnMouseOver : false
                ),
                array(
                    'tab' => 'controls',
                    'type' => 'switch',
                    'label' => $this->l('Change slide on mouse out'),
                    'name' => 'stopOnMouseOver',
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
                    'default_value' => isset($options->stopOnMouseOver) ? $options->stopOnMouseOver : false
                ),
                array(
                    'tab' => 'effects',
                    'type' => 'text',
                    'label' => $this->l('View'),
                    'hint' => $this->l('Option only fo carousel mode.'),
                    'name' => 'view',
                    'suffix' => 'number of slides',
                    'class' => 'carousel fixed-width-sm',
                    'default_value' => isset($options->view) ? $options->view : 1
                ),
                array(
                    'tab' => 'effects',
                    'class' => 'carousel',
                    'type' => 'radio',
                    'label' => $this->l('Move'),
                    'name' => 'move',
                    'hint' => $this->l('You can combine direction of moving. But the best results of combination is only with 1 view slide (option above).'),
                    'values' => array(
                        array(
                            'value' => 'left',
                            'id' => 'left',
                            'label' => $this->l('Left')
                        ),
                        array(
                            'value' => 'right',
                            'id' => 'right',
                            'label' => $this->l('Right')
                        ),
                        array(
                            'value' => 'top',
                            'id' => 'top',
                            'label' => $this->l('Top')
                        ),
                        array(
                            'value' => 'bottom',
                            'id' => 'bottom',
                            'label' => $this->l('Bottom')
                        ),
                    ),
                    'default_value' => isset($options->move) ? $options->move : 'right'
                ),
                array(
                    'tab' => 'effects',
                    'type' => 'text',
                    'label' => $this->l('Slices (X)'),
                    'name' => 'slicesx',
                    'class' => 'mosaic fixed-width-sm',
                    'default_value' => isset($options->slicesx) ? $options->slicesx : 10
                ),
                array(
                    'tab' => 'effects',
                    'type' => 'text',
                    'label' => $this->l('Slices (Y)'),
                    'name' => 'slicesy',
                    'class' => 'mosaic fixed-width-sm',
                    'default_value' => isset($options->slicesy) ? $options->slicesy : 5
                ),
                array(
                    'tab' => 'effects',
                    'class' => 'mosaic',
                    'type' => 'radio',
                    'label' => $this->l('Mode'),
                    'name' => 'mode',
                    'default_value' => isset($options->mode) ? $options->mode : 'crawler',
                    'values' => array(
                        array(
                            'value' => 'random',
                            'id' => 'random',
                            'label' => $this->l('Random')
                        ),
                        array(
                            'value' => 'crawler',
                            'id' => 'crawler',
                            'label' => $this->l('Crawler')
                        ),
                    )
                ),
                array(
                    'tab' => 'effects',
                    'class' => 'slide',
                    'type' => 'select',
                    'label' => $this->l('Direction'),
                    'name' => 'direction',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'left',
                                'name' => $this->l('Left')
                            ),
                            array(
                                'id' => 'right',
                                'name' => $this->l('Right')
                            ),
                            array(
                                'id' => 'top',
                                'name' => $this->l('Top')
                            ),
                            array(
                                'id' => 'bottom',
                                'name' => $this->l('Bottom')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'default_value' => isset($options->direction) ? $options->direction : ''
                ),
                array(
                    'tab' => 'effects',
                    'type' => 'text',
                    'label' => $this->l('Stripes'),
                    'name' => 'stripes',
                    'class' => 'stripes fixed-width-sm',
                    'default_value' => isset($options->stripes) ? $options->stripes : 10
                ),
                array(
                    'tab' => 'effects',
                    'class' => 'stripes',
                    'type' => 'select',
                    'label' => $this->l('Orientation'),
                    'name' => 'orientation',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'vertical',
                                'name' => $this->l('Vertical')
                            ),
                            array(
                                'id' => 'horizontal',
                                'name' => $this->l('Horizontal')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'default_value' => isset($options->orientation) ? $options->orientation : 'vertical'
                ),
                array(
                    'tab' => 'options',
                    'type' => 'select',
                    'label' => $this->l('Easing'),
                    'name' => 'easing',
                    'hint' => $this->l('If you want some yummy transition'),
                    'options' => array(
                        'query' =>
                        $easing
                        ,
                        'id' => 'name',
                        'name' => 'name',
                    ),
                    'default_value' => isset($options->easing) ? $options->easing : ''
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submit',
            )
        );

        $positions = array();
        $href = $this->context->shop->getBaseUrl() . '?fs_live_edit_token=' . FlexiSliders::getLiveEditToken() . '&id_employee=' . $this->context->employee->id;
        $positions[] = '<div class="col-sm-4">';
        $positions[] = '<a onclick="if(!confirm(\'' . $this->l('Web page will be opened in new window in mode for select website position. Do you want continue?') . '\')) return false"  target="_blank" href="' . $href . '" id="select_position"><button   type="button" class="btn btn-default" >' . $this->l('select element from website') . '</button></a>';
        $positions[] = '</div>';
        $this->fields_value['position'] = implode('', $positions);
        $this->fields_form['input'][] = array(
            'tab' => 'display',
            'type' => 'free',
            'name' => 'position',
            'label' => $this->l('Website position picker')
        );
        $this->fields_value['element'] = '<input value="' . $options->element . '" name="element" id="element" type="text">';
        $this->fields_form['input'][] = array(
            'class' => 'element',
            'tab' => 'display',
            'type' => 'free',
            'name' => 'element',
            'desc' => $this->l('If leave empty, slider will not displayed'),
            'label' => $this->l('Selected element'),
        );

        $this->fields_form['input'][] = array(
            'tab' => 'display',
            'type' => 'radio',
            'label' => $this->l('Insert slider'),
            'name' => 'insert',
            'values' => array(
                array(
                    'value' => 'after',
                    'label' => $this->l('After selected element')
                ),
                array(
                    'value' => 'before',
                    'label' => $this->l('Before selected element')
                ),
                array(
                    'value' => 'prepend',
                    'label' => $this->l('Prepend to selected element')
                ),
                array(
                    'value' => 'append',
                    'label' => $this->l('Append to selected element')
                )
            ),
            'default_value' => $options->insert
        );
        $this->fields_form['input'][] = array(
            'type' => 'select',
            'label' => $this->l('Display on controllers'),
            'name' => 'controllers[]',
            'class' => 'chosen element',
            'multiple' => true,
            'options' => array(
                'query' => $this->getControllers(),
                'id' => 'name',
                'name' => 'name'
            ),
            'tab' => 'display',
            'default_value' => $options->controllers
        );

        $this->fields_form['input'][] = array(
            'type' => 'select',
            'label' => $this->l('Display on products page'),
            'name' => 'products[]',
            'class' => 'chosen element',
            'multiple' => true,
            'options' => array(
                'query' => Product::getProducts((int) Context::getContext()->language->id, 0, 1000, 'p.id_product', 'asc', false, true),
                'id' => 'id_product',
                'name' => 'name'
            ),
            'tab' => 'display',
            'default_value' => $options->products
        );

        $this->fields_form['input'][] = array(
            'tab' => 'display',
            'type' => 'categories',
            'label' => $this->l('Categories'),
            'name' => 'categories',
            'desc' => $this->l('Empty is disabled.'),
            'tree' => array(
                'use_search' => false,
                'id' => 'categoryBox',
                'use_checkbox' => true,
                'selected_categories' => $selected_categories,
            ),
            'values' => array(
                'trads' => array(
                    'Root' => $root_category,
                    'selected' => $this->l('Selected'),
                    'Collapse All' => $this->l('Collapse All'),
                    'Expand All' => $this->l('Expand All'),
                    'Check All' => $this->l('Check All'),
                    'Uncheck All' => $this->l('Uncheck All')
                ),
                'selected_cat' => $selected_categories,
                'input_name' => 'categories[]',
                'use_radio' => false,
                'use_search' => false,
                'disabled_categories' => array(),
                'top_category' => Category::getTopCategory(),
                'use_context' => true,
            )
        );

        $this->fields_form['input'][] = array(
            'tab' => 'display',
            'type' => 'select',
            'multiple' => true,
            'size' => 7,
            'label' => $this->l('CMS categories and pages'),
            'name' => 'cms[]',
            'hint' => $this->l('It\'s optional.'),
            'desc' => $this->l('Optional. CTRL+click for select/unselect more options'),
            'options' => array(
                'query' => FlexiSliders::getAllCMSStructure(),
                'id' => 'id',
                'name' => 'name'
            )
            , 'default_value' => isset($options->cms) ? $options->cms : array()
        );


        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'tab' => 'display',
                'type' => 'shop',
                'label' => $this->l('Shop association:'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->page_header_toolbar_btn['save'] = array(
            'href' => 'javascript:$("#' . $this->table . '_form button:submit").click();',
            'desc' => $this->l('Save')
        );
        $this->page_header_toolbar_btn['save-and-stay'] = array(
            'short' => 'SaveAndStay',
            'href' => 'javascript:$("#' . $this->table . '_form").attr("action", $("#' . $this->table . '_form").attr("action")+"&submitStay");$("#' . $this->table . '_form button:submit").click();',
            'desc' => $this->l('Save and stay'),
            'force_desc' => true,
        );
        if ($obj->id) {
            $this->page_header_toolbar_btn['save-and-preview'] = array(
                'short' => 'SaveAndStay',
                'href' => 'javascript:$("#' . $this->table . '_form").attr("action", $("#' . $this->table . '_form").attr("action")+"&submitPreview");$("#' . $this->table . '_form button:submit").click();',
                'desc' => $this->l('Save and go to slides'),
                'force_desc' => true,
            );
        } else {
            $this->page_header_toolbar_btn['save-and-preview'] = array(
                'short' => 'SaveAndStay',
                'href' => 'javascript:$("#' . $this->table . '_form").attr("action", $("#' . $this->table . '_form").attr("action")+"&submitPreview");$("#' . $this->table . '_form button:submit").click();',
                'desc' => $this->l('Save and add slides'),
                'force_desc' => true,
            );
        }

        if ($obj->id) {
            $this->page_header_toolbar_btn['new'] = array(
                'href' => $this->context->link->getAdminLink('AdminFlexiSlides') . '&' . FlexiSliders::$definition['primary'] . '=' . $obj->$par,
                'desc' => $this->l('Go to slides'),
                'icon' => 'process-icon-configure'
            );
        }
        $this->page_header_toolbar_btn['edit'] = array(
            'href' => self::$currentIndex . '&token=' . $this->token,
            'desc' => $this->l('Return to sliders list'),
            'icon' => 'process-icon-cancel'
        );

        $this->tpl_list_vars['title'] = 'test';

        $this->content.= "<script>
          $(document).ready(function(){
         function hide_options(){
            $('.mosaic,.slide,.stripes,.carousel').each(function(){
                $(this).parents('.form-group:first').hide();
            })  
            }
            function show_options(obj){
                    obj.each(function(){
                    $(this).parents('.form-group:first').show();
                })

            }
            hide_options();
            show_options($('.'+$('.effect').val()));
           $('.effect').change(function(){
                hide_options();
                show_options($('.'+$(this).val()));
            })
        })
                </script>";
        return parent::renderForm();
    }

    public function renderList()
    {
        $this->fields_list = array(
            'id_flexislider' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
                'orderby' => false,
                'search' => false,
            ),
            'alias' => array(
                'title' => $this->l('Tag'),
                'type' => 'text',
                'orderby' => false,
                'search' => false,
                'callback' => 'getTag'
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
            ),
            'options' => array(
                'title' => $this->l('Slides'),
                'type' => 'text',
                'orderby' =>
                false,
                'search' => false,
                'callback' => 'getSlides'
            )
        );

        $this->_orderBy = 'position';
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->l('Add new slider'),
            'icon' => 'process-icon-new'
        );
        $this->content.= "<style>.bootstrap .pull-right > .dropdown-menu { height: auto}</style>";
        return parent::renderList() . FlexiSliders::get_ads();
    }

//render image at renderList
    public function getSlides($echo, $row)
    {
        $parms = array($echo);
        array_shift($parms);
        $parms[FlexiSliders::$definition['primary']] = $row[FlexiSliders::$definition['primary']];
        $slides = FlexiSlides::getAll($parms);
        return count($slides);
    }

    public function getTag($echo)
    {
        return '{flexislider alias=\'' . $echo . '\'}';
    }

    public function ajaxProcessUpdatePositions()
    {
        if ($this->tabAccess['edit'] === '1') {
            $id_to_move = (int) Tools::getValue('id');
            $way = (int) Tools::getValue('way');
            $object = new FlexiSliders($id_to_move);
            $positions = Tools::getValue(FlexiSliders::$definition['table']);

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
                if (isset($position) && $object->updatePosition($way, $position))
                    die(true);
                else
                    die('{"hasError" : true, "errors" : "Can not update categories position"}' . $position);
            } else
                die('{"hasError" : true, "errors" : "This category can not be loaded"}');
        }
    }

    private function getControllers()
    {
        $cache_id = __CLASS__ . __FUNCTION__ . '11';

        if (Cache::getInstance()->exists($cache_id)) {
            $controllers_array = Cache::getInstance()->get($cache_id);
        } else {

            // @todo do something better with controllers
            $controllers = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);
            ksort($controllers);
            foreach (array_keys($controllers) as $k) {
                $controllers_array[]['name'] = $k;
            }

            $modules_controllers_type = array('front' => $this->l('Front modules controller'));
            foreach (array_keys($modules_controllers_type) as $type) {
                $all_modules_controllers = Dispatcher::getModuleControllers($type);
                foreach ($all_modules_controllers as $module => $modules_controllers) {
                    foreach ($modules_controllers as $cont) {
                        $controllers_array[]['name'] = 'module-' . $module . '-' . $cont;
                    }
                }
            }

            $timeout = 3600 * 24;
            Cache::getInstance()->set($cache_id, $controllers_array, $timeout);
        }
        return $controllers_array;
    }
}
