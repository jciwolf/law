/*
 ************************************************************************************************************************************
 ***                                                                                                                              ***
 *** RBM Framework File v1.0                                                                                                              ***
 *** By Ramy Mostafa -							                                                                                 ***
 *** 12/03/2009                                                                                                                   ***
 *** Last Modified March 12 2009
 * * http://www.codeproject.com/Articles/34435/Building-Your-Own-JavaScript-Editor ***                                                                                                        ***
 ************************************************************************************************************************************
 */


var RBMEditor = RBMEditor ? RBMEditor : function () {
    var private = {
        EditorName: 'oDiv',
        EditorCount: 0,
        EditorView: {
            Design: "Design",
            Code: "Code",
            Preview: "Preview"
        },
        EditorCommands: {
            BOLD: "b",
            ITALIC: "i",
            UNDERLINE: "u",
            PARAGRAPH: "p",
            BIG: "big",
            SMALL: "small",
            EM: "em",
            STRONG: "strong",
            NEWLINE: "br",
            DIV: "div",
            BLOCKQUOTE: "blockquote",
            SPAN: "span",
            ADDRESS: "address",
            CITE: "cite",
            CODE: "code",
            QUOTE: "q",
            SAMP: "samp",
            VARIABLE: "var",
            LIST: "ul",
            SELECT: "select",
            BUTTON: "button",
            TEXTBOX: "textbox",
            PASSWORD: "password",
            CHECKBOX: "checkbox",
            RADIO: "radio",
            LINK: "a",
            IMAGE: "img",
            HEADER: "h",
            STYLE: "style",
            ALIGN: "align",
            FONTSIZE: "fontSize",
            FONTCOLOR: "fontColor",
            FONTFAMILY: "fontFamily"
        }
    };
    var public = {
        EditorView: {
            Design: "Design",
            Code: "Code",
            Preview: "Preview"
        },
        EditorCommands: {
            BOLD: "b",
            ITALIC: "i",
            UNDERLINE: "u",
            PARAGRAPH: "p",
            BIG: "big",
            SMALL: "small",
            EM: "em",
            STRONG: "strong",
            NEWLINE: "br",
            DIV: "div",
            BLOCKQUOTE: "blockquote",
            SPAN: "span",
            ADDRESS: "address",
            CITE: "cite",
            CODE: "code",
            QUOTE: "q",
            SAMP: "samp",
            VARIABLE: "var",
            LIST: "ul",
            SELECT: "select",
            BUTTON: "button",
            TEXTBOX: "textbox",
            PASSWORD: "password",
            CHECKBOX: "checkbox",
            RADIO: "radio",
            LINK: "a",
            IMAGE: "img",
            HEADER: "h",
            STYLE: "style",
            ALIGN: "align",
            FONTSIZE: "fontSize",
            FONTCOLOR: "fontColor",
            FONTFAMILY: "fontFamily"
        },
        CurrentEditorObject: null,

        Editor: function (ctrl, code) {

            private.EditorCount++;
            this.controlContent = RBM.GetElement(ctrl);
            this.controlContent.contentEditable = 'true';
            this.controlCode = RBM.GetElement(code);
            this.editorView = private.EditorView.Design;

        }

    }


    return public;
}();


