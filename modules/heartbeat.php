if (window.xssinterval) { window.clearInterval(xssinterval); }
var xssinterval = window.setInterval(hb, <?=$heartbeat?>);
