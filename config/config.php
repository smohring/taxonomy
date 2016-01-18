<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * This is the catalog configuration file.
 *
 * PHP version 5
 * @copyright  2007 Martin Komara, 2008 Thyon Design, 2008 Evan Ruchelman
 * @author     Martin Komara <martin.komara@gmail.com>, John Brand <john.brand@thyon.com>, Evan Ruchelman <tl@ruchelman.com>
 * @package    CatalogModule 
 * @license    GPL 
 * @filesource
 */

$GLOBALS['BE_MOD']['content']['taxonomy'] = array
(
    'tables'       => array('tl_taxonomy'),
    'icon'         => 'system/modules/taxonomy/html/icon.gif'
);

$GLOBALS['BE_FFL']['taxonomyTree'] = 'TaxonomyTree';

if (TL_MODE == 'BE')
{
	$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/taxonomy/html/taxonomywizard.js'; 
}

$GLOBALS['TL_HOOKS']['executePreActions'][] = array('TaxonomyTree', 'executePreActions');
$GLOBALS['TL_HOOKS']['executePostActions'][] = array('TaxonomyTree', 'executePostActions');