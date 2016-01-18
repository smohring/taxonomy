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
 * @copyright  2007 Martin Komara, 2008 Thyon Design, 2008 Evan Ruchelman
 * @author     Martin Komara <martin.komara@gmail.com>, John Brand <john.brand@thyon.com>, Evan Ruchelman <tl@ruchelman.com>
 * @package    Taxonomy
 * @license    GPL
 * @filesource
 */


/**
 * Table tl_taxonomy
 */

$GLOBALS['TL_DCA']['tl_taxonomy'] = array
(

    // Config
    'config' => array
    (
        'dataContainer' => 'Table',
        'enableVersioning' => true,
        'label' => &$GLOBALS['TL_LANG']['tl_taxonomy']['title'],
        'onload_callback' => array
        (
            array('tl_taxonomy', 'getRoot')
        )
    ),
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode' => 5,
            'fields' => array('name', 'alias'),
            'flag' => 1,
            'panelLayout' => 'search,sort,filter,limit ',
            'icon' => 'system/modules/taxonomy/html/icon.gif',
        ),
        'label' => array
        (
            'fields' => array('name', 'alias'),
            'format' => '%s (%s)'
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_taxonomy']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ),
            'copy' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_taxonomy']['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.gif',
            ),
            'copyChildren' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_taxonomy']['copyChildren'],
                'href' => 'act=paste&amp;mode=copy&amp;childs=1',
                'icon' => 'copychilds.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ),
            'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_taxonomy']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
            ),
            'cut' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_taxonomy']['cut'],
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_taxonomy']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif'
            ),
        )
    ),
    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},name,alias'
    ),
    // Fields
    'fields' => array
    (
        'name' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_taxonomy']['name'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50')
        ),
        'alias' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_taxonomy']['alias'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array('maxlength' => 255),
            'save_callback' => array
            (
                array('tl_taxonomy', 'generateAlias')
            )
        ),
    )
);

class tl_taxonomy extends Backend
{

    /**
     * Retrieve the root point for taxonomy per user
     * @param mixed
     * @param object
     * @return string
     */

    public function getRoot()
    {
        $this->import('BackendUser', 'User');

        if ($this->User->isAdmin || !count($this->User->groups)) {
            return null;
        }

        switch ($this->User->inherit) {
            case 'custom' :
                $pagemounts = (array)$this->User->taxonomymounts;
                return $pagemounts;
                break;

            case 'group'  :
                $pagemounts = array();
                break;

            case 'extend' :
                $pagemounts = (array)$this->User->taxonomymounts;
                break;
        }

        $objField = $this->Database->execute("SELECT taxonomymounts FROM tl_user_group WHERE id IN(" . join(",", $this->User->groups) . ")");

        while ($objField->next()) {
            if ($objField->taxonomymounts) {
                $pagemounts = array_merge($pagemounts, deserialize($objField->taxonomymounts));
            }
        }

        $GLOBALS['TL_DCA']['tl_taxonomy']['list']['sorting']['root'] = array_unique($pagemounts);
        return array_unique($pagemounts);
    }

    /**
     * Autogenerate a taxonomy alias if it has not been set yet
     * @param mixed
     * @param object
     * @return string
     */

    public function generateAlias($varValue, DataContainer $dc)
    {
        $objField = $this->Database->prepare("SELECT pid FROM " . $dc->table . " WHERE id=?")
            ->limit(1)
            ->execute($dc->id);

        if (!$objField->numRows) {
            throw new Exception($GLOBALS['TL_LANG']['ERR']['aliasTitleMissing']);
        }
        $pid = $objField->pid;

        $autoAlias = false;

        // Generate alias if there is none
        if (!strlen($varValue)) {
            $objTitle = $this->Database->prepare("SELECT name FROM " . $dc->table . " WHERE id=?")
                ->limit(1)
                ->execute($dc->id);

            $autoAlias = true;
            $varValue = standardize($objTitle->name);
        }

        $objAlias = $this->Database->prepare("SELECT id FROM " . $dc->table . " WHERE alias=?")
            ->execute($varValue, $dc->id);

        // Check whether the catalog alias exists
        if ($objAlias->numRows > 1 && !$autoAlias) {
            throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        // Add ID to alias
        if ($objAlias->numRows && $autoAlias) {
            $varValue .= '.' . $dc->id;
        }

        return $varValue;
    }
}