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
 * Language file for table tl_catalog_types (en).
 *
 * PHP version 5
 * @copyright  2007 Martin Komara, 2008 Thyon Design
 * @author     Martin Komara <martin.komara@gmail.com>, John Brand <john.brand@thyon.com>
 * @package    TaxonomyModule 
 * @license    GPL 
 * @filesource
 */

/**
 * Fields
 */

$GLOBALS['TL_LANG']['tl_taxonomy']['title'] = 'Taxonomy terms';
$GLOBALS['TL_LANG']['tl_taxonomy']['name'] = array('Name', 'Enter the name of the taxonomy term.');
$GLOBALS['TL_LANG']['tl_taxonomy']['alias'] = array('Alias', 'Alias to be used for referencing the term, typically in URL filters.');

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_taxonomy']['new']						= array('New term', 'Create new term.');
$GLOBALS['TL_LANG']['tl_taxonomy']['show']					= array('Term details', 'Show details of term ID %s');
$GLOBALS['TL_LANG']['tl_taxonomy']['edit']					= array('Edit term', 'Edit term ID %s');
$GLOBALS['TL_LANG']['tl_taxonomy']['copy']					= array('Duplicate term', 'Duplicate term ID %s');
$GLOBALS['TL_LANG']['tl_taxonomy']['cut']						= array('Move term', 'Move term ID %s');
$GLOBALS['TL_LANG']['tl_taxonomy']['delete']				= array('Delete term', 'Delete term ID %s');
$GLOBALS['TL_LANG']['tl_taxonomy']['copyChildren']	= array('Duplicate term with sub-terms', 'Duplicate term ID %s with sub-terms');

/**
 * Legends
 */

$GLOBALS['TL_LANG']['tl_taxonomy']['title_legend'] = 'Title and alias';


?>