/**
 * Class BackendTaxonomyWizard
 *
 * Provide methods to handle back end tasks.
 * @copyright  Thyon Design 2009
 * @author     John Brand <john.brand@thyon.com>
 * @package    BackendTaxonomyWizard
 */

/**
 * Show all pagetree and filetree nodes
 * @param object
 * @param string
 */
Backend.showTreeBody = function (el, id) {
    el.blur();
    $(id).setStyle('display', ($(el).checked ? 'inline' : 'none'));
};

/**
 * Hide all pagetree and filetree nodes
 */
Backend.hideTreeBody = function () {
    var lists = $$('ul');
    var parent = null;

    for (var i = 0; i < lists.length; i++) {
        if (lists[i].hasClass('mandatory')) {
            $('ctrl_' + lists[i].id).checked = 'checked';
        } else if (lists[i].hasClass('tl_listing') && (parent = lists[i].getFirst('li').getNext('li')) && parent.hasClass('parent')) {
            parent.setStyle('display', 'none');
        }
    }
};

var AjaxRequestTaxonomy =
{

    /**
     * Toggle the page tree (input field)
     * @param object
     * @param string
     * @param string
     * @param string
     * @param integer
     * @return boolean
     */
    toggleTaxonomytree: function (el, id, field, name, level) {
        el.blur();
        var item = $(id);
        var image = $(el).getFirst();

        if (item) {
            if (item.getStyle('display') != 'inline') {
                item.setStyle('display', 'inline');
                image.src = image.src.replace('folPlus.gif', 'folMinus.gif');
                new Request.Contao().post({'action': 'toggleTaxonomytree', 'id': id, 'state': 1, 'REQUEST_TOKEN': Contao.request_token});
            }
            else {
                item.setStyle('display', 'none');
                image.src = image.src.replace('folMinus.gif', 'folPlus.gif');
                new Request.Contao().post({'action': 'toggleTaxonomytree', 'id': id, 'state': 0, 'REQUEST_TOKEN': Contao.request_token});
            }

            return false;
        }

        new Request.Contao({
                onRequest: AjaxRequest.displayBox('Loading data ...'),

                onComplete: function (txt, xml) {
                    var ul = new Element('ul');

                    ul.addClass('level_' + level);
                    ul.set('html', txt);

                    item = new Element('li');

                    item.addClass('parent');
                    item.setProperty('id', id);
                    item.setStyle('display', 'inline');

                    ul.inject(item, 'inside');
                    item.inject($(el).getParent().getParent(), 'after');

                    image.src = image.src.replace('folPlus.gif', 'folMinus.gif');
                    AjaxRequest.hideBox();
                }
            }
        ).post({'action': 'loadTaxonomytree', 'id': id, 'level': level, 'field': field, 'name': name, 'state': 1, 'REQUEST_TOKEN': Contao.request_token});

        return false;
    }
};
