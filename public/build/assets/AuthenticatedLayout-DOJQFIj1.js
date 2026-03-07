import{B as Ee,c as E,o as u,b as p,m as v,aq as fe,E as ge,aY as D,I as be,J as S,M as L,d as P,F as O,t as R,ai as ye,O as ne,H as $,w as U,a as I,aZ as he,j as K,g as y,a_ as se,e as ve,n as Z,r as H,a$ as Ie,R as G,k as C,U as Te,u as h,P as re,ak as Se,T as De,f as ae,W as B,K as Ce}from"./app-CsVeLNYl.js";import{s as Re,Z as j}from"./index-DopaX7DD.js";import{s as ie}from"./index-C7WJurIO.js";import{s as Q}from"./index-C9BdrPlG.js";import{s as ce}from"./index-Bb0UHUGR.js";import{s as xe}from"./index-D0mJi4Ae.js";import{s as q}from"./index-BuhO0EjY.js";import{R as Oe}from"./index-PqPkmIoQ.js";import{u as le,_ as we,a as Ae,s as Pe,b as Be}from"./FavIcons-BpCGXxOp.js";import{_ as Ue}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{a as ke}from"./index-C1ddAyhg.js";function k(t){"@babel/helpers - typeof";return k=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(o){return typeof o}:function(o){return o&&typeof Symbol=="function"&&o.constructor===Symbol&&o!==Symbol.prototype?"symbol":typeof o},k(t)}function M(t,o,e){return(o=Ne(o))in t?Object.defineProperty(t,o,{value:e,enumerable:!0,configurable:!0,writable:!0}):t[o]=e,t}function Ne(t){var o=Xe(t,"string");return k(o)=="symbol"?o:o+""}function Xe(t,o){if(k(t)!="object"||!t)return t;var e=t[Symbol.toPrimitive];if(e!==void 0){var s=e.call(t,o);if(k(s)!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return(o==="string"?String:Number)(t)}var Le=function(o){var e=o.dt;return`
.p-toast {
    width: `.concat(e("toast.width"),`;
    white-space: pre-line;
    word-break: break-word;
}

.p-toast-message {
    margin: 0 0 1rem 0;
}

.p-toast-message-icon {
    flex-shrink: 0;
    font-size: `).concat(e("toast.icon.size"),`;
    width: `).concat(e("toast.icon.size"),`;
    height: `).concat(e("toast.icon.size"),`;
}

.p-toast-message-content {
    display: flex;
    align-items: flex-start;
    padding: `).concat(e("toast.content.padding"),`;
    gap: `).concat(e("toast.content.gap"),`;
}

.p-toast-message-text {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    gap: `).concat(e("toast.text.gap"),`;
}

.p-toast-summary {
    font-weight: `).concat(e("toast.summary.font.weight"),`;
    font-size: `).concat(e("toast.summary.font.size"),`;
}

.p-toast-detail {
    font-weight: `).concat(e("toast.detail.font.weight"),`;
    font-size: `).concat(e("toast.detail.font.size"),`;
}

.p-toast-close-button {
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    background: transparent;
    transition: background `).concat(e("toast.transition.duration"),", color ").concat(e("toast.transition.duration"),", outline-color ").concat(e("toast.transition.duration"),", box-shadow ").concat(e("toast.transition.duration"),`;
    outline-color: transparent;
    color: inherit;
    width: `).concat(e("toast.close.button.width"),`;
    height: `).concat(e("toast.close.button.height"),`;
    border-radius: `).concat(e("toast.close.button.border.radius"),`;
    margin: -25% 0 0 0;
    right: -25%;
    padding: 0;
    border: none;
    user-select: none;
}

.p-toast-close-button:dir(rtl) {
    margin: -25% 0 0 auto;
    left: -25%;
    right: auto;
}

.p-toast-message-info,
.p-toast-message-success,
.p-toast-message-warn,
.p-toast-message-error,
.p-toast-message-secondary,
.p-toast-message-contrast {
    border-width: `).concat(e("toast.border.width"),`;
    border-style: solid;
    backdrop-filter: blur(`).concat(e("toast.blur"),`);
    border-radius: `).concat(e("toast.border.radius"),`;
}

.p-toast-close-icon {
    font-size: `).concat(e("toast.close.icon.size"),`;
    width: `).concat(e("toast.close.icon.size"),`;
    height: `).concat(e("toast.close.icon.size"),`;
}

.p-toast-close-button:focus-visible {
    outline-width: `).concat(e("focus.ring.width"),`;
    outline-style: `).concat(e("focus.ring.style"),`;
    outline-offset: `).concat(e("focus.ring.offset"),`;
}

.p-toast-message-info {
    background: `).concat(e("toast.info.background"),`;
    border-color: `).concat(e("toast.info.border.color"),`;
    color: `).concat(e("toast.info.color"),`;
    box-shadow: `).concat(e("toast.info.shadow"),`;
}

.p-toast-message-info .p-toast-detail {
    color: `).concat(e("toast.info.detail.color"),`;
}

.p-toast-message-info .p-toast-close-button:focus-visible {
    outline-color: `).concat(e("toast.info.close.button.focus.ring.color"),`;
    box-shadow: `).concat(e("toast.info.close.button.focus.ring.shadow"),`;
}

.p-toast-message-info .p-toast-close-button:hover {
    background: `).concat(e("toast.info.close.button.hover.background"),`;
}

.p-toast-message-success {
    background: `).concat(e("toast.success.background"),`;
    border-color: `).concat(e("toast.success.border.color"),`;
    color: `).concat(e("toast.success.color"),`;
    box-shadow: `).concat(e("toast.success.shadow"),`;
}

.p-toast-message-success .p-toast-detail {
    color: `).concat(e("toast.success.detail.color"),`;
}

.p-toast-message-success .p-toast-close-button:focus-visible {
    outline-color: `).concat(e("toast.success.close.button.focus.ring.color"),`;
    box-shadow: `).concat(e("toast.success.close.button.focus.ring.shadow"),`;
}

.p-toast-message-success .p-toast-close-button:hover {
    background: `).concat(e("toast.success.close.button.hover.background"),`;
}

.p-toast-message-warn {
    background: `).concat(e("toast.warn.background"),`;
    border-color: `).concat(e("toast.warn.border.color"),`;
    color: `).concat(e("toast.warn.color"),`;
    box-shadow: `).concat(e("toast.warn.shadow"),`;
}

.p-toast-message-warn .p-toast-detail {
    color: `).concat(e("toast.warn.detail.color"),`;
}

.p-toast-message-warn .p-toast-close-button:focus-visible {
    outline-color: `).concat(e("toast.warn.close.button.focus.ring.color"),`;
    box-shadow: `).concat(e("toast.warn.close.button.focus.ring.shadow"),`;
}

.p-toast-message-warn .p-toast-close-button:hover {
    background: `).concat(e("toast.warn.close.button.hover.background"),`;
}

.p-toast-message-error {
    background: `).concat(e("toast.error.background"),`;
    border-color: `).concat(e("toast.error.border.color"),`;
    color: `).concat(e("toast.error.color"),`;
    box-shadow: `).concat(e("toast.error.shadow"),`;
}

.p-toast-message-error .p-toast-detail {
    color: `).concat(e("toast.error.detail.color"),`;
}

.p-toast-message-error .p-toast-close-button:focus-visible {
    outline-color: `).concat(e("toast.error.close.button.focus.ring.color"),`;
    box-shadow: `).concat(e("toast.error.close.button.focus.ring.shadow"),`;
}

.p-toast-message-error .p-toast-close-button:hover {
    background: `).concat(e("toast.error.close.button.hover.background"),`;
}

.p-toast-message-secondary {
    background: `).concat(e("toast.secondary.background"),`;
    border-color: `).concat(e("toast.secondary.border.color"),`;
    color: `).concat(e("toast.secondary.color"),`;
    box-shadow: `).concat(e("toast.secondary.shadow"),`;
}

.p-toast-message-secondary .p-toast-detail {
    color: `).concat(e("toast.secondary.detail.color"),`;
}

.p-toast-message-secondary .p-toast-close-button:focus-visible {
    outline-color: `).concat(e("toast.secondary.close.button.focus.ring.color"),`;
    box-shadow: `).concat(e("toast.secondary.close.button.focus.ring.shadow"),`;
}

.p-toast-message-secondary .p-toast-close-button:hover {
    background: `).concat(e("toast.secondary.close.button.hover.background"),`;
}

.p-toast-message-contrast {
    background: `).concat(e("toast.contrast.background"),`;
    border-color: `).concat(e("toast.contrast.border.color"),`;
    color: `).concat(e("toast.contrast.color"),`;
    box-shadow: `).concat(e("toast.contrast.shadow"),`;
}

.p-toast-message-contrast .p-toast-detail {
    color: `).concat(e("toast.contrast.detail.color"),`;
}

.p-toast-message-contrast .p-toast-close-button:focus-visible {
    outline-color: `).concat(e("toast.contrast.close.button.focus.ring.color"),`;
    box-shadow: `).concat(e("toast.contrast.close.button.focus.ring.shadow"),`;
}

.p-toast-message-contrast .p-toast-close-button:hover {
    background: `).concat(e("toast.contrast.close.button.hover.background"),`;
}

.p-toast-top-center {
    transform: translateX(-50%);
}

.p-toast-bottom-center {
    transform: translateX(-50%);
}

.p-toast-center {
    min-width: 20vw;
    transform: translate(-50%, -50%);
}

.p-toast-message-enter-from {
    opacity: 0;
    transform: translateY(50%);
}

.p-toast-message-leave-from {
    max-height: 1000px;
}

.p-toast .p-toast-message.p-toast-message-leave-to {
    max-height: 0;
    opacity: 0;
    margin-bottom: 0;
    overflow: hidden;
}

.p-toast-message-enter-active {
    transition: transform 0.3s, opacity 0.3s;
}

.p-toast-message-leave-active {
    transition: max-height 0.45s cubic-bezier(0, 1, 0, 1), opacity 0.3s, margin-bottom 0.3s;
}
`)},Me={root:function(o){var e=o.position;return{position:"fixed",top:e==="top-right"||e==="top-left"||e==="top-center"?"20px":e==="center"?"50%":null,right:(e==="top-right"||e==="bottom-right")&&"20px",bottom:(e==="bottom-left"||e==="bottom-right"||e==="bottom-center")&&"20px",left:e==="top-left"||e==="bottom-left"?"20px":e==="center"||e==="top-center"||e==="bottom-center"?"50%":null}}},_e={root:function(o){var e=o.props;return["p-toast p-component p-toast-"+e.position]},message:function(o){var e=o.props;return["p-toast-message",{"p-toast-message-info":e.message.severity==="info"||e.message.severity===void 0,"p-toast-message-warn":e.message.severity==="warn","p-toast-message-error":e.message.severity==="error","p-toast-message-success":e.message.severity==="success","p-toast-message-secondary":e.message.severity==="secondary","p-toast-message-contrast":e.message.severity==="contrast"}]},messageContent:"p-toast-message-content",messageIcon:function(o){var e=o.props;return["p-toast-message-icon",M(M(M(M({},e.infoIcon,e.message.severity==="info"),e.warnIcon,e.message.severity==="warn"),e.errorIcon,e.message.severity==="error"),e.successIcon,e.message.severity==="success")]},messageText:"p-toast-message-text",summary:"p-toast-summary",detail:"p-toast-detail",closeButton:"p-toast-close-button",closeIcon:"p-toast-close-icon"},Ke=Ee.extend({name:"toast",theme:Le,classes:_e,inlineStyles:Me}),W={name:"ExclamationTriangleIcon",extends:ce};function je(t,o,e,s,a,c){return u(),E("svg",v({width:"14",height:"14",viewBox:"0 0 14 14",fill:"none",xmlns:"http://www.w3.org/2000/svg"},t.pti()),o[0]||(o[0]=[p("path",{d:"M13.4018 13.1893H0.598161C0.49329 13.189 0.390283 13.1615 0.299143 13.1097C0.208003 13.0578 0.131826 12.9832 0.0780112 12.8932C0.0268539 12.8015 0 12.6982 0 12.5931C0 12.4881 0.0268539 12.3848 0.0780112 12.293L6.47985 1.08982C6.53679 1.00399 6.61408 0.933574 6.70484 0.884867C6.7956 0.836159 6.897 0.810669 7 0.810669C7.103 0.810669 7.2044 0.836159 7.29516 0.884867C7.38592 0.933574 7.46321 1.00399 7.52015 1.08982L13.922 12.293C13.9731 12.3848 14 12.4881 14 12.5931C14 12.6982 13.9731 12.8015 13.922 12.8932C13.8682 12.9832 13.792 13.0578 13.7009 13.1097C13.6097 13.1615 13.5067 13.189 13.4018 13.1893ZM1.63046 11.989H12.3695L7 2.59425L1.63046 11.989Z",fill:"currentColor"},null,-1),p("path",{d:"M6.99996 8.78801C6.84143 8.78594 6.68997 8.72204 6.57787 8.60993C6.46576 8.49782 6.40186 8.34637 6.39979 8.18784V5.38703C6.39979 5.22786 6.46302 5.0752 6.57557 4.96265C6.68813 4.85009 6.84078 4.78686 6.99996 4.78686C7.15914 4.78686 7.31179 4.85009 7.42435 4.96265C7.5369 5.0752 7.60013 5.22786 7.60013 5.38703V8.18784C7.59806 8.34637 7.53416 8.49782 7.42205 8.60993C7.30995 8.72204 7.15849 8.78594 6.99996 8.78801Z",fill:"currentColor"},null,-1),p("path",{d:"M6.99996 11.1887C6.84143 11.1866 6.68997 11.1227 6.57787 11.0106C6.46576 10.8985 6.40186 10.7471 6.39979 10.5885V10.1884C6.39979 10.0292 6.46302 9.87658 6.57557 9.76403C6.68813 9.65147 6.84078 9.58824 6.99996 9.58824C7.15914 9.58824 7.31179 9.65147 7.42435 9.76403C7.5369 9.87658 7.60013 10.0292 7.60013 10.1884V10.5885C7.59806 10.7471 7.53416 10.8985 7.42205 11.0106C7.30995 11.1227 7.15849 11.1866 6.99996 11.1887Z",fill:"currentColor"},null,-1)]),16)}W.render=je;var V={name:"InfoCircleIcon",extends:ce};function $e(t,o,e,s,a,c){return u(),E("svg",v({width:"14",height:"14",viewBox:"0 0 14 14",fill:"none",xmlns:"http://www.w3.org/2000/svg"},t.pti()),o[0]||(o[0]=[p("path",{"fill-rule":"evenodd","clip-rule":"evenodd",d:"M3.11101 12.8203C4.26215 13.5895 5.61553 14 7 14C8.85652 14 10.637 13.2625 11.9497 11.9497C13.2625 10.637 14 8.85652 14 7C14 5.61553 13.5895 4.26215 12.8203 3.11101C12.0511 1.95987 10.9579 1.06266 9.67879 0.532846C8.3997 0.00303296 6.99224 -0.13559 5.63437 0.134506C4.2765 0.404603 3.02922 1.07129 2.05026 2.05026C1.07129 3.02922 0.404603 4.2765 0.134506 5.63437C-0.13559 6.99224 0.00303296 8.3997 0.532846 9.67879C1.06266 10.9579 1.95987 12.0511 3.11101 12.8203ZM3.75918 2.14976C4.71846 1.50879 5.84628 1.16667 7 1.16667C8.5471 1.16667 10.0308 1.78125 11.1248 2.87521C12.2188 3.96918 12.8333 5.45291 12.8333 7C12.8333 8.15373 12.4912 9.28154 11.8502 10.2408C11.2093 11.2001 10.2982 11.9478 9.23232 12.3893C8.16642 12.8308 6.99353 12.9463 5.86198 12.7212C4.73042 12.4962 3.69102 11.9406 2.87521 11.1248C2.05941 10.309 1.50384 9.26958 1.27876 8.13803C1.05367 7.00647 1.16919 5.83358 1.61071 4.76768C2.05222 3.70178 2.79989 2.79074 3.75918 2.14976ZM7.00002 4.8611C6.84594 4.85908 6.69873 4.79698 6.58977 4.68801C6.48081 4.57905 6.4187 4.43185 6.41669 4.27776V3.88888C6.41669 3.73417 6.47815 3.58579 6.58754 3.4764C6.69694 3.367 6.84531 3.30554 7.00002 3.30554C7.15473 3.30554 7.3031 3.367 7.4125 3.4764C7.52189 3.58579 7.58335 3.73417 7.58335 3.88888V4.27776C7.58134 4.43185 7.51923 4.57905 7.41027 4.68801C7.30131 4.79698 7.1541 4.85908 7.00002 4.8611ZM7.00002 10.6945C6.84594 10.6925 6.69873 10.6304 6.58977 10.5214C6.48081 10.4124 6.4187 10.2652 6.41669 10.1111V6.22225C6.41669 6.06754 6.47815 5.91917 6.58754 5.80977C6.69694 5.70037 6.84531 5.63892 7.00002 5.63892C7.15473 5.63892 7.3031 5.70037 7.4125 5.80977C7.52189 5.91917 7.58335 6.06754 7.58335 6.22225V10.1111C7.58134 10.2652 7.51923 10.4124 7.41027 10.5214C7.30131 10.6304 7.1541 10.6925 7.00002 10.6945Z",fill:"currentColor"},null,-1)]),16)}V.render=$e;var He={name:"BaseToast",extends:ie,props:{group:{type:String,default:null},position:{type:String,default:"top-right"},autoZIndex:{type:Boolean,default:!0},baseZIndex:{type:Number,default:0},breakpoints:{type:Object,default:null},closeIcon:{type:String,default:void 0},infoIcon:{type:String,default:void 0},warnIcon:{type:String,default:void 0},errorIcon:{type:String,default:void 0},successIcon:{type:String,default:void 0},closeButtonProps:{type:null,default:null}},style:Ke,provide:function(){return{$pcToast:this,$parentInstance:this}}},ue={name:"ToastMessage",hostName:"Toast",extends:ie,emits:["close"],closeTimeout:null,props:{message:{type:null,default:null},templates:{type:Object,default:null},closeIcon:{type:String,default:null},infoIcon:{type:String,default:null},warnIcon:{type:String,default:null},errorIcon:{type:String,default:null},successIcon:{type:String,default:null},closeButtonProps:{type:null,default:null}},mounted:function(){var o=this;this.message.life&&(this.closeTimeout=setTimeout(function(){o.close({message:o.message,type:"life-end"})},this.message.life))},beforeUnmount:function(){this.clearCloseTimeout()},methods:{close:function(o){this.$emit("close",o)},onCloseClick:function(){this.clearCloseTimeout(),this.close({message:this.message,type:"close"})},clearCloseTimeout:function(){this.closeTimeout&&(clearTimeout(this.closeTimeout),this.closeTimeout=null)}},computed:{iconComponent:function(){return{info:!this.infoIcon&&V,success:!this.successIcon&&Q,warn:!this.warnIcon&&W,error:!this.errorIcon&&q}[this.message.severity]},closeAriaLabel:function(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.close:void 0}},components:{TimesIcon:xe,InfoCircleIcon:V,CheckIcon:Q,ExclamationTriangleIcon:W,TimesCircleIcon:q},directives:{ripple:Oe}};function N(t){"@babel/helpers - typeof";return N=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(o){return typeof o}:function(o){return o&&typeof Symbol=="function"&&o.constructor===Symbol&&o!==Symbol.prototype?"symbol":typeof o},N(t)}function ee(t,o){var e=Object.keys(t);if(Object.getOwnPropertySymbols){var s=Object.getOwnPropertySymbols(t);o&&(s=s.filter(function(a){return Object.getOwnPropertyDescriptor(t,a).enumerable})),e.push.apply(e,s)}return e}function te(t){for(var o=1;o<arguments.length;o++){var e=arguments[o]!=null?arguments[o]:{};o%2?ee(Object(e),!0).forEach(function(s){Ge(t,s,e[s])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(e)):ee(Object(e)).forEach(function(s){Object.defineProperty(t,s,Object.getOwnPropertyDescriptor(e,s))})}return t}function Ge(t,o,e){return(o=We(o))in t?Object.defineProperty(t,o,{value:e,enumerable:!0,configurable:!0,writable:!0}):t[o]=e,t}function We(t){var o=Ve(t,"string");return N(o)=="symbol"?o:o+""}function Ve(t,o){if(N(t)!="object"||!t)return t;var e=t[Symbol.toPrimitive];if(e!==void 0){var s=e.call(t,o);if(N(s)!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return(o==="string"?String:Number)(t)}var ze=["aria-label"];function Ye(t,o,e,s,a,c){var i=be("ripple");return u(),E("div",v({class:[t.cx("message"),e.message.styleClass],role:"alert","aria-live":"assertive","aria-atomic":"true"},t.ptm("message")),[e.templates.container?(u(),S(L(e.templates.container),{key:0,message:e.message,closeCallback:c.onCloseClick},null,8,["message","closeCallback"])):(u(),E("div",v({key:1,class:[t.cx("messageContent"),e.message.contentStyleClass]},t.ptm("messageContent")),[e.templates.message?(u(),S(L(e.templates.message),{key:1,message:e.message},null,8,["message"])):(u(),E(O,{key:0},[(u(),S(L(e.templates.messageicon?e.templates.messageicon:e.templates.icon?e.templates.icon:c.iconComponent&&c.iconComponent.name?c.iconComponent:"span"),v({class:t.cx("messageIcon")},t.ptm("messageIcon")),null,16,["class"])),p("div",v({class:t.cx("messageText")},t.ptm("messageText")),[p("span",v({class:t.cx("summary")},t.ptm("summary")),R(e.message.summary),17),p("div",v({class:t.cx("detail")},t.ptm("detail")),R(e.message.detail),17)],16)],64)),e.message.closable!==!1?(u(),E("div",ye(v({key:2},t.ptm("buttonContainer"))),[ne((u(),E("button",v({class:t.cx("closeButton"),type:"button","aria-label":c.closeAriaLabel,onClick:o[0]||(o[0]=function(){return c.onCloseClick&&c.onCloseClick.apply(c,arguments)}),autofocus:""},te(te({},e.closeButtonProps),t.ptm("closeButton"))),[(u(),S(L(e.templates.closeicon||"TimesIcon"),v({class:[t.cx("closeIcon"),e.closeIcon]},t.ptm("closeIcon")),null,16,["class"]))],16,ze)),[[i]])],16)):P("",!0)],16))],16)}ue.render=Ye;function Ze(t){return qe(t)||Qe(t)||Je(t)||Fe()}function Fe(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function Je(t,o){if(t){if(typeof t=="string")return z(t,o);var e={}.toString.call(t).slice(8,-1);return e==="Object"&&t.constructor&&(e=t.constructor.name),e==="Map"||e==="Set"?Array.from(t):e==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(e)?z(t,o):void 0}}function Qe(t){if(typeof Symbol<"u"&&t[Symbol.iterator]!=null||t["@@iterator"]!=null)return Array.from(t)}function qe(t){if(Array.isArray(t))return z(t)}function z(t,o){(o==null||o>t.length)&&(o=t.length);for(var e=0,s=Array(o);e<o;e++)s[e]=t[e];return s}var et=0,me={name:"Toast",extends:He,inheritAttrs:!1,emits:["close","life-end"],data:function(){return{messages:[]}},styleElement:null,mounted:function(){D.on("add",this.onAdd),D.on("remove",this.onRemove),D.on("remove-group",this.onRemoveGroup),D.on("remove-all-groups",this.onRemoveAllGroups),this.breakpoints&&this.createStyle()},beforeUnmount:function(){this.destroyStyle(),this.$refs.container&&this.autoZIndex&&j.clear(this.$refs.container),D.off("add",this.onAdd),D.off("remove",this.onRemove),D.off("remove-group",this.onRemoveGroup),D.off("remove-all-groups",this.onRemoveAllGroups)},methods:{add:function(o){o.id==null&&(o.id=et++),this.messages=[].concat(Ze(this.messages),[o])},remove:function(o){var e=this.messages.findIndex(function(s){return s.id===o.message.id});e!==-1&&(this.messages.splice(e,1),this.$emit(o.type,{message:o.message}))},onAdd:function(o){this.group==o.group&&this.add(o)},onRemove:function(o){this.remove({message:o,type:"close"})},onRemoveGroup:function(o){this.group===o&&(this.messages=[])},onRemoveAllGroups:function(){this.messages=[]},onEnter:function(){this.autoZIndex&&j.set("modal",this.$refs.container,this.baseZIndex||this.$primevue.config.zIndex.modal)},onLeave:function(){var o=this;this.$refs.container&&this.autoZIndex&&ge(this.messages)&&setTimeout(function(){j.clear(o.$refs.container)},200)},createStyle:function(){if(!this.styleElement&&!this.isUnstyled){var o;this.styleElement=document.createElement("style"),this.styleElement.type="text/css",fe(this.styleElement,"nonce",(o=this.$primevue)===null||o===void 0||(o=o.config)===null||o===void 0||(o=o.csp)===null||o===void 0?void 0:o.nonce),document.head.appendChild(this.styleElement);var e="";for(var s in this.breakpoints){var a="";for(var c in this.breakpoints[s])a+=c+":"+this.breakpoints[s][c]+"!important;";e+=`
                        @media screen and (max-width: `.concat(s,`) {
                            .p-toast[`).concat(this.$attrSelector,`] {
                                `).concat(a,`
                            }
                        }
                    `)}this.styleElement.innerHTML=e}},destroyStyle:function(){this.styleElement&&(document.head.removeChild(this.styleElement),this.styleElement=null)}},components:{ToastMessage:ue,Portal:Re}};function X(t){"@babel/helpers - typeof";return X=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(o){return typeof o}:function(o){return o&&typeof Symbol=="function"&&o.constructor===Symbol&&o!==Symbol.prototype?"symbol":typeof o},X(t)}function oe(t,o){var e=Object.keys(t);if(Object.getOwnPropertySymbols){var s=Object.getOwnPropertySymbols(t);o&&(s=s.filter(function(a){return Object.getOwnPropertyDescriptor(t,a).enumerable})),e.push.apply(e,s)}return e}function tt(t){for(var o=1;o<arguments.length;o++){var e=arguments[o]!=null?arguments[o]:{};o%2?oe(Object(e),!0).forEach(function(s){ot(t,s,e[s])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(e)):oe(Object(e)).forEach(function(s){Object.defineProperty(t,s,Object.getOwnPropertyDescriptor(e,s))})}return t}function ot(t,o,e){return(o=nt(o))in t?Object.defineProperty(t,o,{value:e,enumerable:!0,configurable:!0,writable:!0}):t[o]=e,t}function nt(t){var o=st(t,"string");return X(o)=="symbol"?o:o+""}function st(t,o){if(X(t)!="object"||!t)return t;var e=t[Symbol.toPrimitive];if(e!==void 0){var s=e.call(t,o);if(X(s)!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return(o==="string"?String:Number)(t)}function rt(t,o,e,s,a,c){var i=$("ToastMessage"),l=$("Portal");return u(),S(l,null,{default:U(function(){return[p("div",v({ref:"container",class:t.cx("root"),style:t.sx("root",!0,{position:t.position})},t.ptmi("root")),[I(he,v({name:"p-toast-message",tag:"div",onEnter:c.onEnter,onLeave:c.onLeave},tt({},t.ptm("transition"))),{default:U(function(){return[(u(!0),E(O,null,K(a.messages,function(m){return u(),S(i,{key:m.id,message:m,templates:t.$slots,closeIcon:t.closeIcon,infoIcon:t.infoIcon,warnIcon:t.warnIcon,errorIcon:t.errorIcon,successIcon:t.successIcon,closeButtonProps:t.closeButtonProps,unstyled:t.unstyled,onClose:o[0]||(o[0]=function(b){return c.remove(b)}),pt:t.pt},null,8,["message","templates","closeIcon","infoIcon","warnIcon","errorIcon","successIcon","closeButtonProps","unstyled","pt"])}),128))]}),_:1},16,["onEnter","onLeave"])],16)]}),_:1})}me.render=rt;/*! js-cookie v3.0.5 | MIT */function _(t){for(var o=1;o<arguments.length;o++){var e=arguments[o];for(var s in e)t[s]=e[s]}return t}var at={read:function(t){return t[0]==='"'&&(t=t.slice(1,-1)),t.replace(/(%[\dA-F]{2})+/gi,decodeURIComponent)},write:function(t){return encodeURIComponent(t).replace(/%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g,decodeURIComponent)}};function Y(t,o){function e(a,c,i){if(!(typeof document>"u")){i=_({},o,i),typeof i.expires=="number"&&(i.expires=new Date(Date.now()+i.expires*864e5)),i.expires&&(i.expires=i.expires.toUTCString()),a=encodeURIComponent(a).replace(/%(2[346B]|5E|60|7C)/g,decodeURIComponent).replace(/[()]/g,escape);var l="";for(var m in i)i[m]&&(l+="; "+m,i[m]!==!0&&(l+="="+i[m].split(";")[0]));return document.cookie=a+"="+t.write(c,a)+l}}function s(a){if(!(typeof document>"u"||arguments.length&&!a)){for(var c=document.cookie?document.cookie.split("; "):[],i={},l=0;l<c.length;l++){var m=c[l].split("="),b=m.slice(1).join("=");try{var n=decodeURIComponent(m[0]);if(i[n]=t.read(b,n),a===n)break}catch{}}return a?i[a]:i}}return Object.create({set:e,get:s,remove:function(a,c){e(a,"",_({},c,{expires:-1}))},withAttributes:function(a){return Y(this.converter,_({},this.attributes,a))},withConverter:function(a){return Y(_({},this.converter,a),this.attributes)}},{attributes:{value:Object.freeze(o)},converter:{value:Object.freeze(t)}})}var pe=Y(at,{path:"/"});const x=se({preset:"Aura",primary:"emerald",surface:null,darkTheme:!1,menuMode:"static"}),T=se({staticMenuDesktopInactive:pe.get("menuState")==="true"||!1,overlayMenuActive:!1,profileSidebarVisible:!1,configSidebarVisible:!1,staticMenuMobileActive:!1,menuHoverActive:!1,activeMenuItem:null});function F(){const t=m=>{T.activeMenuItem=m.value||m},o=()=>{if(!document.startViewTransition){e();return}document.startViewTransition(()=>e(event))},e=()=>{x.darkTheme=!x.darkTheme,document.documentElement.classList.toggle("app-dark")},s=()=>{x.menuMode==="overlay"&&(T.overlayMenuActive=!T.overlayMenuActive),window.innerWidth>991?(T.staticMenuDesktopInactive=!T.staticMenuDesktopInactive,pe.set("menuState",T.staticMenuDesktopInactive)):T.staticMenuMobileActive=!T.staticMenuMobileActive},a=y(()=>T.overlayMenuActive||T.staticMenuMobileActive),c=y(()=>x.darkTheme),i=y(()=>x.primary),l=y(()=>x.surface);return{layoutConfig:x,layoutState:T,toggleMenu:s,isSidebarActive:a,isDarkTheme:c,getPrimary:i,getSurface:l,setActiveMenuItem:t,toggleDarkMode:o}}const it={},ct={class:"layout-footer"};function lt(t,o){return u(),E("div",ct,o[0]||(o[0]=[ve(" Developed With 💓 By: "),p("a",{href:"#",target:"_blank",rel:"noopener noreferrer",class:"text-primary font-bold hover:underline"}," Sunwarul Islam, and Team! ",-1)]))}const ut=Ue(it,[["render",lt]]);function de(){const t=Z(),o=y(()=>{var n;return((n=t.props.auth)==null?void 0:n.user)??null}),e=y(()=>{var n;return((n=t.props.auth)==null?void 0:n.roles)??[]}),s=y(()=>{var n;return((n=t.props.auth)==null?void 0:n.permissions)??[]});return{user:o,roles:e,permissions:s,can:n=>s.value.includes(n),hasPermission:n=>n?s.value.includes(n):!0,hasAnyPermission:(n=[])=>n.length?(Array.isArray(n)||(n=[n]),n.some(f=>s.value.includes(f))):!0,hasAllPermissions:(n=[])=>n.length?(Array.isArray(n)||(n=[n]),n.every(f=>s.value.includes(f))):!0,hasRole:n=>n?e.value.includes(n):!0,hasAnyRole:(n=[])=>n.length?(Array.isArray(n)||(n=[n]),n.some(f=>e.value.includes(f))):!0}}const r=Object.freeze({CATEGORY:{INDEX:"Category Index",STORE:"Category Store",UPDATE:"Category Update",EDIT:"Category Edit",DELETE:"Category Delete",SEARCH:"Category Search",EXPORT:"Category Export",BULK_DELETE:"Category Bulk Delete",BULK_RESTORE:"Category Bulk Restore"},BRAND:{INDEX:"Brand Index",STORE:"Brand Store",UPDATE:"Brand Update",EDIT:"Brand Edit",DELETE:"Brand Delete",SEARCH:"Brand Search",EXPORT:"Brand Export",BULK_DELETE:"Brand Bulk Delete",BULK_RESTORE:"Brand Bulk Restore"},TAG:{INDEX:"Tag Index",STORE:"Tag Store",UPDATE:"Tag Update",EDIT:"Tag Edit",DELETE:"Tag Delete",SEARCH:"Tag Search",EXPORT:"Tag Export",BULK_DELETE:"Tag Bulk Delete",BULK_RESTORE:"Tag Bulk Restore"},TAX:{INDEX:"Tax Index",STORE:"Tax Store",UPDATE:"Tax Update",EDIT:"Tax Edit",DELETE:"Tax Delete",SEARCH:"Tax Search",EXPORT:"Tax Export",BULK_DELETE:"Tax Bulk Delete",BULK_RESTORE:"Tax Bulk Restore"},PAYMENT_METHOD:{INDEX:"Payment Methods Index",STORE:"Payment Methods Store",UPDATE:"Payment Methods Update",EDIT:"Payment Methods Edit",DELETE:"Payment Methods Delete",SEARCH:"Payment Methods Search",EXPORT:"Payment Methods Export",BULK_DELETE:"Payment Methods Bulk Delete",BULK_RESTORE:"Payment Methods Bulk Restore"},PRODUCT:{INDEX:"Product Index",STORE:"Product Store",UPDATE:"Product Update",EDIT:"Product Edit",DELETE:"Product Delete",MOVEMENT:"Product Movement",SEARCH:"Product Search",EXPORT:"Product Export",BULK_DELETE:"Product Bulk Delete",BULK_RESTORE:"Product Bulk Restore"},POS:{INDEX:"POS Index"},ORDER:{INDEX:"Order Index",VIEW:"Order View",INVOICE:"Order Invoice",COMPLETE:"Order Complete",VOID:"Order Void"},ROLE:{INDEX:"Role Index",STORE:"Role Store",UPDATE:"Role Update",EDIT:"Role Edit",DELETE:"Role Delete",SEARCH:"Role Search",EXPORT:"Role Export",BULK_DELETE:"Role Bulk Delete",BULK_RESTORE:"Role Bulk Restore"},PERMISSION:{INDEX:"Permission Index",STORE:"Permission Store"},USER:{INDEX:"User Index",STORE:"User Store",UPDATE:"User Update",EDIT:"User Edit",DELETE:"User Delete",SEARCH:"User Search",EXPORT:"User Export",BULK_DELETE:"User Bulk Delete",BULK_RESTORE:"User Bulk Restore"},BRANCH:{INDEX:"Branch Index",STORE:"Branch Store",UPDATE:"Branch Update",EDIT:"Branch Edit",DELETE:"Branch Delete",SEARCH:"Branch Search",EXPORT:"Branch Export",BULK_DELETE:"Branch Bulk Delete",BULK_RESTORE:"Branch Bulk Restore"},WAREHOUSE:{INDEX:"Warehouse Index",STORE:"Warehouse Store",UPDATE:"Warehouse Update",EDIT:"Warehouse Edit",DELETE:"Warehouse Delete",SEARCH:"Warehouse Search",EXPORT:"Warehouse Export",BULK_DELETE:"Warehouse Bulk Delete",BULK_RESTORE:"Warehouse Bulk Restore"},BASE_UNIT:{INDEX:"Base Unit Index",STORE:"Base Unit Store",UPDATE:"Base Unit Update",EDIT:"Base Unit Edit",DELETE:"Base Unit Delete",SEARCH:"Base Unit Search",EXPORT:"Base Unit Export",BULK_DELETE:"Base Unit Bulk Delete",BULK_RESTORE:"Base Unit Bulk Restore"},UNIT:{INDEX:"Unit Index",STORE:"Unit Store",UPDATE:"Unit Update",EDIT:"Unit Edit",DELETE:"Unit Delete",SEARCH:"Unit Search",EXPORT:"Unit Export",BULK_DELETE:"Unit Bulk Delete",BULK_RESTORE:"Unit Bulk Restore"},SUPPLIER:{INDEX:"Supplier Index",STORE:"Supplier Store",UPDATE:"Supplier Update",EDIT:"Supplier Edit",DELETE:"Supplier Delete",SEARCH:"Supplier Search",EXPORT:"Supplier Export",BULK_DELETE:"Supplier Bulk Delete",BULK_RESTORE:"Supplier Bulk Restore"},CUSTOMER:{INDEX:"Customer Index",STORE:"Customer Store",UPDATE:"Customer Update",EDIT:"Customer Edit",DELETE:"Customer Delete",SEARCH:"Customer Search",EXPORT:"Customer Export",BULK_DELETE:"Customer Bulk Delete",BULK_RESTORE:"Customer Bulk Restore"},EXPENSE_CATEGORY:{INDEX:"Expense Categories Index",STORE:"Expense Categories Store",UPDATE:"Expense Categories Update",EDIT:"Expense Categories Edit",DELETE:"Expense Categories Delete",SEARCH:"Expense Categories Search",EXPORT:"Expense Categories Export",BULK_DELETE:"Expense Categories Bulk Delete",BULK_RESTORE:"Expense Categories Bulk Restore"},EXPENSE:{INDEX:"Expense Index",STORE:"Expense Store",UPDATE:"Expense Update",EDIT:"Expense Edit",DELETE:"Expense Delete",SEARCH:"Expense Search",EXPORT:"Expense Export",BULK_DELETE:"Expense Bulk Delete",BULK_RESTORE:"Expense Bulk Restore"},COUNTRY:{INDEX:"Country Index",STORE:"Country Store",UPDATE:"Country Update",EDIT:"Country Edit",DELETE:"Country Delete",SEARCH:"Country Search",EXPORT:"Country Export",BULK_DELETE:"Country Bulk Delete",BULK_RESTORE:"Country Bulk Restore"},SETTING:{INDEX:"Setting Index"}}),mt={key:0,class:"layout-menuitem-root-text"},pt=["href","target"],dt={class:"layout-menuitem-text"},Et={key:0,class:"pi pi-fw pi-angle-down layout-submenu-toggler"},ft={class:"layout-menuitem-text"},gt={class:"layout-submenu"},bt={__name:"AppMenuItem",props:{item:{type:Object,default:()=>({})},index:{type:Number,default:0},root:{type:Boolean,default:!0},parentItemKey:{type:String,default:null}},setup(t){const{layoutState:o,setActiveMenuItem:e,toggleMenu:s}=F(),a=t,c=Z(),i=y(()=>c.url),l=H(!1),m=H("");function b(d){var g;if(!d)return!1;if(d.to&&!d.items)try{const w=new URL(d.to,window.location.origin).pathname;return i.value===w||i.value.startsWith(w+"/")}catch{return i.value===d.to||i.value.startsWith(d.to+"/")}return(g=d.items)!=null&&g.length?d.items.some(b):!1}Ie(()=>{m.value=a.parentItemKey?`${a.parentItemKey}-${a.index}`:String(a.index);const d=o.activeMenuItem;l.value=d===m.value||(d?d.startsWith(m.value+"-"):!1),a.item.items&&b(a.item)&&e(m.value)}),G(()=>o.activeMenuItem,d=>{l.value=d===m.value||!!d&&d.startsWith(m.value+"-")}),G(()=>i.value,()=>{a.item.items&&b(a.item)&&e(m.value)});function n(d,g){if(g.disabled){d.preventDefault();return}(g.to||g.url)&&(o.staticMenuMobileActive||o.overlayMenuActive)&&s(),g.command&&g.command({originalEvent:d,item:g});const w=g.items&&l.value?a.parentItemKey:m.value;e(w)}function f(d){return b(d)}return(d,g)=>{const w=$("AppMenuItem",!0);return u(),E("li",{class:C({"layout-root-menuitem":t.root,"active-menuitem":l.value})},[t.root&&t.item.visible!==!1?(u(),E("div",mt,R(t.item.label),1)):P("",!0),(!t.item.to||t.item.items)&&t.item.visible!==!1?(u(),E("a",{key:1,href:t.item.url||"#",onClick:g[0]||(g[0]=Te(A=>n(A,t.item),["prevent"])),class:C(t.item.class),target:t.item.target,tabindex:"0"},[p("i",{class:C([t.item.icon,"layout-menuitem-icon"])},null,2),p("span",dt,R(t.item.label),1),t.item.items?(u(),E("i",Et)):P("",!0)],10,pt)):P("",!0),t.item.to&&!t.item.items&&t.item.visible!==!1?(u(),S(h(re),{key:2,onClick:g[1]||(g[1]=A=>n(A,t.item)),class:C([t.item.class,{"active-route":f(t.item)}]),tabindex:"0",href:t.item.to},{default:U(()=>[p("i",{class:C([t.item.icon,"layout-menuitem-icon"])},null,2),p("span",ft,R(t.item.label),1)]),_:1},8,["class","href"])):P("",!0),t.item.items&&t.item.visible!==!1?(u(),S(De,{key:3,name:"layout-submenu"},{default:U(()=>[ne(p("ul",gt,[(u(!0),E(O,null,K(t.item.items,(A,J)=>(u(),S(w,{key:`${A.label}-${J}`,index:J,item:A,parentItemKey:m.value,root:!1},null,8,["index","item","parentItemKey"]))),128))],512),[[Se,t.root?!0:l.value]])]),_:1})):P("",!0)],2)}}},yt={class:"layout-menu"},ht={key:1,class:"menu-separator"},vt={__name:"AppMenu",setup(t){const{t:o,locale:e}=ae(),{initLocale:s}=le();s();const a=y(()=>e.value),c=y(()=>(a.value,[{label:o("menu.products"),icon:"pi pi-box",permissionMode:"any",permissions:[r.CATEGORY.INDEX,r.BRAND.INDEX,r.TAG.INDEX,r.TAX.INDEX,r.PAYMENT_METHOD.INDEX,r.PRODUCT.INDEX],items:[{label:o("menu.categories"),icon:"pi pi-list",permissionMode:"any",permissions:[r.CATEGORY.INDEX,r.BRAND.INDEX,r.TAG.INDEX,r.TAX.INDEX,r.PAYMENT_METHOD.INDEX],items:[{label:o("menu.attributes"),icon:"pi pi-id-card",to:route("product-attributes.index"),permissions:[r.CATEGORY.INDEX]},{label:o("menu.attribute_values"),icon:"pi pi-id-card",to:route("product-attribute-values.index"),permissions:[r.CATEGORY.INDEX]},{label:o("menu.categories"),icon:"pi pi-id-card",to:route("categories.index"),permissions:[r.CATEGORY.INDEX]},{label:o("menu.brands"),icon:"pi pi-check-square",to:route("brands.index"),permissions:[r.BRAND.INDEX]},{label:o("menu.tags"),icon:"pi pi-check-square",to:route("tags.index"),permissions:[r.TAG.INDEX]},{label:o("menu.taxes"),icon:"pi pi-check-square",to:route("taxes.index"),permissions:[r.TAX.INDEX]},{label:o("menu.payment_methods"),icon:"pi pi-credit-card",to:route("payment-methods.index"),permissions:[r.PAYMENT_METHOD.INDEX]}]},{label:o("menu.products"),icon:"pi pi-shopping-bag",to:route("products.index"),permissions:[r.PRODUCT.INDEX]}]},{label:o("pos.sales"),icon:"pi pi-shopping-cart",permissionMode:"any",permissions:[r.POS.INDEX,r.ORDER.INDEX],items:[{label:o("menu.pos"),icon:"pi pi-desktop",to:route("pos.index"),permissions:[r.POS.INDEX]},{label:o("menu.orders"),icon:"pi pi-receipt",to:route("pos.orders.index"),permissions:[r.ORDER.INDEX]},{label:o("menu.customers"),icon:"pi pi-users",to:route("customers.index"),permissions:[r.CUSTOMER.INDEX]}]},{label:o("menu.users"),icon:"pi pi-users",permissionMode:"any",permissions:[r.ROLE.INDEX,r.PERMISSION.INDEX,r.USER.INDEX],items:[{label:o("menu.permissions"),icon:"pi pi-shield",permissionMode:"any",permissions:[r.ROLE.INDEX,r.PERMISSION.INDEX],items:[{label:o("menu.roles"),icon:"pi pi-id-card",to:route("roles.index"),permissions:[r.ROLE.INDEX]},{label:o("menu.permissions"),icon:"pi pi-lock",to:route("permissions.index"),permissions:[r.PERMISSION.INDEX]}]},{label:o("menu.users"),icon:"pi pi-user",to:route("users.index"),permissions:[r.USER.INDEX]}]},{label:o("menu.settings"),icon:"pi pi-cog",permissionMode:"any",permissions:[r.BRANCH.INDEX,r.WAREHOUSE.INDEX,r.BASE_UNIT.INDEX,r.UNIT.INDEX,r.SUPPLIER.INDEX,r.CUSTOMER.INDEX,r.EXPENSE_CATEGORY.INDEX,r.EXPENSE.INDEX,r.COUNTRY.INDEX,r.SETTING.INDEX],items:[{label:o("branches.branches"),icon:"pi pi-building",permissionMode:"any",permissions:[r.BRANCH.INDEX,r.WAREHOUSE.INDEX],items:[{label:o("menu.branches"),icon:"pi pi-building",to:route("branches.index"),permissions:[r.BRANCH.INDEX]},{label:o("menu.warehouses"),icon:"pi pi-warehouse",to:route("warehouses.index"),permissions:[r.WAREHOUSE.INDEX]}]},{label:o("menu.units"),icon:"pi pi-clone",permissionMode:"any",permissions:[r.BASE_UNIT.INDEX,r.UNIT.INDEX,r.SUPPLIER.INDEX],items:[{label:o("menu.base_units"),icon:"pi pi-clone",to:route("base-units.index"),permissions:[r.BASE_UNIT.INDEX]},{label:o("menu.units"),icon:"pi pi-clone",to:route("units.index"),permissions:[r.UNIT.INDEX]},{label:o("menu.suppliers"),icon:"pi pi-truck",to:route("suppliers.index"),permissions:[r.SUPPLIER.INDEX]},{label:o("menu.warranty_guarantees"),icon:"pi pi-clone",to:route("warranty-guarantees.index"),permissions:[r.SUPPLIER.INDEX]}]},{label:o("settings.settings"),icon:"pi pi-wallet",permissionMode:"any",permissions:[r.EXPENSE_CATEGORY.INDEX,r.EXPENSE.INDEX],items:[{label:o("menu.expense_categories"),icon:"pi pi-list",to:route("expense-categories.index"),permissions:[r.EXPENSE_CATEGORY.INDEX]},{label:o("menu.expenses"),icon:"pi pi-wallet",to:route("expenses.index"),permissions:[r.EXPENSE.INDEX]},{label:o("menu.currencies"),icon:"pi pi-money-bill",to:route("currencies.index"),permissions:[r.COUNTRY.INDEX]}]},{label:o("menu.settings"),icon:"pi pi-cog",to:route("settings.general"),permissions:[r.SETTING.INDEX]}]}])),{hasAnyPermission:i}=de(),l=y(()=>{const m=(b=[])=>b.map(n=>{const f=n.items?m(n.items):[],d=i(n.permissions||[]),g=f.length>0;return!d&&!g?null:{...n,items:n.items?f:void 0}}).filter(Boolean);return m(c.value)});return(m,b)=>(u(),E("ul",yt,[(u(!0),E(O,null,K(l.value,(n,f)=>(u(),E(O,{key:`${n.label}-${f}`},[n.separator?(u(),E("li",ht)):(u(),S(bt,{key:0,item:n,index:f,root:!0},null,8,["item","index"]))],64))),128))]))}},It={class:"layout-sidebar card"},Tt={__name:"AppSidebar",setup(t){return(o,e)=>(u(),E("div",It,[I(vt)]))}},St={class:"flex items-center gap-2"},Dt=["value"],Ct=["value"],Rt={__name:"BranchSwitcher",setup(t){const o=Z(),e=y(()=>o.props.branches||[]),s=y(()=>o.props.auth.user.branch_id),a=c=>{B.post(route("branch.switch"),{branch_id:c.target.value},{preserveScroll:!0,preserveState:!0})};return(c,i)=>(u(),E("div",St,[i[1]||(i[1]=p("label",{class:"text-sm text-slate-600 font-medium hidden md:block"}," Branch ",-1)),p("select",{class:"px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 hover:border-indigo-400 transition min-w-[120px]",value:s.value,onChange:a},[i[0]||(i[0]=p("option",{disabled:"",value:""},"Select Branch",-1)),(u(!0),E(O,null,K(e.value,l=>(u(),E("option",{key:l.id,value:l.id},R(l.name),9,Ct))),128))],40,Dt)]))}},xt={class:"layout-topbar flex items-center justify-between px-4 bg-white border-b shadow-sm"},Ot={class:"flex items-center gap-3"},wt={class:"flex items-center gap-4"},At={class:"text-sm font-medium"},Pt={class:"text-sm font-medium"},Bt={__name:"AppTopbar",setup(t){const{t:o}=ae(),{toggleMenu:e}=F(),{user:s}=de(),a=y(()=>route().current()),c=i=>a.value.startsWith(i);return(i,l)=>{const m=Pe,b=ke;return u(),E("div",xt,[p("div",Ot,[p("button",{class:"layout-menu-button layout-topbar-action",onClick:l[0]||(l[0]=(...n)=>h(e)&&h(e)(...n))},l[4]||(l[4]=[p("i",{class:"pi pi-bars text-xl"},null,-1)])),I(h(re),{href:"/dashboard",class:"layout-topbar-logo flex items-center gap-2"},{default:U(()=>[I(we)]),_:1})]),p("div",wt,[I(Ae),I(Rt),p("button",{onClick:l[1]||(l[1]=n=>h(B).visit(i.route("pos.index"))),class:C(["flex items-center gap-2 px-3 py-2 rounded-lg transition",c("pos.index")?"bg-indigo-600 text-white shadow-sm":"hover:bg-indigo-50 text-slate-700"])},[l[5]||(l[5]=p("i",{class:"pi pi-desktop text-lg"},null,-1)),p("span",At,R(h(o)("menu.pos")),1)],2),p("button",{onClick:l[2]||(l[2]=n=>h(B).visit(i.route("pos.orders.index"))),class:C(["flex items-center gap-2 px-3 py-2 rounded-lg transition",c("pos.orders.index")?"bg-emerald-600 text-white shadow-sm":"hover:bg-emerald-50 text-slate-700"])},[l[6]||(l[6]=p("i",{class:"pi pi-receipt text-lg"},null,-1)),p("span",Pt,R(h(o)("menu.orders")),1)],2),I(m,{ref:"menu",popup:!0,id:"top_profile_menu",model:[{label:h(o)("menu.settings"),icon:"pi pi-cog",command:()=>h(B).visit(i.route("profile.edit"))},{label:h(o)("auth.logout"),icon:"pi pi-sign-out",command:()=>h(B).post(i.route("logout"))}]},null,8,["model"]),I(b,{type:"button",label:h(s).name,icon:"pi pi-user",class:"layout-topbar-action1",variant:"outlined",onClick:l[3]||(l[3]=n=>i.$refs.menu.toggle(n))},null,8,["label"])])])}}},Ut={class:"layout-main-container"},kt={class:"layout-main"},Vt={__name:"AuthenticatedLayout",setup(t){const{layoutConfig:o,layoutState:e,isSidebarActive:s}=F(),{initLocale:a}=le();a();const c=H(null);G(s,n=>{n?l():m()});const i=y(()=>({"layout-overlay":o.menuMode==="overlay","layout-static":o.menuMode==="static","layout-static-inactive":e.staticMenuDesktopInactive&&o.menuMode==="static","layout-overlay-active":e.overlayMenuActive,"layout-mobile-active":e.staticMenuMobileActive}));function l(){c.value||(c.value=n=>{b(n)&&(e.overlayMenuActive=!1,e.staticMenuMobileActive=!1,e.menuHoverActive=!1)},document.addEventListener("click",c.value))}function m(){c.value&&(document.removeEventListener("click",c),c.value=null)}function b(n){const f=document.querySelector(".layout-sidebar"),d=document.querySelector(".layout-menu-button");return!(f.isSameNode(n.target)||f.contains(n.target)||d.isSameNode(n.target)||d.contains(n.target))}return(n,f)=>{const d=me;return u(),E(O,null,[I(Be),p("div",{class:C(["layout-wrapper bg-blue-50",i.value])},[I(Bt),I(Tt),p("div",Ut,[p("div",kt,[Ce(n.$slots,"default")]),I(ut)]),f[0]||(f[0]=p("div",{class:"layout-mask animate-fadein"},null,-1))],2),I(d)],64)}}};export{Vt as _};
