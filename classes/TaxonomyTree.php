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
 * PHP version 5
 * @copyright  2007 Martin Komara, 2008 Thyon Design, 2008 Evan Ruchelman
 * @author     Martin Komara <martin.komara@gmail.com>, John Brand <john.brand@thyon.com>, Evan Ruchelman <tl@ruchelman.com>
 * @package    Backend
 * @license    LGPL
 * @filesource
 */


/**
 * Class TaxonomyTree
 *
 * Provide methods to handle input field "taxonomyTree".
 * @copyright  2007 Martin Komara, 2008 Thyon Design, 2008 Evan Ruchelman
 * @author     Martin Komara <martin.komara@gmail.com>, John Brand <john.brand@thyon.com>, Evan Ruchelman <tl@ruchelman.com>
 * @package    Controller
 */
class TaxonomyTree extends Widget
{

    /**
     * Submit user input
     * @var boolean
     */
    protected $blnSubmitInput = true;

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'be_widget';

    /**
     * Ajax id
     * @var string
     */
    protected $strAjaxId;

    /**
     * Ajax key
     * @var string
     */
    protected $strAjaxKey;

    /**
     * Ajax name
     * @var string
     */
    protected $strAjaxName;


    /**
     * Load database object
     * @param array
     */
    public function __construct($arrAttributes = false)
    {
        $this->import('Database');
        $this->import('BackendUser', 'User');

        parent::__construct($arrAttributes);
    }


    /**
     * Add specific attributes
     * @param string
     * @param mixed
     */
    public function __set($strKey, $varValue)
    {
        switch ($strKey) {
            case 'mandatory':
                $this->arrConfiguration['mandatory'] = $varValue ? true : false;
                break;

            default:
                parent::__set($strKey, $varValue);
                break;
        }
    }


    /**
     * Skip the field if "change selection" is not checked
     * @param mixed
     * @return mixed
     */
    protected function validator($varInput)
    {
        //Todo: Change this code to support Contao 3.x
        /*
        if (!$this->Input->post($this->strName . '_save')) {
            $this->mandatory = false;
            $this->blnSubmitInput = false;
        }
        */

        return parent::validator($varInput);
    }


    /**
     * Generate the widget and return it as string
     * @return string
     */
    public function generate()
    {
        $rootid = ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['rootid'] ? $this->eliminateNestedPages($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['rootid'], 'tl_taxonomy') : array(0));
        $tree = null;

        if ($rootid[0] == 0) {
            $objRoot = $this->Database->prepare("SELECT id FROM tl_taxonomy WHERE pid=? ORDER BY sorting")
                ->execute(0);

            while ($objRoot->next()) {
                $tree .= $this->renderTaxonomytree($objRoot->id, -20);
            }
        } else // Show mounted pages to users
        {
            foreach ($rootid as $node) {
                $tree .= $this->renderTaxonomytree($node, -20);
            }
        }

