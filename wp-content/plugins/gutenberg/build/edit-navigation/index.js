this.wp=this.wp||{},this.wp.editNavigation=function(e){var t={};function n(r){if(t[r])return t[r].exports;var o=t[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}return n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)n.d(r,o,function(t){return e[t]}.bind(null,o));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=409)}({0:function(e,t){!function(){e.exports=this.wp.element}()},1:function(e,t){!function(){e.exports=this.wp.i18n}()},13:function(e,t){!function(){e.exports=this.regeneratorRuntime}()},18:function(e,t){!function(){e.exports=this.wp.url}()},197:function(e,t,n){"use strict";var r=n(0),o=n(6),i=Object(r.createElement)(o.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},Object(r.createElement)(o.Path,{fillRule:"evenodd",d:"M10.289 4.836A1 1 0 0111.275 4h1.306a1 1 0 01.987.836l.244 1.466c.787.26 1.503.679 2.108 1.218l1.393-.522a1 1 0 011.216.437l.653 1.13a1 1 0 01-.23 1.273l-1.148.944a6.025 6.025 0 010 2.435l1.149.946a1 1 0 01.23 1.272l-.653 1.13a1 1 0 01-1.216.437l-1.394-.522c-.605.54-1.32.958-2.108 1.218l-.244 1.466a1 1 0 01-.987.836h-1.306a1 1 0 01-.986-.836l-.244-1.466a5.995 5.995 0 01-2.108-1.218l-1.394.522a1 1 0 01-1.217-.436l-.653-1.131a1 1 0 01.23-1.272l1.149-.946a6.026 6.026 0 010-2.435l-1.148-.944a1 1 0 01-.23-1.272l.653-1.131a1 1 0 011.217-.437l1.393.522a5.994 5.994 0 012.108-1.218l.244-1.466zM14.929 12a3 3 0 11-6 0 3 3 0 016 0z",clipRule:"evenodd"}));t.a=i},198:function(e,t,n){"use strict";var r="undefined"!=typeof crypto&&crypto.getRandomValues&&crypto.getRandomValues.bind(crypto)||"undefined"!=typeof msCrypto&&"function"==typeof msCrypto.getRandomValues&&msCrypto.getRandomValues.bind(msCrypto),o=new Uint8Array(16);function i(){if(!r)throw new Error("crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported");return r(o)}for(var c=[],a=0;a<256;++a)c[a]=(a+256).toString(16).substr(1);var u=function(e,t){var n=t||0,r=c;return[r[e[n++]],r[e[n++]],r[e[n++]],r[e[n++]],"-",r[e[n++]],r[e[n++]],"-",r[e[n++]],r[e[n++]],"-",r[e[n++]],r[e[n++]],"-",r[e[n++]],r[e[n++]],r[e[n++]],r[e[n++]],r[e[n++]],r[e[n++]]].join("")};t.a=function(e,t,n){var r=t&&n||0;"string"==typeof e&&(t="binary"===e?new Array(16):null,e=null);var o=(e=e||{}).random||(e.rng||i)();if(o[6]=15&o[6]|64,o[8]=63&o[8]|128,t)for(var c=0;c<16;++c)t[r+c]=o[c];return t||u(o)}},2:function(e,t){!function(){e.exports=this.lodash}()},25:function(e,t){!function(){e.exports=this.wp.hooks}()},3:function(e,t){!function(){e.exports=this.wp.components}()},31:function(e,t){!function(){e.exports=this.wp.apiFetch}()},38:function(e,t){!function(){e.exports=this.wp.coreData}()},4:function(e,t){!function(){e.exports=this.wp.data}()},409:function(e,t,n){"use strict";n.r(t),n.d(t,"initialize",(function(){return nt}));var r={};n.r(r),n.d(r,"getNavigationPostForMenu",(function(){return De}));var o={};n.r(o),n.d(o,"getNavigationPostForMenu",(function(){return Fe})),n.d(o,"hasResolvedNavigationPost",(function(){return Ge})),n.d(o,"getMenuItemForClientId",(function(){return Le}));var i={};function c(){return(c=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e}).apply(this,arguments)}function a(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}n.r(i),n.d(i,"createMissingMenuItems",(function(){return Qe})),n.d(i,"saveNavigationPost",(function(){return qe}));var u=n(0),s=n(2),l=(n(74),n(79)),p=n(9),f=n(1),b=n(31),d=n.n(b),m=n(18),v=n(62),O=n(25);function y(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=new Array(t);n<t;n++)r[n]=e[n];return r}function g(e,t){if(e){if("string"==typeof e)return y(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);return"Object"===n&&e.constructor&&(n=e.constructor.name),"Map"===n||"Set"===n?Array.from(e):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?y(e,t):void 0}}function j(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e)){var n=[],r=!0,o=!1,i=void 0;try{for(var c,a=e[Symbol.iterator]();!(r=(c=a.next()).done)&&(n.push(c.value),!t||n.length!==t);r=!0);}catch(e){o=!0,i=e}finally{try{r||null==a.return||a.return()}finally{if(o)throw i}}return n}}(e,t)||g(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}var h=n(3),_=n(5),E=n(13),w=n.n(E);function S(e,t,n,r,o,i,c){try{var a=e[i](c),u=a.value}catch(e){return void n(e)}a.done?t(u):Promise.resolve(u).then(r,o)}function P(e){return function(){var t=this,n=arguments;return new Promise((function(r,o){var i=e.apply(t,n);function c(e){S(i,r,o,c,a,"next",e)}function a(e){S(i,r,o,c,a,"throw",e)}c(void 0)}))}}var k=n(4);var I=n(38),x=n(8);function N(e){return function(e){if(Array.isArray(e))return y(e)}(e)||function(e){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e))return Array.from(e)}(e)||g(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function C(e){return{type:"API_FETCH",request:e}}function T(e){return{type:"GET_PENDING_ACTIONS",postId:e}}function D(e){return{type:"IS_PROCESSING_POST",postId:e}}function M(e){return{type:"GET_MENU_ITEM_TO_CLIENT_ID_MAPPING",postId:e}}function R(e){return{type:"RESOLVE_MENU_ITEMS",query:V(e)}}function A(e,t){for(var n=arguments.length,r=new Array(n>2?n-2:0),o=2;o<n;o++)r[o-2]=arguments[o];return{type:"DISPATCH",registryName:e,actionName:t,args:r}}var B={API_FETCH:function(e){var t=e.request;return d()(t)},SELECT:Object(k.createRegistryControl)((function(e){return function(t){var n,r=t.registryName,o=t.selectorName,i=t.args;return(n=e.select(r))[o].apply(n,N(i))}})),GET_PENDING_ACTIONS:Object(k.createRegistryControl)((function(e){return function(t){var n,r=t.postId;return(null===(n=F(e).processingQueue[r])||void 0===n?void 0:n.pendingActions)||[]}})),IS_PROCESSING_POST:Object(k.createRegistryControl)((function(e){return function(t){var n,r=t.postId;return!!(null===(n=F(e).processingQueue[r])||void 0===n?void 0:n.inProgress)}})),GET_MENU_ITEM_TO_CLIENT_ID_MAPPING:Object(k.createRegistryControl)((function(e){return function(t){var n=t.postId;return F(e).mapping[n]||{}}})),DISPATCH:Object(k.createRegistryControl)((function(e){return function(t){var n,r=t.registryName,o=t.actionName,i=t.args;return(n=e.dispatch(r))[o].apply(n,N(i))}})),RESOLVE_MENU_ITEMS:Object(k.createRegistryControl)((function(e){return function(t){var n=t.query;return e.__experimentalResolveSelect("core").getMenuItems(n)}}))},F=function(e){return e.stores["core/edit-navigation"].store.getState()},G=B;function L(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function U(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?L(Object(n),!0).forEach((function(t){a(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):L(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}var z=function(e){return"navigation-post-".concat(e)};function V(e){return{menus:e,per_page:-1}}function H(e){return w.a.mark((function t(n){var r,o,i;return w.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return r=n.id,t.next=3,D(r);case 3:if(!t.sent){t.next=8;break}return t.next=7,{type:"ENQUEUE_AFTER_PROCESSING",postId:r,action:e};case 7:return t.abrupt("return",{status:"pending"});case 8:return t.next=10,{type:"POP_PENDING_ACTION",postId:r,action:e};case 10:return t.next=12,{type:"START_PROCESSING_POST",postId:r};case 12:return t.prev=12,t.t0=e,t.next=16,{type:"SELECT",registryName:"core/edit-navigation",selectorName:"getNavigationPostForMenu",args:[n.meta.menuId]};case 16:return t.t1=t.sent,t.delegateYield((0,t.t0)(t.t1),"t2",18);case 18:return t.prev=18,t.next=21,{type:"FINISH_PROCESSING_POST",postId:r,action:e};case 21:return t.next=23,T(r);case 23:if(!(o=t.sent).length){t.next=27;break}return i=H(o[0]),t.delegateYield(i(n),"t3",27);case 27:return t.finish(18);case 28:case"end":return t.stop()}}),t,null,[[12,,18,28]])}))}function Q(e,t,n){var r=function e(t){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;return t.flatMap((function(t,r){var o;return[{block:t,parentId:n,position:r+1}].concat(e(t.innerBlocks,null===(o=u(t))||void 0===o?void 0:o.id))}))}(e).map((function(e){return function(e,n,r){var o,i,c,a,l=Object(s.omit)(u(e),"menus","meta");o="core/navigation-link"===e.name?{type:"custom",title:null===(i=e.attributes)||void 0===i?void 0:i.label,original_title:"",url:e.attributes.url,description:e.attributes.description,xfn:null===(c=e.attributes.rel)||void 0===c?void 0:c.split(" "),classes:null===(a=e.attributes.className)||void 0===a?void 0:a.split(" "),attr_title:e.attributes.title}:{type:"block",content:Object(x.serialize)(e)};return U(U(U({},l),o),{},{position:r,nav_menu_term_id:t,menu_item_parent:n,status:"publish",_invalid:!1})}(e.block,e.parentId,e.position)})),o=function(e){return"nav_menu_item[".concat(e.id,"]")},i=Object(s.keyBy)(r,o);for(var c in n){var a=o(n[c]);a in i||(i[a]=!1)}return JSON.stringify(i);function u(e){return Object(s.omit)(n[e.clientId]||{},"_links")}}function q(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function Y(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function $(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}function J(e,t){return(J=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function W(e){return(W="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function K(e,t){return!t||"object"!==W(t)&&"function"!=typeof t?$(e):t}function Z(e){return(Z=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function X(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var n,r=Z(e);if(t){var o=Z(this).constructor;n=Reflect.construct(r,arguments,o)}else n=r.apply(this,arguments);return K(this,n)}}var ee=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&J(e,t)}(i,e);var t,n,r,o=X(i);function i(){var e;return q(this,i),(e=o.apply(this,arguments)).reboot=e.reboot.bind($(e)),e.state={error:null},e}return t=i,(n=[{key:"componentDidCatch",value:function(e){this.setState({error:e})}},{key:"reboot",value:function(){this.props.onError&&this.props.onError()}},{key:"render",value:function(){return this.state.error?Object(u.createElement)(_.Warning,{className:"navigation-editor-error-boundary",actions:[Object(u.createElement)(h.Button,{key:"recovery",onClick:this.reboot,isSecondary:!0},Object(f.__)("Attempt Recovery"))]},Object(f.__)("The navigation editor has encountered an unexpected error.")):this.props.children}}])&&Y(t.prototype,n),r&&Y(t,r),i}(u.Component),te=n(42);function ne(e){var t=e.saveBlocks;Object(te.useShortcut)("core/edit-navigation/save-menu",Object(u.useCallback)((function(e){e.preventDefault(),t()})),{bindGlobal:!0});var n=Object(k.useDispatch)("core"),r=n.redo,o=n.undo;return Object(te.useShortcut)("core/edit-navigation/undo",(function(e){o(),e.preventDefault()}),{bindGlobal:!0}),Object(te.useShortcut)("core/edit-navigation/redo",(function(e){r(),e.preventDefault()}),{bindGlobal:!0}),null}ne.Register=function(){var e=Object(k.useDispatch)("core/keyboard-shortcuts").registerShortcut;return Object(u.useEffect)((function(){e({name:"core/edit-navigation/save-menu",category:"global",description:Object(f.__)("Save the navigation currently being edited."),keyCombination:{modifier:"primary",character:"s"}}),e({name:"core/edit-navigation/undo",category:"global",description:Object(f.__)("Undo your last changes."),keyCombination:{modifier:"primary",character:"z"}}),e({name:"core/edit-navigation/redo",category:"global",description:Object(f.__)("Redo your last undo."),keyCombination:{modifier:"primaryShift",character:"z"}})}),[e]),null};var re=ne;function oe(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function ie(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?oe(Object(n),!0).forEach((function(t){a(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):oe(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function ce(){var e=Object(k.useSelect)((function(e){return e("core").getMenus()}),[]),t=function(){var e=j(Object(u.useState)(null),2),t=e[0],n=e[1];Object(u.useEffect)((function(){var e=!0;return function(){var t=P(w.a.mark((function t(){var r;return w.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,d()({method:"GET",path:"/__experimental/menu-locations"});case 2:r=t.sent,e&&n(r);case 4:case"end":return t.stop()}}),t)})));return function(){return t.apply(this,arguments)}}()(),function(){return e=!1}}),[]);var r=Object(k.useDispatch)("core").saveMenu,o=Object(u.useCallback)(function(){var e=P(w.a.mark((function e(o,i){var c,u,s;return w.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return c=t[o].menu,u=ie(ie({},t),{},a({},o,ie(ie({},t[o]),{},{menu:i}))),n(u),s=[],c&&s.push(r({id:c,locations:Object.values(u).filter((function(e){return e.menu===c})).map((function(e){return e.name}))})),i&&s.push(r({id:i,locations:Object.values(u).filter((function(e){return e.menu===i})).map((function(e){return e.name}))})),e.next=8,Promise.all(s);case 8:case"end":return e.stop()}}),e)})));return function(t,n){return e.apply(this,arguments)}}(),[t]);return[Object(u.useMemo)((function(){return t?Object.values(t):null}),[t]),o]}(),n=j(t,2),r=n[0],o=n[1];return e&&r?e.length?r.length?r.map((function(t){return Object(u.createElement)(h.SelectControl,{key:t.name,label:t.description,labelPosition:"top",value:t.menu,options:[{value:0,label:Object(f.__)("-")}].concat(N(e.map((function(e){return{value:e.id,label:e.name}})))),onChange:function(e){o(t.name,Number(e))}})})):Object(u.createElement)("p",null,Object(f.__)("There are no available menu locations.")):Object(u.createElement)("p",null,Object(f.__)("There are no available menus.")):Object(u.createElement)(h.Spinner,null)}var ae=function(e){return function(t){return t.name.toLowerCase()===e.toLowerCase()}};function ue(e){var t=e.menus,n=e.onCreate,r=j(Object(u.useState)(""),2),o=r[0],i=r[1],c=Object(k.useDispatch)("core/notices"),a=c.createErrorNotice,l=c.createInfoNotice,p=j(Object(u.useState)(!1),2),b=p[0],d=p[1],m=Object(k.useDispatch)("core").saveMenu,v=function(){var e=P(w.a.mark((function e(r){var i,c;return w.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r.preventDefault(),o.length){e.next=3;break}return e.abrupt("return");case 3:if(!Object(s.some)(t,ae(o))){e.next=7;break}return i=Object(f.sprintf)(// translators: %s: the name of a menu.
Object(f.__)("The menu name %s conflicts with another menu name. Please try another."),o),a(i,{id:"edit-navigation-error"}),e.abrupt("return");case 7:return d(!0),e.next=10,m({name:o});case 10:(c=e.sent)&&(l(Object(f.__)("Menu created"),{type:"snackbar",isDismissible:!0}),n(c.id)),d(!1);case 13:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}();return Object(u.createElement)("form",{onSubmit:v},Object(u.createElement)(h.TextControl,{autoFocus:!0,label:Object(f.__)("Menu name"),value:o,onChange:i}),Object(u.createElement)(h.Button,{className:"edit-navigation-header__create-menu-button",type:"submit",isPrimary:!0,disabled:!o.length,isBusy:b},Object(f.__)("Create menu")))}function se(e){var t=e.menus,n=e.selectedMenuId,r=e.onSelectMenu,o=Object(p.useViewportMatch)("small","<");return Object(u.createElement)("div",{className:"edit-navigation-header"},Object(u.createElement)("h1",{className:"edit-navigation-header__title"},Object(f.__)("Navigation")),Object(u.createElement)("div",{className:"edit-navigation-header__actions"},Object(u.createElement)("div",{className:"edit-navigation-header__current-menu"},Object(u.createElement)(h.SelectControl,{label:Object(f.__)("Currently editing"),hideLabelFromVision:o,disabled:!(null==t?void 0:t.length),value:null!=n?n:0,options:(null==t?void 0:t.length)?t.map((function(e){return{value:e.id,label:e.name}})):[{value:0,label:"-"}],onChange:r})),Object(u.createElement)(h.Dropdown,{position:"bottom center",renderToggle:function(e){var t=e.isOpen,n=e.onToggle;return Object(u.createElement)(h.Button,{isTertiary:!0,"aria-expanded":t,onClick:n},Object(f.__)("Add new"))},renderContent:function(){return Object(u.createElement)(ue,{menus:t,onCreate:r})}}),Object(u.createElement)(h.Dropdown,{contentClassName:"edit-navigation-header__manage-locations",position:"bottom center",renderToggle:function(e){var t=e.isOpen,n=e.onToggle;return Object(u.createElement)(h.Button,{isTertiary:!0,"aria-expanded":t,onClick:n},Object(f.__)("Manage locations"))},renderContent:function(){return Object(u.createElement)(ce,null)}})))}function le(){var e=Object(k.useDispatch)("core/notices").removeNotice,t=Object(k.useSelect)((function(e){return e("core/notices").getNotices()}),[]),n=Object(s.filter)(t,{isDismissible:!0,type:"default"}),r=Object(s.filter)(t,{isDismissible:!1,type:"default"}),o=Object(s.filter)(t,{type:"snackbar"});return Object(u.createElement)(u.Fragment,null,Object(u.createElement)(h.NoticeList,{notices:r,className:"edit-navigation-notices__notice-list"}),Object(u.createElement)(h.NoticeList,{notices:n,className:"edit-navigation-notices__notice-list",onRemove:e}),Object(u.createElement)(h.SnackbarList,{notices:o,className:"edit-navigation-notices__snackbar-list",onRemove:e}))}function pe(e){var t=e.navigationPost,n=Object(k.useDispatch)("core/edit-navigation").saveNavigationPost;return Object(u.createElement)(h.Button,{className:"edit-navigation-toolbar__save-button",isPrimary:!0,onClick:function(){n(t)}},Object(f.__)("Save"))}var fe=n(197);function be(){return Object(u.createElement)(h.Dropdown,{position:"bottom left",renderToggle:function(e){var t=e.isOpen,n=e.onToggle;return Object(u.createElement)(h.Button,{icon:fe.a,isPressed:t,label:Object(f.__)("Block inspector"),"aria-expanded":t,onClick:n})},renderContent:function(){return Object(u.createElement)(_.BlockInspector,{bubblesVirtually:!1})}})}function de(e){var t=e.isPending,n=e.navigationPost;return Object(u.createElement)("div",{className:"edit-navigation-toolbar"},t?Object(u.createElement)(h.Spinner,null):Object(u.createElement)(u.Fragment,null,Object(u.createElement)(_.NavigableToolbar,{className:"edit-navigation-toolbar__block-tools","aria-label":Object(f.__)("Block tools")},Object(u.createElement)(_.BlockToolbar,{hideDragHandle:!0,__experimentalExpandedControl:!0})),Object(u.createElement)(h.Popover.Slot,{name:"block-toolbar"}),Object(u.createElement)(be,null),Object(u.createElement)(pe,{navigationPost:n})))}function me(e){var t=e.isPending;return Object(u.createElement)("div",{className:"edit-navigation-editor__block-view"},t?Object(u.createElement)(h.Spinner,null):Object(u.createElement)("div",{className:"editor-styles-wrapper"},Object(u.createElement)(_.WritingFlow,null,Object(u.createElement)(_.ObserveTyping,null,Object(u.createElement)(_.BlockList,null)))))}function ve(e){var t,n=e.isPending,r=e.blocks,o=j(Object(u.useState)(null===(t=r[0])||void 0===t?void 0:t.clientId),2),i=o[0],c=o[1];return Object(u.createElement)("div",{className:"edit-navigation-editor__list-view"},Object(u.createElement)("h3",{className:"edit-navigation-editor__list-view-title"},Object(f.__)("List view")),n?Object(u.createElement)(h.Spinner,null):Object(u.createElement)(_.__experimentalBlockNavigationTree,{blocks:r,selectedBlockClientId:i,selectBlock:c,__experimentalFeatures:!0,showNestedBlocks:!0,showAppender:!0,showBlockMovers:!0}))}function Oe(e){var t=e.isPending,n=e.blocks;return Object(u.createElement)("div",{className:"edit-navigation-editor"},Object(u.createElement)(me,{isPending:t}),Object(u.createElement)(ve,{isPending:t,blocks:n}))}function ye(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function ge(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?ye(Object(n),!0).forEach((function(t){a(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):ye(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function je(e){var t=e.menuId,n=Object(k.useSelect)((function(e){return e("core").getMenu(t)}),[t]),r=j(Object(u.useState)(null),2),o=r[0],i=r[1];Object(u.useEffect)((function(){null===o&&n&&i(n.auto_add)}),[o,n]);var c=Object(k.useDispatch)("core").saveMenu;return Object(u.createElement)(h.PanelBody,null,Object(u.createElement)(h.CheckboxControl,{label:Object(f.__)("Automatically add new top-level pages"),checked:null!=o&&o,onChange:function(e){i(e),c(ge(ge({},n),{},{auto_add:e}))}}))}function he(e){var t=e.onDeleteMenu;return Object(u.createElement)(h.PanelBody,{className:"edit-navigation-inspector-additions__delete-menu-panel"},Object(u.createElement)(h.Button,{isLink:!0,isDestructive:!0,onClick:function(){window.confirm(Object(f.__)("Are you sure you want to delete this navigation?"))&&t()}},Object(f.__)("Delete menu")))}function _e(e){var t=e.menuId,n=e.onDeleteMenu,r=Object(k.useSelect)((function(e){return e("core/block-editor").getSelectedBlock()}),[]);return"core/navigation"!==(null==r?void 0:r.name)?null:Object(u.createElement)(_.InspectorControls,null,Object(u.createElement)(je,{menuId:t}),Object(u.createElement)(he,{onDeleteMenu:n}))}function Ee(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function we(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?Ee(Object(n),!0).forEach((function(t){a(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Ee(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function Se(e){var t,n,r,o,i,c,a=e.blockEditorSettings,s=function(){var e=Object(k.useSelect)((function(e){return e("core").getMenus({per_page:-1})}),[]),t=j(Object(u.useState)(null),2),n=t[0],r=t[1];Object(u.useEffect)((function(){!n&&(null==e?void 0:e.length)&&r(e[0].id)}),[n,e]);var o=Object(k.useSelect)((function(e){return e("core/edit-navigation").getNavigationPostForMenu(n)}),[n]),i=Object(k.useDispatch)("core").deleteMenu;return{menus:e,selectedMenuId:n,navigationPost:o,selectMenu:function(e){r(e)},deleteMenu:function(){var e=P(w.a.mark((function e(){return w.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,i(n,{force:!0});case 2:e.sent&&r(null);case 4:case"end":return e.stop()}}),e)})));return function(){return e.apply(this,arguments)}}()}}(),l=s.menus,p=s.selectedMenuId,f=s.navigationPost,b=s.selectMenu,d=s.deleteMenu,m=function(e){var t=Object(k.useDispatch)("core/edit-navigation").createMissingMenuItems,n=j(Object(I.useEntityBlockEditor)("root","postType",{id:null==e?void 0:e.id}),3),r=n[0],o=n[1],i=n[2];return[r,o,Object(u.useCallback)(function(){var n=P(w.a.mark((function n(r){return w.a.wrap((function(n){for(;;)switch(n.prev=n.next){case 0:return n.next=2,i(r);case 2:t(e);case 3:case"end":return n.stop()}}),n)})));return function(e){return n.apply(this,arguments)}}(),[r,i])]}(f),v=j(m,3),O=v[0],y=v[1],g=v[2];return t=p,n=Object(k.useSelect)((function(e){return{lastSaveError:e("core").getLastEntitySaveError("root","menu"),lastDeleteError:e("core").getLastEntityDeleteError("root","menu",t)}}),[t]),r=n.lastSaveError,o=n.lastDeleteError,i=Object(k.useDispatch)("core/notices").createErrorNotice,c=function(e){var t=(new window.DOMParser).parseFromString(e.message,"text/html").body.textContent||"";i(t,{id:"edit-navigation-error"})},Object(u.useEffect)((function(){r&&c(r)}),[r]),Object(u.useEffect)((function(){o&&c(o)}),[o]),Object(u.createElement)(ee,null,Object(u.createElement)(h.SlotFillProvider,null,Object(u.createElement)(h.DropZoneProvider,null,Object(u.createElement)(h.FocusReturnProvider,null,Object(u.createElement)(_.BlockEditorKeyboardShortcuts.Register,null),Object(u.createElement)(re.Register,null),Object(u.createElement)(le,null),Object(u.createElement)("div",{className:"edit-navigation-layout"},Object(u.createElement)(se,{menus:l,selectedMenuId:p,onSelectMenu:b}),Object(u.createElement)(_.BlockEditorProvider,{value:O,onInput:y,onChange:g,settings:we(we({},a),{},{templateLock:"all",hasFixedToolbar:!0})},Object(u.createElement)(de,{isPending:!f,navigationPost:f}),Object(u.createElement)(Oe,{isPending:!f,blocks:O}),Object(u.createElement)(_e,{menuId:p,onDeleteMenu:d}))),Object(u.createElement)(h.Popover.Slot,null)))))}function Pe(e,t){if(null==e)return{};var n,r,o=function(e,t){if(null==e)return{};var n,r,o={},i=Object.keys(e);for(r=0;r<i.length;r++)n=i[r],t.indexOf(n)>=0||(o[n]=e[n]);return o}(e,t);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(e);for(r=0;r<i.length;r++)n=i[r],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(o[n]=e[n])}return o}function ke(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function Ie(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?ke(Object(n),!0).forEach((function(t){a(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):ke(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}var xe=Object(k.combineReducers)({mapping:function(e,t){var n=t.type,r=t.postId,o=Pe(t,["type","postId"]);return"SET_MENU_ITEM_TO_CLIENT_ID_MAPPING"===n?Ie(Ie({},e),{},a({},r,o.mapping)):e||{}},processingQueue:function(e,t){var n,r=t.type,o=t.postId,i=Pe(t,["type","postId"]);switch(r){case"START_PROCESSING_POST":return Ie(Ie({},e),{},a({},o,Ie(Ie({},e[o]),{},{inProgress:!0})));case"FINISH_PROCESSING_POST":return Ie(Ie({},e),{},a({},o,Ie(Ie({},e[o]),{},{inProgress:!1})));case"POP_PENDING_ACTION":var c,u=Ie({},e[o]);if("pendingActions"in u)u.pendingActions=null===(c=u.pendingActions)||void 0===c?void 0:c.filter((function(e){return e!==i.action}));return Ie(Ie({},e),{},a({},o,u));case"ENQUEUE_AFTER_PROCESSING":var s=(null===(n=e[o])||void 0===n?void 0:n.pendingActions)||[];if(!s.includes(i.action))return Ie(Ie({},e),{},a({},o,Ie(Ie({},e[o]),{},{pendingActions:[].concat(N(s),[i.action])})))}return e||{}}});function Ne(e,t){var n;if("undefined"==typeof Symbol||null==e[Symbol.iterator]){if(Array.isArray(e)||(n=function(e,t){if(!e)return;if("string"==typeof e)return Ce(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);"Object"===n&&e.constructor&&(n=e.constructor.name);if("Map"===n||"Set"===n)return Array.from(e);if("Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return Ce(e,t)}(e))||t&&e&&"number"==typeof e.length){n&&(e=n);var r=0,o=function(){};return{s:o,n:function(){return r>=e.length?{done:!0}:{done:!1,value:e[r++]}},e:function(e){throw e},f:o}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var i,c=!0,a=!1;return{s:function(){n=e[Symbol.iterator]()},n:function(){var e=n.next();return c=e.done,e},e:function(e){a=!0,i=e},f:function(){try{c||null==n.return||n.return()}finally{if(a)throw i}}}}function Ce(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=new Array(t);n<t;n++)r[n]=e[n];return r}var Te=w.a.mark(De);function De(e){var t,n,r,o,i,c,a;return w.a.wrap((function(u){for(;;)switch(u.prev=u.next){case 0:return t=Me(e),u.next=3,Re(t);case 3:return n=["root","postType",t.id],u.next=6,A("core","startResolution","getEntityRecord",n);case 6:return u.next=8,R(e);case 8:return r=u.sent,o=Ae(r),i=j(o,2),c=i[0],a=i[1],u.next=12,{type:"SET_MENU_ITEM_TO_CLIENT_ID_MAPPING",postId:t.id,mapping:a};case 12:return u.next=14,Re(Me(e,c));case 14:return u.next=16,A("core","finishResolution","getEntityRecord",n);case 16:case"end":return u.stop()}}),Te)}var Me=function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null,n=z(e);return{id:n,slug:n,status:"draft",type:"page",blocks:t?[t]:[],meta:{menuId:e}}},Re=function(e){return A("core","receiveEntityRecords","root","postType",e,{id:e.id},!1)};function Ae(e){var t=Object(s.groupBy)(e,"parent"),n={},r=function e(r){var o=[];if(r){var i,c=Ne(Object(s.sortBy)(r,"menu_order"));try{for(c.s();!(i=c.n()).done;){var a,u=i.value,l=[];(null===(a=t[u.id])||void 0===a?void 0:a.length)&&(l=e(t[u.id]));var p=Be(u,l);n[u.id]=p.clientId,o.push(p)}}catch(e){c.e(e)}finally{c.f()}return o}}(t[0]||[]);return[Object(x.createBlock)("core/navigation",{},r),n]}function Be(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:[];if("block"===e.type){var n=Object(x.parse)(e.content.raw),r=j(n,1),o=r[0];return o?Object(x.createBlock)(o.name,o.attributes,t):Object(x.createBlock)("core/freeform",{originalContent:e.content.raw})}var i={label:e.title.rendered,url:e.url,title:e.attr_title,className:e.classes.join(" "),description:e.description,rel:e.xfn.join(" ")};return Object(x.createBlock)("core/navigation-link",i,t)}var Fe=Object(k.createRegistrySelector)((function(e){return function(t,n){return Ge(t,n)?e("core").getEditedEntityRecord("root","postType",z(n)):null}})),Ge=Object(k.createRegistrySelector)((function(e){return function(t,n){return e("core").hasFinishedResolution("getEntityRecord",["root","postType",z(n)])}})),Le=Object(k.createRegistrySelector)((function(e){return function(t,n,r){var o=Object(s.invert)(t.mapping[n]);return e("core").getMenuItem(o[r])}})),Ue=n(198),ze=w.a.mark($e);function Ve(e,t){var n;if("undefined"==typeof Symbol||null==e[Symbol.iterator]){if(Array.isArray(e)||(n=function(e,t){if(!e)return;if("string"==typeof e)return He(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);"Object"===n&&e.constructor&&(n=e.constructor.name);if("Map"===n||"Set"===n)return Array.from(e);if("Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return He(e,t)}(e))||t&&e&&"number"==typeof e.length){n&&(e=n);var r=0,o=function(){};return{s:o,n:function(){return r>=e.length?{done:!0}:{done:!1,value:e[r++]}},e:function(e){throw e},f:o}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var i,c=!0,a=!1;return{s:function(){n=e[Symbol.iterator]()},n:function(){var e=n.next();return c=e.done,e},e:function(e){a=!0,i=e},f:function(){try{c||null==n.return||n.return()}finally{if(a)throw i}}}}function He(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=new Array(t);n<t;n++)r[n]=e[n];return r}var Qe=H(w.a.mark((function e(t){var n,r,o,i,c,a,u;return w.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return n=t.meta.menuId,e.next=3,M(t.id);case 3:r=e.sent,o=Object(s.invert)(r),i=[t.blocks[0]];case 6:if(!i.length){e.next=21;break}if((c=i.pop()).clientId in o){e.next=18;break}return e.next=11,C({path:"/__experimental/menu-items",method:"POST",data:{title:"Placeholder",url:"Placeholder",menu_order:0}});case 11:return a=e.sent,r[a.id]=c.clientId,e.next=15,R(n);case 15:return u=e.sent,e.next=18,A("core","receiveEntityRecords","root","menuItem",[].concat(N(u),[a]),V(n),!1);case 18:i.push.apply(i,N(c.innerBlocks)),e.next=6;break;case 21:return e.next=23,{type:"SET_MENU_ITEM_TO_CLIENT_ID_MAPPING",postId:t.id,mapping:r};case 23:case"end":return e.stop()}}),e)}))),qe=H(w.a.mark((function e(t){var n,r;return w.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return n=t.meta.menuId,e.t0=Ye,e.next=4,R(n);case 4:return e.t1=e.sent,e.next=7,M(t.id);case 7:return e.t2=e.sent,r=(0,e.t0)(e.t1,e.t2),e.prev=9,e.delegateYield($e(n,r,t.blocks[0]),"t3",11);case 11:if(e.t3.success){e.next=14;break}throw new Error;case 14:return e.next=16,A("core/notices","createSuccessNotice",Object(f.__)("Navigation saved."),{type:"snackbar"});case 16:e.next=22;break;case 18:return e.prev=18,e.t4=e.catch(9),e.next=22,A("core/notices","createErrorNotice",Object(f.__)("There was an error."),{type:"snackbar"});case 22:case"end":return e.stop()}}),e,null,[[9,18]])})));function Ye(e,t){var n={};if(!e||!t)return n;var r,o=Ve(e);try{for(o.s();!(r=o.n()).done;){var i=r.value,c=t[i.id];c&&(n[c]=i)}}catch(e){o.e(e)}finally{o.f()}return n}function $e(e,t,n){var r,o,i,c;return w.a.wrap((function(a){for(;;)switch(a.prev=a.next){case 0:return a.next=2,C({path:"/__experimental/customizer-nonces/get-save-nonce"});case 2:if(r=a.sent,o=r.nonce,i=r.stylesheet,o){a.next=7;break}throw new Error;case 7:return(c=new FormData).append("wp_customize","on"),c.append("customize_theme",i),c.append("nonce",o),c.append("customize_changeset_uuid",Object(Ue.a)()),c.append("customize_autosaved","on"),c.append("customize_changeset_status","publish"),c.append("action","customize_save"),c.append("customized",Q(n.innerBlocks,e,t)),a.next=18,C({url:"/wp-admin/admin-ajax.php",method:"POST",body:c});case 18:return a.abrupt("return",a.sent);case 19:case"end":return a.stop()}}),ze)}var Je={reducer:xe,controls:G,selectors:o,resolvers:r,actions:i};Object(k.registerStore)("core/edit-navigation",Je);function We(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function Ke(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?We(Object(n),!0).forEach((function(t){a(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):We(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function Ze(e,t){return["core/navigation","core/navigation-link"].includes(t)||Object(s.set)(e,["supports","inserter"],!1),e}function Xe(e,t){return"core/navigation"!==t?e:Ke(Ke({},e),{},{supports:Ke(Ke({},Object(s.omit)(e.supports,["anchor","customClassName","__experimentalColor","__experimentalFontSize"])),{},{customClassName:!1})})}var et=Object(p.createHigherOrderComponent)((function(e){return function(t){return"core/navigation"!==t.name?Object(u.createElement)(e,t):Object(u.createElement)(e,c({},t,{hasSubmenuIndicatorSetting:!1,hasItemJustificationControls:!1,hasListViewModal:!1}))}}),"removeNavigationBlockEditUnsupportedFeatures"),tt=function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},n=t.isInitialSuggestions,r=t.type,o=t.subtype,i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},c=i.disablePostFormats,a=void 0!==c&&c,u=n?3:20,l=[];return r&&"post"!==r||l.push(d()({path:Object(m.addQueryArgs)("/wp/v2/search",{search:e,per_page:u,type:"post",subtype:o})}).catch((function(){return[]}))),r&&"term"!==r||l.push(d()({path:Object(m.addQueryArgs)("/wp/v2/search",{search:e,per_page:u,type:"term",subtype:o})}).catch((function(){return[]}))),a||r&&"post-format"!==r||l.push(d()({path:Object(m.addQueryArgs)("/wp/v2/search",{search:e,per_page:u,type:"post-format",subtype:o})}).catch((function(){return[]}))),Promise.all(l).then((function(e){return Object(s.map)(Object(s.flatten)(e).slice(0,u),(function(e){return{id:e.id,url:e.url,title:Object(v.decodeEntities)(e.title)||Object(f.__)("(no title)"),type:e.subtype||e.type}}))}))};function nt(e,t){t.blockNavMenus||Object(O.addFilter)("blocks.registerBlockType","core/edit-navigation/disable-inserting-non-navigation-blocks",Ze),Object(O.addFilter)("blocks.registerBlockType","core/edit-navigation/remove-navigation-block-settings-unsupported-features",Xe),Object(O.addFilter)("editor.BlockEdit","core/edit-navigation/remove-navigation-block-edit-unsupported-features",et),Object(l.registerCoreBlocks)(),Object(l.__experimentalRegisterExperimentalCoreBlocks)(t),t.__experimentalFetchLinkSuggestions=Object(s.partialRight)(tt,t),Object(u.render)(Object(u.createElement)(Se,{blockEditorSettings:t}),document.getElementById(e))}},42:function(e,t){!function(){e.exports=this.wp.keyboardShortcuts}()},5:function(e,t){!function(){e.exports=this.wp.blockEditor}()},6:function(e,t){!function(){e.exports=this.wp.primitives}()},62:function(e,t){!function(){e.exports=this.wp.htmlEntities}()},74:function(e,t){!function(){e.exports=this.wp.notices}()},79:function(e,t){!function(){e.exports=this.wp.blockLibrary}()},8:function(e,t){!function(){e.exports=this.wp.blocks}()},9:function(e,t){!function(){e.exports=this.wp.compose}()}});