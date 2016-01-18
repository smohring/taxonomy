<?php if (!defined('TL_ROOT')) {
    die('You can not access this file directly!');
}

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that
 * specializes in accessibility and generates W3C-compliant HTML code. It
 * provides a wide range of functionality to develop professional websites
 * including a built-in search engine, form generator, file and user manager,
 * CSS engine, multi-language support and many more. For more information and
 * additional TYPOlight applications like the TYPOlight MVC Framework please
 * visit the project website http://www.typolight.org.
 *
 * This is the data container array for table tl_taxonomy.
 *
 * PHP version 5
 * @copyright  Evan Ruchelman 2007
 * @author     Evan Ruchelman
 * @package    Taxonomy
 * @license    GPL
 * @filesource
 */

/**
 * Table tl_user_group
 */

$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default'] = str_replace('fop;', 'fop;{taxonomy_legend},taxonomymounts;', $GLOBALS['TL_DCA']['tl_user_group']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_user_group']['fields']['taxonomymounts'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_user_group']['taxonomymounts'],
    'exclude' => true,
    'inputType' => 'taxonomyTree',
    'eval' => array('fieldType' => 'checkbox'),
);