        // Reset radio button selection
        if ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['fieldType'] == 'radio') {
            $strReset = "\n" . '    <li class="tl_folder"><div class="tl_left">&nbsp;</div> <div class="tl_right"><label for="ctrl_' . $this->strId . '_0" class="tl_change_selected">' . $GLOBALS['TL_LANG']['MSC']['resetSelected'] . '</label> <input type="radio" name="' . $this->strName . '" id="' . $this->strName . '_0" class="tl_tree_radio" value="" onfocus="Backend.getScrollOffset();" /></div><div style="clear:both;"></div></li>';
        }

        // Select ALL checkbox selection
        if ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['fieldType'] == 'checkbox') {
            $strReset = "\n" . '    <li class="tl_folder"><div class="tl_left">&nbsp;</div> <div class="tl_right"><label for="check_all_' . $this->strId . '_0" class="tl_change_selected">' . $GLOBALS['TL_LANG']['MSC']['selectAll'] . '</label> <input type="checkbox" id="check_all_' . $this->strId . '_0" class="tl_checkbox" value="" onclick="Backend.toggleCheckboxGroup(this, \'' . $this->strName . '\')" /></div><div style="clear:both;"></div></li>';
        }

        $header = strlen($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['header'][0]) ? $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['header'] :
            (strlen($GLOBALS['TL_LANG']['MSC']['taxonomyTree']) ? $GLOBALS['TL_LANG']['MSC']['taxonomyTree'] : 'Taxonomy terms');

        return '  <ul class="tl_listing' . (strlen($this->strClass) ? ' ' . $this->strClass : '') . '" id="' . $this->strId . '">
    <li class="tl_folder_top"><div class="tl_left">' . $this->generateImage($GLOBALS['BE_MOD']['content']['taxonomy']['icon']) . ' ' . $header . '</div> <div class="tl_right"><label for="ctrl_' . $this->strId . '" class="tl_change_selected">' . $GLOBALS['TL_LANG']['MSC']['changeSelected'] . '</label> <input type="checkbox" name="' . $this->strName . '_save" id="ctrl_' . $this->strId . '" class="tl_tree_checkbox" value="1" onclick="Backend.showTreeBody(this, \'' . $this->strId . '_parent\');" /></div><div style="clear:both;"></div></li><li class="parent" id="' . $this->strId . '_parent"><ul>' . $tree /*this->renderTaxonomytree($rootid, -20)*/ . $strReset . '
  </ul></li></ul>';
    }


    /**
     * Generate a particular subpart of the page tree and return it as HTML string
     * @param integer
     * @param string
     * @param integer
     * @return string
     */
    public function generateAjax($id, $strField, $level)
    {

        $this->strField = $strField;

        if (!$this->Input->post('isAjax')) {
            return '';
        }

        if ($this->Database->fieldExists($strField, $this->strTable)) {
            $objField = $this->Database->prepare("SELECT " . $strField . " FROM " . $this->strTable . " WHERE id=?")
                ->limit(1)
                ->execute($this->strId);

            if ($objField->numRows) {
                $this->varValue = deserialize($objField->$strField);
            }
        }

        $this->varValue = deserialize($objField->$strField);

        // Load requested nodes
        $tree = '';
        $level = $level * 20;

        $objPage = $this->Database->prepare("SELECT id FROM tl_taxonomy WHERE pid=? ORDER BY sorting")
            ->execute($id);

        while ($objPage->next()) {
            $tree .= $this->renderTaxonomytree($objPage->id, $level);
        }

        return $tree;

    }


    /**
     * Check the Ajax pre actions
     * @param string
     * @param object
     * @return string
     */
    public function executePreActions($action)
    {
        switch ($action) {
            // Toggle nodes of the file or page tree
            case 'toggleTaxonomytree':
                $this->strAjaxId = preg_replace('/.*_([0-9a-zA-Z]+)$/i', '$1', $this->Input->post('id'));
                $this->strAjaxKey = str_replace('_' . $this->strAjaxId, '', $this->Input->post('id'));

                if ($this->Input->get('act') == 'editAll') {
                    $this->strAjaxKey = preg_replace('/(.*)_[0-9a-zA-Z]+$/i', '$1', $this->strAjaxKey);
                    $this->strAjaxName = preg_replace('/.*_([0-9a-zA-Z]+)$/i', '$1', $this->Input->post('name'));
                }

                $nodes = $this->Session->get($this->strAjaxKey);
                $nodes[$this->strAjaxId] = intval($this->Input->post('state'));

                $this->Session->set($this->strAjaxKey, $nodes);
                exit;
                break;

            // Load nodes of the file or page tree
            case 'loadTaxonomytree':
                $this->strAjaxId = preg_replace('/.*_([0-9a-zA-Z]+)$/i', '$1', $this->Input->post('id'));
                $this->strAjaxKey = str_replace('_' . $this->strAjaxId, '', $this->Input->post('id'));

                if ($this->Input->get('act') == 'editAll') {
                    $this->strAjaxKey = preg_replace('/(.*)_[0-9a-zA-Z]+$/i', '$1', $this->strAjaxKey);
                    $this->strAjaxName = preg_replace('/.*_([0-9a-zA-Z]+)$/i', '$1', $this->Input->post('name'));
                }

                $nodes = $this->Session->get($this->strAjaxKey);
                $nodes[$this->strAjaxId] = intval($this->Input->post('state'));

                $this->Session->set($this->strAjaxKey, $nodes);
                break;
        }
    }


    /**
     * Check the Ajax post actions
     * @param string
     * @param object
     * @return string
     */
    public function executePostActions($action, $dc)
    {
        if ($action == 'loadTaxonomytree') {

            $arrData['strTable'] = $dc->table;
            $arrData['id'] = strlen($this->strAjaxName) ? $this->strAjaxName : $dc->id;
            $arrData['name'] = $this->Input->post('name');

            $objWidget = new $GLOBALS['BE_FFL']['taxonomyTree']($arrData, $dc);

            echo $objWidget->generateAjax($this->strAjaxId, $this->Input->post('field'), intval($this->Input->post('level')));
            exit;
        }
    }


    /**
     * Recursively render the taxonomytree
     * @param int
     * @param integer
     * @param boolean
     * @return string
     */
    private function renderTaxonomytree($id, $intMargin)
    {
        static $session, $family;
        $session = $this->Session->getData();

        $global_ptr = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval'];

        $flag = substr($this->strField, 0, 2);
        $node = 'tree_' . $this->strTable . '_' . $this->strField;
        $xtnode = 'tree_' . $this->strTable . '_' . $this->strName;

        // Get session data and toggle nodes
        if ($this->Input->get($flag . 'tg')) {
            $session[$node][$this->Input->get($flag . 'tg')] = (isset($session[$node][$this->Input->get($flag . 'tg')]) && $session[$node][$this->Input->get($flag . 'tg')] == 1) ? 0 : 1;
            $this->Session->setData($session);

            $this->redirect(preg_replace('/(&(amp;)?|\?)' . $flag . 'tg=[^& ]*/i', '', $this->Environment->request));
        }

        if (!isset($family)) {
            $objFamily = $this->Database->prepare("SELECT * FROM tl_taxonomy ORDER BY sorting")->execute();
            while ($objFamily->next()) {
                $family[$objFamily->pid]['children'][] = $objFamily->id;
                $family[$objFamily->pid]['parent'] = 1;
            }

            foreach ($family as $fid => $value) {
                for ($t = 0; $t < count($value['children']); $t++) {
                    if ($family[$value['children'][$t]]['parent']) {
                        $family[$fid]['grandparent'] = 1;
                    }
                }
            }
        }

        $obj = $this->Database->prepare("SELECT * FROM tl_taxonomy WHERE id=?")
            ->limit(1)
            ->execute($id);

        // Return if there is no result
        if ($obj->numRows < 1) {
            return '';
        }

        if ($global_ptr['onlyParents'] && !$family[$id]['parent']) {
            return;
        }

        $return = '';
        $intSpacing = 20;
        $childs = array();

        // Check whether there are child records
        $objNodes = $this->Database->prepare("SELECT id FROM tl_taxonomy WHERE pid=? ORDER BY sorting")
            ->execute($id);

        if ($objNodes->numRows) {
            $childs = $objNodes->fetchEach('id');
        }

        $return .= "\n    " . '<li class="' . (($obj->type == 'root') ? 'tl_folder' : 'tl_file') . '" onmouseover="Theme.hoverDiv(this, 1);" onmouseout="Theme.hoverDiv(this, 0);"><div class="tl_left" style="padding-left:' . ($intMargin + $intSpacing) . 'px;">';

        $folderAttribute = 'style="margin-left:20px;"';
        $session[$node][$id] = is_numeric($session[$node][$id]) ? $session[$node][$id] : 0;
        $level = ($intMargin / $intSpacing + 1);

        if ((!$global_ptr['onlyParents'] && count($childs)) || $family[$id]['grandparent']) {
            $folderAttribute = '';
            $img = ($session[$node][$id] == 1) ? 'folMinus.gif' : 'folPlus.gif';
            $return .= '<a href="' . $this->addToUrl($flag . 'tg=' . $id) . '" onclick="Backend.getScrollOffset(); return AjaxRequestTaxonomy.toggleTaxonomytree(this, \'' . $xtnode . '_' . $id . '\', \'' . $this->strField . '\', \'' . $this->strName . '\', ' . $level . ');">' . $this->generateImage($img,
                    '', 'style="margin-right:2px;"') . '</a>';
        }

        $sub = 0;
        $image = 'iconPLAIN.gif';

        // Add page name
        $return .= $this->generateImage($image, '', $folderAttribute) . ' <label for="' . $this->strName . '_' . $id . '">' . ($family[$id]['parent'] ? '<strong>' : '') . $obj->name . ($family[$id]['parent'] ? '</strong>' : '') . '</label></div> <div class="tl_right">';

        // Add checkbox or radio button
        if ((!$global_ptr['noSelectParents'] || !$family[$id]['parent']))
            switch ($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['fieldType']) {
                case 'checkbox':
                    $return .= '<input type="checkbox" name="' . $this->strName . '[]" id="' . $this->strName . '_' . $id . '" class="tl_checkbox" value="' . specialchars($id) . '"' . ($global_ptr['submitOnChange'] ? 'onclick="Backend.autoSubmit(\'' . $this->strTable . '\');"' : '') . ' onfocus="Backend.getScrollOffset();"' . $this->optionChecked($id,
                            $this->varValue) . ' />';
                    break;

                case 'radio':
                    $return .= '<input type="radio" name="' . $this->strName . '" id="' . $this->strName . '_' . $id . '" class="tl_tree_radio" value="' . specialchars($id) . '" ' . ($global_ptr['submitOnChange'] ? 'onclick="Backend.autoSubmit(\'' . $this->strTable . '\');"' : '') . 'onfocus="Backend.getScrollOffset();"' . $this->optionChecked($id,
                            $this->varValue) . ' />';
                    break;
            }

        $return .= '</div><div style="clear:both;"></div></li>';

        // Begin new submenu
        if (count($childs) && $session[$node][$id] == 1) {
            $return .= '<li class="parent" id="' . $xtnode . '_' . $id . '"><ul class="level_' . $level . '">';

            for ($k = 0; $k < count($childs); $k++) {
                $return .= $this->renderTaxonomytree($childs[$k], ($intMargin + $intSpacing));
            }

            $return .= '</ul></li>';
        }

        return $return;
    }
}