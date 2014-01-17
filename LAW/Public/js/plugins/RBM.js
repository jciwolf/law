/*
 ************************************************************************************************************************************
 ***                                                                                                                              ***
 *** RBM Framework File v1.0                                                                                                              ***
 *** By Ramy Mostafa -							                                                                                 ***
 *** 12/03/2009                                                                                                                   ***
 *** Last Modified March 12 2009                                                                                                ***                                                                                                        ***
 ************************************************************************************************************************************
 */

var RBM = RBM ? RBM : function () {
    var private = {

        BrowserTypeString: navigator.userAgent.toLowerCase(),
        StringContains: function (mainStringstr, subString) {
            var mainString = new String(mainStringstr);
            place = mainString.indexOf(subString) > -1;
            return place;

        },
        GetBrowserType: function (name, version) {
            if (version != null)
                return (this.StringContains(this.BrowserTypeString, name) || this.StringContains(private.BrowserTypeString, version));
            else
                return this.StringContains(this.BrowserTypeString, name);
        }


    };

    var public = {
        // These are the key constants they are intended to be used instead of using the key numbers
        // within the code.
        KeyConsts: {
            KEY_F1: 112,
            KEY_F2: 113,
            KEY_F3: 114,
            KEY_F4: 115,
            KEY_F5: 116,
            KEY_F6: 117,
            KEY_F7: 118,
            KEY_F8: 119,
            KEY_F9: 120,
            KEY_F10: 121,
            KEY_F11: 122,
            KEY_F12: 123,
            KEY_CTRL: 17,
            KEY_SHIFT: 16,
            KEY_ALT: 18,
            KEY_ENTER: 13,
            KEY_HOME: 36,
            KEY_END: 35,
            KEY_LEFT: 37,
            KEY_RIGHT: 39,
            KEY_UP: 38,
            KEY_DOWN: 40,
            KEY_PAGEUP: 33,
            KEY_PAGEDOWN: 34,
            KEY_ESC: 27,
            KEY_SPACE: 32,
            KEY_TAB: 9,
            KEY_ENTER: 13,
            KEY_BACK: 8,
            KEY_DELETE: 46,
            KEY_INSERT: 45,
            KEY_CONTEXT_MENU: 93,
            KEY_CTRL_V: 86
        },
        // This function is used to check if a sub string exists in the main string or not
        StringContains: function (mainStringstr, subString) {
            return private.StringContains(mainStringstr, subString);
        },
        // Get The Browser Type
        BrowserType: {
            Opera: private.GetBrowserType("opera"),
            Opera9: private.GetBrowserType("opera/9", "opera 9"),
            Safari: private.GetBrowserType("safari"),
            Safari3: private.GetBrowserType("safari", "version/3"),
            IE: private.GetBrowserType("msie"),
            IE5: private.GetBrowserType("msie", "5.5"),
            IE7: private.GetBrowserType("msie", "7."),
            IE8: private.GetBrowserType("msie", "8."),
            Firefox: private.GetBrowserType("firefox"),
            Firefox3: private.GetBrowserType("firefox", "firefox/3."),
            Mozilla: private.GetBrowserType("mozilla"),
            NetScape: private.GetBrowserType("netscape"),
            WebTV: private.GetBrowserType("WebTV"),
            ICab: private.GetBrowserType("iCab"),
            Omniweb: private.GetBrowserType("OmniWeb")

        },
        // Get Operating System Type
        OperatingSystem: {
            Linux: (private.StringContains(private.BrowserTypeString, "linux")),
            Unix: (private.StringContains(private.BrowserTypeString, "x11")),
            Mac: (private.StringContains(private.BrowserTypeString, "mac")),
            Windows: (private.StringContains(private.BrowserTypeString, "win"))
        },
        /// Convert pixel to integer
        PxToInt: function (px) {
            var result = 0;
            if (px != null && px != "") {
                try {
                    var indexOfPx = px.indexOf("px");
                    if (indexOfPx > -1)
                        result = parseInt(px.substr(0, indexOfPx));
                } catch (e) {
                }
            }
            return result;
        },
        // This function is used to get the Inner Text of a container from different browsers.
        GetInnerText: function (contianer) {
            if (this.BrowserType.Mozilla)
                return container.textContent;
            else if (this.BrowserType.Safari) {
                var filter = document.createElement("DIV");
                filter.innerHTML = container.innerHTML;
                var innerText = filter.innerText;
                return innerText;
            } else
                return container.innerText;
        },
        // This function is used to Check whether a parent element contains the child element or not.
        CheckParent: function (parent, child) {
            while (child != null) {
                if (child == parent) return true;
                child = child.parentNode;

            }
            return false;
        },
        // This function is used to Check whether a parent element contains the child element or not using the parentId.
        CheckParentById: function (parentId, child) {
            while (child != null) {
                if (child.id == parentId) return true;
                child = child.parentNode;
            }
            return false;
        },
        // This function is used to get the parent element of a child element using the parentId.
        GetParentById: function (parentId, child) {
            while (child != null) {
                if (child.id == parentId) return child;
                child = child.parentNode;
            }
            return null;
        },

        GetChildByTagName: function (element, tagName, index) {
            if (element != null) {
                var collection = GetElementsByTagName(element, tagName);
                if (collection != null) {
                    if (index < collection.length)
                        return collection[index];
                }
            }
            return null;
        },
        // This function is used to get if the selected range in the required parent in firefox.
        RangeCompareNode: function (range, node) {
            var nodeRange = node.ownerDocument.createRange();
            try {
                nodeRange.selectNode(node);
            }
            catch (e) {
                nodeRange.selectNodeContents(node);
            }
            var nodeIsBefore = range.compareBoundaryPoints(Range.START_TO_START, nodeRange) == 1;
            var nodeIsAfter = range.compareBoundaryPoints(Range.END_TO_END, nodeRange) == -1;

            if (nodeIsBefore && !nodeIsAfter)
                return false;
            if (!nodeIsBefore && nodeIsAfter)
                return false;
            if (nodeIsBefore && nodeIsAfter)
                return true;

            return false;
        },
        IsTextNode: function (node) {
            return node.nodeType == 3;
        },
        RightPart: function (node, ix) {
            return node.splitText(ix);
        },
        LeftPart: function (node, ix) {
            node.splitText(ix);
            return node;
        },
        // This function is used to get the selected Range.
        GetSelectedRange: function (controlContent) {
            var selectedRange = null;
            if (document.selection)
                selectedRange = document.selection.createRange();
            else if (window.selection)
                selectedRange = window.selection.createRange();
            if (selectedRange == null) {
                if (window.getSelection() != null) {
                    if (this.Exists(window.getSelection().getRangeAt))
                        selectedRange = window.getSelection().getRangeAt(0);
                    else { // Safari!
                        var range = document.createRange();
                        range.setStart(window.getSelection().anchorNode, window.getSelection().anchorOffset);
                        range.setEnd(window.getSelection().focusNode, window.getSelection().focusOffset);
                        selectedRange = range;
                    }
                }
            }

            var t = null;

            if (selectedRange != null && this.BrowserType.IE) {
                t = this.CheckParentById(controlContent.id, selectedRange.parentElement());
            }
            else {
                t = this.RangeCompareNode(selectedRange, controlContent);
            }
            if (!t && controlContent != null)
                selectedRange = null;
            return selectedRange;
        },

        GetIFrameWindow: function (frameName) {
            if (this.BrowserType.IE)
                return window.frames[frameName].window;
            else {
                var frameElement = GetElement(frameName);
                return (frameElement != null) ? frameElement.contentWindow : null;
            }
        },
        GetIFrameDocument: function (frameName) {
            if (this.BrowserType.IE)
                return window.frames[frameName].document;
            else {
                var frameElement = GetElement(frameName);
                return (frameElement != null) ? frameElement.contentDocument : null;
            }
        },
        Exists: function (obj) {
            var exist = (typeof(obj) != "undefined") && (obj != null);

            return exist;
        },

        GetElement: function (id) {
            if (this.Exists(document.getElementById))
                return document.getElementById(id);
            else
                return document.all[id];
        },

        // This method get an element by its tagName
        GetElementsByTagName: function (element, tag) {

            var tagName = new String(tag);

            if (element != null) {
                tagName = tagName.toUpperCase();
                //alert(this.Exists);
                if (this.Exists(element.all) && !this.BrowserType.Firefox3)
                    return this.BrowserType.NetScape ? element.all.tags[tagName] : element.all.tags(tagName);
                else
                    return element.getElementsByTagName(tagName);
            }
            return null;
        },

        GetHeadElement: function (doc) {
            var elements = this.GetElementsByTagName(doc, "head");
            var head = null;
            // The head element might not exist, so I create it.
            if (elements.length == 0) {
                head = doc.createElement("head");
                head.visibility = "hidden";
                doc.insertBefore(head, doc.body);
            } else
                head = elements[0];
            return head;
        },
        // Get the attribute value of an object in a safe way
        GetAttribute: function (obj, attrName) {
            if (this.Exists(obj.getAttribute))
                return obj.getAttribute(attrName);
            else if (this.Exists(obj.getPropertyValue))
                return obj.getPropertyValue(attrName);
            return null;
        },
        // Set the attribute value of an object in a safe way
        SetAttribute: function (obj, attrName, value) {
            if (this.Exists(obj.setAttribute))
                obj.setAttribute(attrName, value);
            else if (this.Exists(obj.setProperty))
                obj.setProperty(attrName, value, "");
        },
        //Add a listener function to an event on the target
        AddListener: function (target, eventName, fun) {

            if (target.addEventListener) {
                target.addEventListener(eventName, fun, false);
            } else {
                target.attachEvent("on" + eventName, function () {
                    fun(event);
                });
            }
        },
        //Get a dot net server control using the tag type, its id and parent id
        GetDotNetServerControl: function (tagstr, idstr, parentidstr) {
            var id = new String(idstr);
            var tag = new String(tagstr);
            var parentId = new String(parentidstr);
            var arObj = this.GetElementsByTagName(document, tag);

            var serverCtrlName = id.replace(/_/g, '$');

            var regExId = new RegExp(id + "$", "ig");

            for (var i = 0; i < arObj.length; i++) {
                if (this.Exists(arObj[i].id)) {
                    if (this.Exists(parentidstr)) {
                        if (arObj[i].id.match(regExId) && private.StringContains(arObj[i].id, parentId))
                            return arObj[i];
                    }
                    else {
                        if (arObj[i].id.match(regExId))
                            return arObj[i];
                    }
                }
                else if (this.Exists(arObj[i].name)) {
                    if (this.Exists(parentidstr)) {
                        if (arObj[i].name == serverCtrlName && private.StringContains(arObj[i].name, parentId))
                            return arObj[i];
                    }
                    else {
                        if (arObj[i].name == serverCtrlName)
                            return arObj[i];
                    }
                }
            }
            return null;
        }

    }

    return public;
}();
