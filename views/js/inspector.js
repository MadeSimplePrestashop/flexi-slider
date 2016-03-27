function get(name)
{
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(window.location.href);
    if (results == null)
        return "";
    else
        return results[1];
}


$(document).ready(function () {

    $('.cancelinspector').click(function () {
        $('.inspector-select').removeClass('inspector-select');
        $('body').removeClass('ignore');
        $('#inspector-tools').slideUp('slow');
    })
    $('.submitinspector').click(function () {
        //$('#inspector-tools form').serialize()
        window.opener.document.getElementById('element').value = $("#inspector-text").val();
        window.close();
    })

    $("a").dblclick(function () {
        $('#inspector-tools').hide();
        location.href = $(this).attr('href');
    }
    );
    $('a').each(function ()
    {
        var href = $(this).attr('href');
        var search = this.search;
        var href_add = 'fs_live_edit_token=' + get('fs_live_edit_token')
                + '&id_employee=' + get('id_employee');
        var baseDir_ = baseDir.replace('https', 'http');
        if (typeof (href) != 'undefined' && href.substr(0, 1) != '#' && href.replace('https', 'http').substr(0, baseDir_.length) == baseDir_)
        {
            if (search.length == 0)
                this.search = href_add;
            else
                this.search += '&' + href_add;
        }
    });
    var last;
    /**
     * Get full CSS path of any element
     * 
     * Returns a jQuery-style CSS path, with IDs, classes and ':nth-child' pseudo-selectors.
     * 
     * Can either build a full CSS path, from 'html' all the way to ':nth-child()', or a
     * more optimised short path, stopping at the first parent with a specific ID,
     * eg. "#content .top p" instead of "html body #main #content .top p:nth-child(3)"
     */
    function cssPath(el) {
        var fullPath = 0, // Set to 1 to build ultra-specific full CSS-path, or 0 for optimised selector
                useNthChild = 1, // Set to 1 to use ":nth-child()" pseudo-selectors to match the given element
                cssPathStr = '',
                testPath = '',
                parents = [],
                parentSelectors = [],
                tagName,
                cssId,
                cssClass,
                tagSelector,
                vagueMatch,
                nth,
                i,
                c;
        // Go up the list of parent nodes and build unique identifier for each:
        while (el) {
            vagueMatch = 0;
            // Get the node's HTML tag name in lowercase:
            tagName = el.nodeName.toLowerCase();
            // Get node's ID attribute, adding a '#':
            cssId = (el.id) ? ('#' + el.id) : false;
            // Get node's CSS classes, replacing spaces with '.':
            //cssClass = (el.className) ? ('.' + el.className.replace(/\s+/g, ".")) : '';
            cssClass = ( el.getAttribute('class') ) ? ( '.' + el.getAttribute('class').replace(/\s+/g,".") ) : '';

            // Build a unique identifier for this parent node:
            if (cssId) {
                // Matched by ID:
                tagSelector = tagName + cssId + cssClass;
            } else if (cssClass) {
                // Matched by class (will be checked for multiples afterwards):
                tagSelector = tagName + cssClass;
            } else {
                // Couldn't match by ID or class, so use ":nth-child()" instead:
                vagueMatch = 1;
                tagSelector = tagName;
            }

            // Add this full tag selector to the parentSelectors array:
            parentSelectors.unshift(tagSelector)

            // If doing short/optimised CSS paths and this element has an ID, stop here:
            if (cssId && !fullPath)
                break;
            // Go up to the next parent node:
            el = el.parentNode !== document ? el.parentNode : false;
        } // endwhile

        // Build the CSS path string from the parent tag selectors:
        for (i = 0; i < parentSelectors.length; i++) {
            if(i==0)
            cssPathStr += parentSelectors[i]; // + ' ' + cssPathStr;
        else
            cssPathStr += ' ' + parentSelectors[i]; // + ' ' + cssPathStr;

            // If using ":nth-child()" selectors and this selector has no ID / isn't the html or body tag:
            if (useNthChild && !parentSelectors[i].match(/#/) && !parentSelectors[i].match(/^(html|body)$/)) {

                // If there's no CSS class, or if the semi-complete CSS selector path matches multiple elements:
                if ($(cssPathStr.split(' ').join(' > ')).length > 1) {
                    
                    // Count element's previous siblings for ":nth-child" pseudo-selector.
                    
//                    for (nth = 1,
//                        c = el;
//                        c !== null && c.previousElementSibling;
//                        c = c.previousElementSibling,
//                        nth++);

                        nth = $(cssPathStr.split(' ').join(' > ')).length;
                    
                    // Append ":nth-child()" to CSS path:
                    cssPathStr += ":nth-child(" + nth + ")";
                }
            }

        }

        // Return trimmed full CSS path:
        return cssPathStr.replace(/^[ \t]+|[ \t]+$/, '');
    }


    /**
     * MouseOver action for all elements on the page:
     */
    function inspectorMouseOver(e) {
        // NB: this doesn't work in IE (needs fix):
        var element = e.target;
        if ($(element).hasClass('ignore') || $(element).parents('.ignore:first').length)
            return;
        // Set outline:
        element.style.outline = '2px solid #f00';
        element.style.position = 'relative';
        element.style.zIndex = '10000';
        //element.style = 'relative:relative; z-index:1000';
        // Set last selected element so it can be 'deselected' on cancel.
        last = element;
    }


    /**
     * MouseOut event action for all elements
     */
    function inspectorMouseOut(e) {
        // Remove outline from element:
        e.target.style.outline = '';
        e.target.style.position = '';
        e.target.style.zIndex = '';
    }


    /**
     * Click action for hovered element
     */
    function inspectorOnClick(e) {
        e.preventDefault();
        var element = e.target;
        if ($(element).hasClass('ignore') || $(element).parents('.ignore:first').length)
            return;
        // These are the default actions (the XPath code might be a bit janky)
        // Really, these could do anything:
        $("#inspector-text").val(cssPath(e.target));
        $('.inspector-select').removeClass('inspector-select');
        $(element).addClass('inspector-select');
        $('body').addClass('ignore');
        $('#inspector-note').hide();
        //console.log(cssPath(e.target));
        /* console.log( getXPath(e.target).join('/') ); */
        $('#inspector-tools').slideDown('slow');
        return false;
    }


    /**
     * Function to cancel inspector:
     */
    function inspectorCancel(e) {
        // Unbind inspector mouse and click events:
        if (e === null && event.keyCode === 27) { // IE (won't work yet):
            document.detachEvent("mouseover", inspectorMouseOver);
            document.detachEvent("mouseout", inspectorMouseOut);
            document.detachEvent("click", inspectorOnClick);
            document.detachEvent("keydown", inspectorCancel);
            last.style.outlineStyle = 'none';
        } else if (e.which === 27) { // Better browsers:
            document.removeEventListener("mouseover", inspectorMouseOver, true);
            document.removeEventListener("mouseout", inspectorMouseOut, true);
            document.removeEventListener("click", inspectorOnClick, true);
            document.removeEventListener("keydown", inspectorCancel, true);
            // Remove outline on last-selected element:
            last.style.outline = 'none';
        }
    }


    /**
     * Add event listeners for DOM-inspectorey actions
     */
    if (document.addEventListener) {
        document.addEventListener("mouseover", inspectorMouseOver, true);
        document.addEventListener("mouseout", inspectorMouseOut, true);
        document.addEventListener("click", inspectorOnClick, true);
        document.addEventListener("keydown", inspectorCancel, true);
    } else if (document.attachEvent) {
        document.attachEvent("mouseover", inspectorMouseOver);
        document.attachEvent("mouseout", inspectorMouseOut);
        document.attachEvent("click", inspectorOnClick);
        document.attachEvent("keydown", inspectorCancel);
    }

}
)