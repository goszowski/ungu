var bV=parseInt(navigator.appVersion);
var NS4=(document.layers) ? true : false;
var IE4=((document.all)&&(bV>=4)) ? true : false;
var capable = (NS4 || IE4) ? true : false;

function getIndex(el) {
    ind = null;
    for (i=0; i< document.layers.length; i++) {
        whichEl = document.layers[i];
        if (whichEl.id == el) {
            ind = i;
            break;
        }
    }
    return ind;
}

function arrange() {
	alert("arrange()")
    nextY = document.layers[firstInd].pageY + document.layers[firstInd].document.height;
    for (i=firstInd+1; i< document.layers.length; i++) {
	alert("i="+i)
        whichEl = document.layers[i];
        if (whichEl.visibility != "hide") {
            whichEl.pageY = nextY;
            nextY += whichEl.document.height;
        }
    }
}

function initIt() {
    bV=parseInt(navigator.appVersion);
    NS4=(document.layers) ? true : false;
    IE4=((document.all)&&(bV>=4)) ? true : false;
    capable = (NS4 || IE4) ? true : false;

	preInit();
    if (NS4) {
        for (i=0; i<document.layers.length; i++) {
            whichEl = document.layers[i];
            if (whichEl.id.indexOf("s") != -1) {
                whichEl.visibility = "show";
            }
            if (whichEl.id.indexOf("h") != -1) {
                whichEl.visibility = "hide";
            }
        }
        arrange();
    } else if(IE4) {
        tempColl = document.all.tags("DIV");
        for (i=0; i<tempColl.length; i++) {
            //if (tempColl(i).className == "js_tree_el_hidden") tempColl(i).style.display = "none";
            whichEl = tempColl(i)
            if (whichEl.id.indexOf("s") != -1) {
                whichEl.style.display = "block";
            }
            if (whichEl.id.indexOf("h") != -1) {
                whichEl.style.display = "none";
            }
        }
    }
}

function expandIt(elname) {
    if (!capable) return;

    if (IE4) {
        var El = document.all.namedItem(elname)
        var Im = document.all.namedItem("img" + elname)
    } else if(NS4) {
        El = eval("document." + elname);
        var Im = eval("document." + elname + ".document.images.imEx");
        //alert(Im);return;
    }

    if(Im.src.indexOf ("minus") != -1) {
        vis = "hide";
        Im.src = "/admin/_img/tree_plus.gif";
    }
    else {
        vis = "show"
        Im.src = "/admin/_img/tree_minus.gif";
    }
    rec_refresh_subtree(elname, vis);

    if (IE4) {
        window.event.cancelBubble = true ;
    } else if(NS4) {
        arrange();
    }
}

function isDirectSubElement(subname, elname) {
    if( ( subname.indexOf(elname) != -1 ) && (elname != subname) ) {
        if( subname.substr(elname.length+1).indexOf("_") == -1 ) {
            return true;
        }
    }
    return false;
}

function rec_refresh_subtree(elname, v) {
    if(IE4) {
        var El = document.all.namedItem(elname);
        var Im = document.all.namedItem("img" + elname);
        tempColl = document.all.tags("DIV");
        for (i=0; i<tempColl.length; i++) {
            var whichEl = tempColl(i);
            if(isDirectSubElement(whichEl.id, elname)) {
                if(v == "show") {
                    whichEl.style.display = "block";
                    var whichIm = document.all.namedItem("img" + whichEl.id);
                    if ( whichIm.src.indexOf ("minus") != -1 ) {
                        rec_refresh_subtree(whichEl.id, v);
                    }
                } else {
                    whichEl.style.display = "none";
                    rec_refresh_subtree(whichEl.id, v);
                }
            }
        }
    }
    else {
        var El = eval("document." + elname);
        var Im = eval("document." + elname + ".document.images['imEx']");

        for (var i=0; i<document.layers.length; i++) {
            var whichEl = document.layers[i];
            if(isDirectSubElement(whichEl.id, elname)) {
                if(v == "show") {
                    whichEl.visibility = "show";
                    var whichIm = eval("document." + whichEl.id + ".document.images['imEx']");
                    if ( whichIm.src.indexOf ("minus") != -1 ) {
                        rec_refresh_subtree(whichEl.id, v);
                    }
                } else {
                    rec_refresh_subtree(whichEl.id, v);
                    whichEl.visibility = "hide";
                }
            }
        }
    }
}

with (document) {
    write("<style type='text/css'>");
    if (NS4) {
        write(".jstreeel{position:absolute; visibility:hidden}");
    } else if(IE4) {
        write(".jstreeel{font-family: Verdana, Arial, Helvetica, sans-serif; color: #000000; text-decoration:none;}");
    }
    write("</style>");
}
