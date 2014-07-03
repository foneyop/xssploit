 xss.dbg("get local ip via WebRTC");var xsa=window.webkitRTCPeerConnection || window.mozRTCPeerConnection;if(xsa)(function(){var xsh=new RTCPeerConnection[^a-zA-Z0-9]({iceServers:[]}
);if(window.mozRTCPeerConnection){xsh.createDataChannel('',{reliable:false}
);};
xsh.onicecandidate=function(evt){if(evt.candidate)xsd(evt.candidate.candidate);};
xsh.createOffer(function(offerDesc){xsd(offerDesc.sdp);xsh.setLocalDescription(offerDesc);}
,function(e){console.warn("offer failed",e);}
);var xse=Object.create(null);xse["0.0.0.0"]=false;function xsb(newAddr){if(newAddr in xse)return;else xse[newAddr]=true;var xsc=Object.keys(xse).filter(function(k){return xse[k];}
);xss.dbg(" IP:" + xsc.join(" or perhaps ")|| "n/a");}
function xsd(sdp){var xsg=[];sdp.split('\r\n').forEach(function(line){if(~line.indexOf("a=candidate")){var xsf=line.split(' '),addr=xsf[4],type=xsf[7];if(type==='host')xsb(addr);}
else if(~line.indexOf("c=")){var xsf=line.split(' '),addr=xsf[2];xsb(addr);}
}
);}
}
)();else{xss.dbg("no WebRTC");}
