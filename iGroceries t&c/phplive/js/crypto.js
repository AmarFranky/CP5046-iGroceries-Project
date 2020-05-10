/* 
	
MIT License

Copyright (c) 2017 Etienne Martin

https://github.com/etienne-martin/WebCrypto.swift

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/

"use strict";function initWebCrypto(){function a(a,b){var c=a[0],d=a[1],e=a[2],f=a[3];c+=(d&e|~d&f)+b[0]-680876936|0,c=(c<<7|c>>>25)+d|0,f+=(c&d|~c&e)+b[1]-389564586|0,f=(f<<12|f>>>20)+c|0,e+=(f&c|~f&d)+b[2]+606105819|0,e=(e<<17|e>>>15)+f|0,d+=(e&f|~e&c)+b[3]-1044525330|0,d=(d<<22|d>>>10)+e|0,c+=(d&e|~d&f)+b[4]-176418897|0,c=(c<<7|c>>>25)+d|0,f+=(c&d|~c&e)+b[5]+1200080426|0,f=(f<<12|f>>>20)+c|0,e+=(f&c|~f&d)+b[6]-1473231341|0,e=(e<<17|e>>>15)+f|0,d+=(e&f|~e&c)+b[7]-45705983|0,d=(d<<22|d>>>10)+e|0,c+=(d&e|~d&f)+b[8]+1770035416|0,c=(c<<7|c>>>25)+d|0,f+=(c&d|~c&e)+b[9]-1958414417|0,f=(f<<12|f>>>20)+c|0,e+=(f&c|~f&d)+b[10]-42063|0,e=(e<<17|e>>>15)+f|0,d+=(e&f|~e&c)+b[11]-1990404162|0,d=(d<<22|d>>>10)+e|0,c+=(d&e|~d&f)+b[12]+1804603682|0,c=(c<<7|c>>>25)+d|0,f+=(c&d|~c&e)+b[13]-40341101|0,f=(f<<12|f>>>20)+c|0,e+=(f&c|~f&d)+b[14]-1502002290|0,e=(e<<17|e>>>15)+f|0,d+=(e&f|~e&c)+b[15]+1236535329|0,d=(d<<22|d>>>10)+e|0,c+=(d&f|e&~f)+b[1]-165796510|0,c=(c<<5|c>>>27)+d|0,f+=(c&e|d&~e)+b[6]-1069501632|0,f=(f<<9|f>>>23)+c|0,e+=(f&d|c&~d)+b[11]+643717713|0,e=(e<<14|e>>>18)+f|0,d+=(e&c|f&~c)+b[0]-373897302|0,d=(d<<20|d>>>12)+e|0,c+=(d&f|e&~f)+b[5]-701558691|0,c=(c<<5|c>>>27)+d|0,f+=(c&e|d&~e)+b[10]+38016083|0,f=(f<<9|f>>>23)+c|0,e+=(f&d|c&~d)+b[15]-660478335|0,e=(e<<14|e>>>18)+f|0,d+=(e&c|f&~c)+b[4]-405537848|0,d=(d<<20|d>>>12)+e|0,c+=(d&f|e&~f)+b[9]+568446438|0,c=(c<<5|c>>>27)+d|0,f+=(c&e|d&~e)+b[14]-1019803690|0,f=(f<<9|f>>>23)+c|0,e+=(f&d|c&~d)+b[3]-187363961|0,e=(e<<14|e>>>18)+f|0,d+=(e&c|f&~c)+b[8]+1163531501|0,d=(d<<20|d>>>12)+e|0,c+=(d&f|e&~f)+b[13]-1444681467|0,c=(c<<5|c>>>27)+d|0,f+=(c&e|d&~e)+b[2]-51403784|0,f=(f<<9|f>>>23)+c|0,e+=(f&d|c&~d)+b[7]+1735328473|0,e=(e<<14|e>>>18)+f|0,d+=(e&c|f&~c)+b[12]-1926607734|0,d=(d<<20|d>>>12)+e|0,c+=(d^e^f)+b[5]-378558|0,c=(c<<4|c>>>28)+d|0,f+=(c^d^e)+b[8]-2022574463|0,f=(f<<11|f>>>21)+c|0,e+=(f^c^d)+b[11]+1839030562|0,e=(e<<16|e>>>16)+f|0,d+=(e^f^c)+b[14]-35309556|0,d=(d<<23|d>>>9)+e|0,c+=(d^e^f)+b[1]-1530992060|0,c=(c<<4|c>>>28)+d|0,f+=(c^d^e)+b[4]+1272893353|0,f=(f<<11|f>>>21)+c|0,e+=(f^c^d)+b[7]-155497632|0,e=(e<<16|e>>>16)+f|0,d+=(e^f^c)+b[10]-1094730640|0,d=(d<<23|d>>>9)+e|0,c+=(d^e^f)+b[13]+681279174|0,c=(c<<4|c>>>28)+d|0,f+=(c^d^e)+b[0]-358537222|0,f=(f<<11|f>>>21)+c|0,e+=(f^c^d)+b[3]-722521979|0,e=(e<<16|e>>>16)+f|0,d+=(e^f^c)+b[6]+76029189|0,d=(d<<23|d>>>9)+e|0,c+=(d^e^f)+b[9]-640364487|0,c=(c<<4|c>>>28)+d|0,f+=(c^d^e)+b[12]-421815835|0,f=(f<<11|f>>>21)+c|0,e+=(f^c^d)+b[15]+530742520|0,e=(e<<16|e>>>16)+f|0,d+=(e^f^c)+b[2]-995338651|0,d=(d<<23|d>>>9)+e|0,c+=(e^(d|~f))+b[0]-198630844|0,c=(c<<6|c>>>26)+d|0,f+=(d^(c|~e))+b[7]+1126891415|0,f=(f<<10|f>>>22)+c|0,e+=(c^(f|~d))+b[14]-1416354905|0,e=(e<<15|e>>>17)+f|0,d+=(f^(e|~c))+b[5]-57434055|0,d=(d<<21|d>>>11)+e|0,c+=(e^(d|~f))+b[12]+1700485571|0,c=(c<<6|c>>>26)+d|0,f+=(d^(c|~e))+b[3]-1894986606|0,f=(f<<10|f>>>22)+c|0,e+=(c^(f|~d))+b[10]-1051523|0,e=(e<<15|e>>>17)+f|0,d+=(f^(e|~c))+b[1]-2054922799|0,d=(d<<21|d>>>11)+e|0,c+=(e^(d|~f))+b[8]+1873313359|0,c=(c<<6|c>>>26)+d|0,f+=(d^(c|~e))+b[15]-30611744|0,f=(f<<10|f>>>22)+c|0,e+=(c^(f|~d))+b[6]-1560198380|0,e=(e<<15|e>>>17)+f|0,d+=(f^(e|~c))+b[13]+1309151649|0,d=(d<<21|d>>>11)+e|0,c+=(e^(d|~f))+b[4]-145523070|0,c=(c<<6|c>>>26)+d|0,f+=(d^(c|~e))+b[11]-1120210379|0,f=(f<<10|f>>>22)+c|0,e+=(c^(f|~d))+b[2]+718787259|0,e=(e<<15|e>>>17)+f|0,d+=(f^(e|~c))+b[9]-343485551|0,d=(d<<21|d>>>11)+e|0,a[0]=c+a[0]|0,a[1]=d+a[1]|0,a[2]=e+a[2]|0,a[3]=f+a[3]|0}function b(a){var b,c=[];for(b=0;b<64;b+=4)c[b>>2]=a.charCodeAt(b)+(a.charCodeAt(b+1)<<8)+(a.charCodeAt(b+2)<<16)+(a.charCodeAt(b+3)<<24);return c}function c(c){var d,e,f,g,h,i,j=c.length,k=[1732584193,-271733879,-1732584194,271733878];for(d=64;d<=j;d+=64)a(k,b(c.substring(d-64,d)));for(c=c.substring(d-64),e=c.length,f=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],d=0;d<e;d+=1)f[d>>2]|=c.charCodeAt(d)<<(d%4<<3);if(f[d>>2]|=128<<(d%4<<3),d>55)for(a(k,f),d=0;d<16;d+=1)f[d]=0;return g=8*j,g=g.toString(16).match(/(.*?)(.{0,8})$/),h=parseInt(g[2],16),i=parseInt(g[1],16)||0,f[14]=h,f[15]=i,a(k,f),k}function d(a){var b,c="";for(b=0;b<4;b+=1)c+=x[a>>8*b+4&15]+x[a>>8*b&15];return c}function e(a){var b;for(b=0;b<a.length;b+=1)a[b]=d(a[b]);return a.join("")}function f(a){var b,c=[],d=a.length;for(b=0;b<d-1;b+=2)c.push(parseInt(a.substr(b,2),16));return String.fromCharCode.apply(String,c)}function g(a){return f(e(c(a)))}function h(a,b,c){null===b&&(b="");for(var d=[g(a+b)],e=16,f=1;e<c;++f,e+=16)d.push(g(d[f-1]+a+b));return d.join("").substr(0,c)}function i(a){if(this.data="",this.read=0,"string"==typeof a)this.data=a;else if(util.isArrayBuffer(a)||util.isArrayBufferView(a)){var b=new Uint8Array(a);try{this.data=String.fromCharCode.apply(null,b)}catch(a){for(var c=0;c<b.length;++c)this.putByte(b[c])}}else(a instanceof i||"object"==typeof a&&"string"==typeof a.data&&"number"==typeof a.read)&&(this.data=a.data,this.read=a.read);this._constructedStringLength=0,this.getBytes=function(a){var b;return a?(a=Math.min(this.length(),a),b=this.data.slice(this.read,this.read+a),this.read+=a):0===a?b="":(b=0===this.read?this.data:this.data.slice(this.read),this.clear()),b},this.length=function(){return this.data.length-this.read}}function j(a){return new i(a)}function k(){return q(window.crypto.getRandomValues(new Uint8Array(8)))}function l(a,b){var b=b||k(),c=h(a,b,48),d=j(c);return{key:d.getBytes(32),iv:d.getBytes(16),salt:b}}function m(a){if("function"==typeof a.callback)a.callback(a);else try{window.webkit.messageHandlers.scriptHandler.postMessage(a)}catch(a){}}function n(a){for(var b=a.toString(),c="",d=0;d<b.length;d+=2)c+=String.fromCharCode(parseInt(b.substr(d,2),16));return c}function o(a){for(var b=new Uint8Array(a),c=[],d=0;d<b.length;d++){var e=b[d].toString(16),f=("00"+e).slice(-2);c.push(f)}return c.join("")}function p(a){for(var b=new Uint8Array(a.length),c=0;c<a.length;c++)b[c]=a.charCodeAt(c);return b}function q(a){for(var b="",c=0;c<a.byteLength;c++)b+=String.fromCharCode(a[c]);return b}function r(a){return void 0!==a}function s(a){var b=a.length?a.length:256,c=a.callback;if(128!==b&&192!==b&&256!==b)return m({error:"invalidKeyLength",callback:c,func:"string"}),!1;y.generateKey({name:"AES-CBC",length:b},!0,["encrypt","decrypt"]).catch(function(a){m({error:a,callback:c,func:"string"})}).then(function(a){y.exportKey("raw",a).catch(function(a){m({error:a,callback:c,func:"string"})}).then(function(a){m({result:o(new Uint8Array(a)),callback:c,func:"string"})})})}function t(a){var b=a.length,c=a.callback;if(!r(b)||isNaN(parseInt(b)))return m({error:"invalidLength",callback:c,func:"string"}),!1;m({result:o(window.crypto.getRandomValues(new Uint8Array(b))),callback:c,func:"string"})}function u(a){var b=a.callback;if(!r(a.data))return m({error:"missingData",callback:b,func:"data"}),!1;var c=atob(a.data),d=a.password,e=a.key,f=a.iv,g=p(c);if(r(d)){if(0===d.length)return m({error:"invalidPasswordLength",callback:b,func:"data"}),!1;var h=l(d),e=p(h.key),f=p(h.iv),i=h.salt}else{if(!r(e)||!r(f))return m({error:"missingPasswordKeyOrIv",callback:b,func:"data"}),!1;if(e=p(n(e)),f=p(n(f)),32!==a.key.length&&48!==a.key.length&&64!==a.key.length)return m({error:"invalidKeyLength",callback:b,func:"data"}),!1;if(32!==a.iv.length)return m({error:"invalidIvLength",callback:b,func:"data"}),!1}y.importKey("raw",e,{name:"AES-CBC"},!1,["encrypt","decrypt"]).catch(function(a){m({error:a,callback:b,func:"data"})}).then(function(a){y.encrypt({name:"AES-CBC",iv:f},a,g).catch(function(a){m({error:a,callback:b,func:"data"})}).then(function(a){var c=q(new Uint8Array(a));i&&(c=z+i+c),c=btoa(c),m({result:c,callback:b,func:"data"})})})}function v(a){var b=a.callback;if(!r(a.data))return m({error:"missingData",callback:b,func:"data"}),!1;var c=atob(a.data),d=a.password,e=a.key,f=a.iv,g=p(c);if(c.substr(0,8)===z){var h=c.substr(8,8);g=p(c.substr(16,c.length))}if(r(d)){if(0===d.length)return m({error:"invalidPasswordLength",callback:b,func:"data"}),!1;var i=l(d,h),e=p(i.key),f=p(i.iv)}else{if(!r(e)||!r(f))return m({error:"missingPasswordKeyOrIv",callback:b,func:"data"}),!1;if(e=p(n(e)),f=p(n(f)),32!==a.key.length&&48!==a.key.length&&64!==a.key.length)return m({error:"invalidKeyLength",callback:b,func:"data"}),!1;if(32!==a.iv.length)return m({error:"invalidIvLength",callback:b,func:"data"}),!1}y.importKey("raw",e,{name:"AES-CBC"},!1,["encrypt","decrypt"]).catch(function(a){m({error:a,callback:b,func:"data"})}).then(function(a){y.decrypt({name:"AES-CBC",iv:f},a,g).catch(function(a){m({error:a,callback:b,func:"data"})}).then(function(a){m({result:btoa(q(new Uint8Array(a))),callback:b,func:"data"})})})}function w(a){var b=a.algorithm,c=p(atob(a.data)),d=a.callback;y.digest({name:b},c).catch(function(a){m({error:a,callback:d,func:"string"})}).then(function(a){var a=o(new Uint8Array(a));m({result:a,callback:d,func:"string"})})}var x=["0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"];if(window.crypto&&!window.crypto.subtle&&window.crypto.webkitSubtle)var y=window.crypto.webkitSubtle;else var y=window.crypto.subtle;var z="Salted__";window.WebCrypto={generateKey:s,generateRandomNumber:t,encrypt:u,decrypt:v,hash:w}}