RBMEditor.Editor.prototype = {
    ToString: function () {
        var result;
        if (this.editorView == RBMEditor.EditorView.Design) {
            result = this.controlContent.innerHTML;
            result = this.GetFormatCode(result);
        }
        return result;
    },
    GetHTML: function (sCode) {
        //return sCode;
        var ts = new String(sCode);

        var parts = ts.split("<");
        var subpart = '';
        var i = 0;
        var j = 0;
        var totalStr = '';
        var tagName = '';
        var readTag = true;
        var readSub = true;
        for (i = 0; i < parts.length; i++) {
            if (parts[i] == '')
                continue;


            subpart = '';
            tagName = '';
            readTag = true;
            readSub = true;

            for (j = 0; j < parts[i].length; j++) {
                if (parts[i].substr(j, 1) == '>')
                    readSub = false;
                if (parts[i].substr(j, 1) == ' ' || parts[i].substr(j, 1) == '>')
                    readTag = false;


                if (readSub == true)
                    subpart = subpart + parts[i].substr(j, 1);
                if (readTag == true)
                    tagName = tagName + parts[i].substr(j, 1);
            }

            if (this.IsSupportedTag(tagName) == false) {
                parts[i] = parts[i].replace(subpart + '>', ' ');
                parts[i] = parts[i].replace('/' + tagName + '>', ' ');
            }
            else {
                parts[i] = '<' + parts[i];
                parts[i] = this.RemoveUnknownAttributes(subpart.replace(tagName, ''), parts[i]);
            }

        }

        var retValue = '';
        for (i = 0; i < parts.length; i++) {
            if (parts[i] != '')
                retValue = retValue + parts[i];
        }
        var tnewValue = new String(retValue);
        return tnewValue;
    },
    IsSupportedTag: function (tag) {
        var globalTagNames = new Array('big', 'br', 'a', 'img', 'table', 'td', 'tr', 'th', 'b', 'strong', 'div', 'u', 'small', 'em', 'i', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'caption', 'address', 'cite', 'code', 'dl', 'dd', 'dt', 'dfn', 'ul', 'li', 'object', 'param', 'embed', 'pre', 'q', 'samp', 'th', 'var', 'p', 'span', 'select', 'option', 'input', 'form');
        var tagName = new String(tag);
        tagName = tagName.toLowerCase();
        tagName = tagName.replace('/', '');

        for (var i = 0; i < globalTagNames.length; i++) {

            if (globalTagNames[i] == tagName) {

                return true;
            }
        }
        return false;

    },
    IsSupportedAttribute: function (atr) {

        var globalAttributeNames = new Array('src', 'id', 'align', 'title', 'alt', 'border', 'height', 'width', 'columns', 'href', 'style', 'class', 'cite', ' style', ' style ', 'style ', 'type', 'name', 'value', 'action');
        var atrParser = new String(atr);
        atrParser = atrParser.toLowerCase();
        atrParser = atrParser.replace(' ', '');
        for (var i = 0; i < globalAttributeNames.length; i++) {
            if (globalAttributeNames[i] == atrParser) {

                return true;
            }
        }
        return false;
    },
    GetFormatCode: function (sCode) {
        var ts = new String(sCode);
        //Here we replace any custom tag namespace we have placed
        //with an empty string
        return ts;
    },
    RemoveUnknownAttributes: function (tag, originalContent) {
        tag = tag + ' ';
        var tagParser = new String(originalContent);
        var i = 0;
        var tagName = '';
        var subPart = '';
        var readTag = true;
        var readSub = true;
        var firstSpace = false;
        var equalCount = 0;
        var removeParts = [];
        var arrCount = 0;

        for (i = 0; i < tag.length; i++) {
            if (tag.substr(i, 1) == '=') {
                readTag = false;
                if (tag.substr(i + 1, 1) == ' ') {
                    firstSpace = true;
                }
            }
            if (readTag == false && tag.substr(i, 1) == '"')
                equalCount++;
            if (readTag == false && (tag.substr(i, 1) == ' ' || tag.substr(i, 1) == '>' ) && firstSpace == false) {
                if (equalCount == 0 || (equalCount % 2) == 0)
                    readSub = false;
            }
            if (readSub == true)
                subPart = subPart + tag.substr(i, 1);
            if (readTag == true)
                tagName = tagName + tag.substr(i, 1);
            if (readSub == false) {
                if (this.IsSupportedAttribute(tagName) == false) {
                    removeParts[arrCount] = subPart;
                    arrCount++;
                }

                subPart = '';
                tagName = '';
                readSub = true;
                readTag = true;
            }
            if ((equalCount % 2) == 0) {
                equalCount = 0;

            }
            firstSpace = false;
        }

        for (i = 0; i < removeParts.length; i++) {
            tagParser = tagParser.replace(removeParts[i], '');
        }
        return tagParser;
    },
    ExecuteUndoCommand: function (sCommand, html) {
        var result = false;
        if (sCommand == RBMEditor.EditorCommands.NEWLINE)
            return result;

        if (RBM.Exists(html.indexOf)) {

            var tag = this.GetTag(sCommand);
            if (!RBM.StringContains(html.toLowerCase(), '<' + sCommand))
                return result;
            if (sCommand == RBMEditor.EditorCommands.BOLD) {
                document.execCommand('Bold', false, null);
                result = true;

            }
            else if (sCommand == RBMEditor.EditorCommands.ITALIC || sCommand == RBMEditor.EditorCommands.EM) {
                document.execCommand('Italic', false, null);
                result = true;
            }
            else {
                document.execCommand('RemoveFormat', false, null);
                result = true;
            }
        }
        return result;
    },
    GetTag: function (sCommand) {
        return '<' + sCommand + '>';
    },
    ExecuteCommand: function (sCommand, id, value, name, isControl) {
        var selection = RBM.GetSelectedRange(this.controlContent);

        if (selection == null)
            return;
        var html = this.GetHTMLFromSelection(selection);

        var undo = this.ExecuteUndoCommand(sCommand, new String(html));

        if (!undo) {
            var element = this.CreateNewNode(sCommand, id, value, html);
            if (element != null)
                this.SetHTMLFromSelection(selection, element);

        }
    },
    GetHTMLFromSelection: function (selection) {
        var ret = "";
        if (RBM.BrowserType.IE) {
            ret = selection.htmlText;
        }
        else {
            var tempDiv = document.createElement("span");
            var clonedFragment = selection.cloneContents();
            if (RBM.Exists(clonedFragment)) {
                tempDiv.appendChild(clonedFragment);
                ret = tempDiv.innerHTML;
            }
        }
        return ret;
    },
    // writes a marker node on a range and returns the node.
    WriteMarkerNode: function (rng) {
        var id = this.controlContent.document.uniqueID;
        var html = "<span id='" + id + "'></span>";
        rng.pasteHTML(html);
        var node = this.controlContent.document.getElementById(id);
        return node;
    },
    CreateNewNode: function (sCommand, id, value, html) {
        if (sCommand != null) {
            var element = document.createElement(sCommand);

            if (id != null && value != null)
                RBM.SetAttribute(element, id, value);
            if (RBM.Exists(html)) {
                element.innerHTML = html;
            }
            return element;
        }
        return null;
    },
    SetHTMLFromSelection: function (selectedRange, node) {
        if (selectedRange != null) {
            if (RBM.BrowserType.IE) {

                var marker = this.WriteMarkerNode(selectedRange);
                marker.appendChild(node);
                marker.removeNode(); // removes node but not children
            }
            else {

                selectedRange.deleteContents();
                if (RBM.IsTextNode(selectedRange.startContainer)) {
                    var refNode = RBM.RightPart(selectedRange.startContainer, selectedRange.startOffset)
                    refNode.parentNode.insertBefore(node, refNode);
                } else {
                    if (selectedRange.startOffset == selectedRange.startContainer.childNodes.length) {
                        refNode.parentNode.appendChild(node);
                    } else {
                        var refNode = selectedRange.startContainer.childNodes[selectedRange.startOffset];
                        refNode.parentNode.insertBefore(node, refNode);
                    }
                }
            }
        }
    }
}