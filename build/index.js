(window.webpackJsonp_blythefamily=window.webpackJsonp_blythefamily||[]).push([[1],{6:function(e,t,r){},7:function(e,t,r){},8:function(e,t,r){}}]),function(e){function t(t){for(var n,o,l=t[0],c=t[1],s=t[2],p=0,f=[];p<l.length;p++)o=l[p],Object.prototype.hasOwnProperty.call(a,o)&&a[o]&&f.push(a[o][0]),a[o]=0;for(n in c)Object.prototype.hasOwnProperty.call(c,n)&&(e[n]=c[n]);for(u&&u(t);f.length;)f.shift()();return i.push.apply(i,s||[]),r()}function r(){for(var e,t=0;t<i.length;t++){for(var r=i[t],n=!0,l=1;l<r.length;l++){var c=r[l];0!==a[c]&&(n=!1)}n&&(i.splice(t--,1),e=o(o.s=r[0]))}return e}var n={},a={0:0},i=[];function o(t){if(n[t])return n[t].exports;var r=n[t]={i:t,l:!1,exports:{}};return e[t].call(r.exports,r,r.exports,o),r.l=!0,r.exports}o.m=e,o.c=n,o.d=function(e,t,r){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(o.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var n in e)o.d(r,n,function(t){return e[t]}.bind(null,n));return r},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="";var l=window.webpackJsonp_blythefamily=window.webpackJsonp_blythefamily||[],c=l.push.bind(l);l.push=t,l=l.slice();for(var s=0;s<l.length;s++)t(l[s]);var u=c;i.push([9,1]),r()}([function(e,t){e.exports=window.wp.blockEditor},function(e,t){e.exports=window.wp.i18n},function(e,t){e.exports=window.wp.blocks},function(e,t,r){var n;!function(){"use strict";var r={}.hasOwnProperty;function a(){for(var e=[],t=0;t<arguments.length;t++){var n=arguments[t];if(n){var i=typeof n;if("string"===i||"number"===i)e.push(n);else if(Array.isArray(n)){if(n.length){var o=a.apply(null,n);o&&e.push(o)}}else if("object"===i)if(n.toString===Object.prototype.toString)for(var l in n)r.call(n,l)&&n[l]&&e.push(l);else e.push(n.toString())}}return e.join(" ")}e.exports?(a.default=a,e.exports=a):void 0===(n=function(){return a}.apply(t,[]))||(e.exports=n)}()},function(e,t){e.exports=window.wp.element},function(e,t){},,,,function(e,t,r){"use strict";r.r(t);var n=r(2),a=(r(6),r(3),r(1)),i=r(0);r(4);function o(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){var r=e&&("undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"]);if(null!=r){var n,a,i=[],_n=!0,o=!1;try{for(r=r.call(e);!(_n=(n=r.next()).done)&&(i.push(n.value),!t||i.length!==t);_n=!0);}catch(e){o=!0,a=e}finally{try{_n||null==r.return||r.return()}finally{if(o)throw a}}return i}}(e,t)||function(e,t){if(e){if("string"==typeof e)return l(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?l(e,t):void 0}}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function l(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}var c={from:[{type:"block",blocks:["core/paragraph"],transform:function(e){for(var t="string"==typeof e.content?e.content.trim().split("<br>"):"",r="",a=t[0].toLowerCase(),i=null,l=0,c=Object.entries({v1:"Verse 1",v2:"Verse 2",v3:"Verse 3",v4:"Verse 4",ch:"Chorus",bridge:"Bridge"});l<c.length;l++){var s=o(c[l],2),u=s[0],p=s[1];if(a.startsWith(p.toLowerCase())?i=p.length:a.startsWith(u)&&(i=u.length),i){r=p,t[0]=t[0].substr(i);break}}for(var f=[],h=0;h<t.length;h++)t[h].match(/[a-z\d ]+/g)&&f.push({type:"li",props:{children:[t[h]]}});return Object(n.createBlock)("blythe/lyrics-section",{title:r,phrases:f})}}]};Object(n.registerBlockType)("blythe/lyrics-section",{attributes:{title:{type:"array",source:"children",selector:".title"},phrases:{type:"array",source:"children",selector:".phrases"}},edit:function(e){var t=e.attributes,r=e.setAttributes,n=(e.mergeBlocks,e.onReplace,e.mergedStyle,e.clientId,t.title),o=t.phrases;return React.createElement("div",Object(i.useBlockProps)(),React.createElement(i.BlockControls,null),React.createElement(i.RichText,{identifier:"content",tagName:"span",multiline:!1,value:n,onChange:function(e){return r({title:e})},"aria-label":Object(a.__)("Section title text"),placeholder:Object(a.__)("Section Title (Verse, Chorus, etc)"),className:"title",keepPlaceholderOnFocus:!0}),React.createElement(i.RichText,{tagName:"ul",multiline:"li",className:"phrases",placeholder:Object(a.__)("Lyric phrases...","blythe"),value:o,onChange:function(e){return r({phrases:e})},keepPlaceholderOnFocus:!0}))},save:function(e){var t=e.attributes,r=t.title,n=t.phrases;return React.createElement("div",{className:"wp-block lyrics-section"},React.createElement(i.RichText.Content,{tagName:"span",className:"title",value:r}),React.createElement(i.RichText.Content,{tagName:"ul",multiline:"li",className:"phrases",value:n}))},transforms:c}),r(7);var s=r(5),u=r.n(s);Object(n.registerBlockType)("blythe/episode-info",{attributes:{description:{type:"array",source:"children",selector:".description"},scriptures:{type:"array",source:"children",selector:".scriptures"}},edit:function(e){var t=e.attributes,r=e.setAttributes,n=(e.mergeBlocks,e.onReplace,e.mergedStyle,e.clientId,t.description),o=t.scriptures;return React.createElement("div",Object(i.useBlockProps)(),React.createElement(i.BlockControls,null),React.createElement("h3",null,"Episode Description:"),React.createElement(i.RichText,{identifier:"description",tagName:"p",multiline:!1,value:n,onChange:function(e){return r({description:e})},"aria-label":Object(a.__)("Episode description"),placeholder:Object(a.__)("Episode description..."),className:"description",keepPlaceholderOnFocus:!0}),React.createElement("h3",null,"Scripture Reading:"),React.createElement(i.RichText,{tagName:"ul",multiline:"li",className:"scriptures",placeholder:Object(a.__)("Example: Genesis 1:2-4","blythe"),value:o,onChange:function(e){return r({scriptures:e})},keepPlaceholderOnFocus:!0}))},save:function(e){var t=e.attributes,r=t.description,n=t.scriptures;return React.createElement("div",{className:"wp-block episode-info"},React.createElement(i.RichText.Content,{tagName:"p",className:"description",value:r}),React.createElement("h3",null,"Scripture Reading:"),React.createElement(i.RichText.Content,{tagName:"ul",multiline:"li",className:"scriptures",value:n}))},transforms:u.a}),r(8)}]);