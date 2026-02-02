import{B as pe,c as p,o as c,b as f,m as b,af as ke,x as Re,aR as O,A as fe,g as y,I as P,d as v,G as C,t as k,a1 as we,K as te,z as N,w,a as I,aS as Le,H as B,N as T,aT as be,e as he,M as ne,r as G,aU as Ae,P as Z,F as x,S as Pe,u as D,R as ge,a4 as Ue,T as Ee,n as Be,s as Me,q as F,v as Ne,j as _,X as j,h as ie,aJ as Xe,D as W,W as M}from"./app-DNIPLehX.js";import{s as ye,Z as U,O as Ke,C as He,U as re}from"./index-CdRNdLRW.js";import{s as $}from"./index-Ho6OOvno.js";import{s as ae}from"./index-BbsSO-Ls.js";import{s as ve}from"./index-XRiuXSsq.js";import{s as _e}from"./index-DaqGkxDl.js";import{s as le}from"./index-CJ-xGX8c.js";import{R as Ie}from"./index-aP7FDtIt.js";import{_ as je}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{a as ze}from"./index-DCJua8eU.js";import{A as Ve,_ as We}from"./FavIcons-1Zr4kX6g.js";function X(e){"@babel/helpers - typeof";return X=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(n){return typeof n}:function(n){return n&&typeof Symbol=="function"&&n.constructor===Symbol&&n!==Symbol.prototype?"symbol":typeof n},X(e)}function z(e,n,t){return(n=Ge(n))in e?Object.defineProperty(e,n,{value:t,enumerable:!0,configurable:!0,writable:!0}):e[n]=t,e}function Ge(e){var n=$e(e,"string");return X(n)=="symbol"?n:n+""}function $e(e,n){if(X(e)!="object"||!e)return e;var t=e[Symbol.toPrimitive];if(t!==void 0){var s=t.call(e,n);if(X(s)!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return(n==="string"?String:Number)(e)}var Fe=function(n){var t=n.dt;return`
.p-toast {
    width: `.concat(t("toast.width"),`;
    white-space: pre-line;
    word-break: break-word;
}

.p-toast-message {
    margin: 0 0 1rem 0;
}

.p-toast-message-icon {
    flex-shrink: 0;
    font-size: `).concat(t("toast.icon.size"),`;
    width: `).concat(t("toast.icon.size"),`;
    height: `).concat(t("toast.icon.size"),`;
}

.p-toast-message-content {
    display: flex;
    align-items: flex-start;
    padding: `).concat(t("toast.content.padding"),`;
    gap: `).concat(t("toast.content.gap"),`;
}

.p-toast-message-text {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    gap: `).concat(t("toast.text.gap"),`;
}

.p-toast-summary {
    font-weight: `).concat(t("toast.summary.font.weight"),`;
    font-size: `).concat(t("toast.summary.font.size"),`;
}

.p-toast-detail {
    font-weight: `).concat(t("toast.detail.font.weight"),`;
    font-size: `).concat(t("toast.detail.font.size"),`;
}

.p-toast-close-button {
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    background: transparent;
    transition: background `).concat(t("toast.transition.duration"),", color ").concat(t("toast.transition.duration"),", outline-color ").concat(t("toast.transition.duration"),", box-shadow ").concat(t("toast.transition.duration"),`;
    outline-color: transparent;
    color: inherit;
    width: `).concat(t("toast.close.button.width"),`;
    height: `).concat(t("toast.close.button.height"),`;
    border-radius: `).concat(t("toast.close.button.border.radius"),`;
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
    border-width: `).concat(t("toast.border.width"),`;
    border-style: solid;
    backdrop-filter: blur(`).concat(t("toast.blur"),`);
    border-radius: `).concat(t("toast.border.radius"),`;
}

.p-toast-close-icon {
    font-size: `).concat(t("toast.close.icon.size"),`;
    width: `).concat(t("toast.close.icon.size"),`;
    height: `).concat(t("toast.close.icon.size"),`;
}

.p-toast-close-button:focus-visible {
    outline-width: `).concat(t("focus.ring.width"),`;
    outline-style: `).concat(t("focus.ring.style"),`;
    outline-offset: `).concat(t("focus.ring.offset"),`;
}

.p-toast-message-info {
    background: `).concat(t("toast.info.background"),`;
    border-color: `).concat(t("toast.info.border.color"),`;
    color: `).concat(t("toast.info.color"),`;
    box-shadow: `).concat(t("toast.info.shadow"),`;
}

.p-toast-message-info .p-toast-detail {
    color: `).concat(t("toast.info.detail.color"),`;
}

.p-toast-message-info .p-toast-close-button:focus-visible {
    outline-color: `).concat(t("toast.info.close.button.focus.ring.color"),`;
    box-shadow: `).concat(t("toast.info.close.button.focus.ring.shadow"),`;
}

.p-toast-message-info .p-toast-close-button:hover {
    background: `).concat(t("toast.info.close.button.hover.background"),`;
}

.p-toast-message-success {
    background: `).concat(t("toast.success.background"),`;
    border-color: `).concat(t("toast.success.border.color"),`;
    color: `).concat(t("toast.success.color"),`;
    box-shadow: `).concat(t("toast.success.shadow"),`;
}

.p-toast-message-success .p-toast-detail {
    color: `).concat(t("toast.success.detail.color"),`;
}

.p-toast-message-success .p-toast-close-button:focus-visible {
    outline-color: `).concat(t("toast.success.close.button.focus.ring.color"),`;
    box-shadow: `).concat(t("toast.success.close.button.focus.ring.shadow"),`;
}

.p-toast-message-success .p-toast-close-button:hover {
    background: `).concat(t("toast.success.close.button.hover.background"),`;
}

.p-toast-message-warn {
    background: `).concat(t("toast.warn.background"),`;
    border-color: `).concat(t("toast.warn.border.color"),`;
    color: `).concat(t("toast.warn.color"),`;
    box-shadow: `).concat(t("toast.warn.shadow"),`;
}

.p-toast-message-warn .p-toast-detail {
    color: `).concat(t("toast.warn.detail.color"),`;
}

.p-toast-message-warn .p-toast-close-button:focus-visible {
    outline-color: `).concat(t("toast.warn.close.button.focus.ring.color"),`;
    box-shadow: `).concat(t("toast.warn.close.button.focus.ring.shadow"),`;
}

.p-toast-message-warn .p-toast-close-button:hover {
    background: `).concat(t("toast.warn.close.button.hover.background"),`;
}

.p-toast-message-error {
    background: `).concat(t("toast.error.background"),`;
    border-color: `).concat(t("toast.error.border.color"),`;
    color: `).concat(t("toast.error.color"),`;
    box-shadow: `).concat(t("toast.error.shadow"),`;
}

.p-toast-message-error .p-toast-detail {
    color: `).concat(t("toast.error.detail.color"),`;
}

.p-toast-message-error .p-toast-close-button:focus-visible {
    outline-color: `).concat(t("toast.error.close.button.focus.ring.color"),`;
    box-shadow: `).concat(t("toast.error.close.button.focus.ring.shadow"),`;
}

.p-toast-message-error .p-toast-close-button:hover {
    background: `).concat(t("toast.error.close.button.hover.background"),`;
}

.p-toast-message-secondary {
    background: `).concat(t("toast.secondary.background"),`;
    border-color: `).concat(t("toast.secondary.border.color"),`;
    color: `).concat(t("toast.secondary.color"),`;
    box-shadow: `).concat(t("toast.secondary.shadow"),`;
}

.p-toast-message-secondary .p-toast-detail {
    color: `).concat(t("toast.secondary.detail.color"),`;
}

.p-toast-message-secondary .p-toast-close-button:focus-visible {
    outline-color: `).concat(t("toast.secondary.close.button.focus.ring.color"),`;
    box-shadow: `).concat(t("toast.secondary.close.button.focus.ring.shadow"),`;
}

.p-toast-message-secondary .p-toast-close-button:hover {
    background: `).concat(t("toast.secondary.close.button.hover.background"),`;
}

.p-toast-message-contrast {
    background: `).concat(t("toast.contrast.background"),`;
    border-color: `).concat(t("toast.contrast.border.color"),`;
    color: `).concat(t("toast.contrast.color"),`;
    box-shadow: `).concat(t("toast.contrast.shadow"),`;
}

.p-toast-message-contrast .p-toast-detail {
    color: `).concat(t("toast.contrast.detail.color"),`;
}

.p-toast-message-contrast .p-toast-close-button:focus-visible {
    outline-color: `).concat(t("toast.contrast.close.button.focus.ring.color"),`;
    box-shadow: `).concat(t("toast.contrast.close.button.focus.ring.shadow"),`;
}

.p-toast-message-contrast .p-toast-close-button:hover {
    background: `).concat(t("toast.contrast.close.button.hover.background"),`;
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
`)},Ze={root:function(n){var t=n.position;return{position:"fixed",top:t==="top-right"||t==="top-left"||t==="top-center"?"20px":t==="center"?"50%":null,right:(t==="top-right"||t==="bottom-right")&&"20px",bottom:(t==="bottom-left"||t==="bottom-right"||t==="bottom-center")&&"20px",left:t==="top-left"||t==="bottom-left"?"20px":t==="center"||t==="top-center"||t==="bottom-center"?"50%":null}}},Ye={root:function(n){var t=n.props;return["p-toast p-component p-toast-"+t.position]},message:function(n){var t=n.props;return["p-toast-message",{"p-toast-message-info":t.message.severity==="info"||t.message.severity===void 0,"p-toast-message-warn":t.message.severity==="warn","p-toast-message-error":t.message.severity==="error","p-toast-message-success":t.message.severity==="success","p-toast-message-secondary":t.message.severity==="secondary","p-toast-message-contrast":t.message.severity==="contrast"}]},messageContent:"p-toast-message-content",messageIcon:function(n){var t=n.props;return["p-toast-message-icon",z(z(z(z({},t.infoIcon,t.message.severity==="info"),t.warnIcon,t.message.severity==="warn"),t.errorIcon,t.message.severity==="error"),t.successIcon,t.message.severity==="success")]},messageText:"p-toast-message-text",summary:"p-toast-summary",detail:"p-toast-detail",closeButton:"p-toast-close-button",closeIcon:"p-toast-close-icon"},Je=pe.extend({name:"toast",theme:Fe,classes:Ye,inlineStyles:Ze}),Y={name:"ExclamationTriangleIcon",extends:ve};function Qe(e,n,t,s,i,o){return c(),p("svg",b({width:"14",height:"14",viewBox:"0 0 14 14",fill:"none",xmlns:"http://www.w3.org/2000/svg"},e.pti()),n[0]||(n[0]=[f("path",{d:"M13.4018 13.1893H0.598161C0.49329 13.189 0.390283 13.1615 0.299143 13.1097C0.208003 13.0578 0.131826 12.9832 0.0780112 12.8932C0.0268539 12.8015 0 12.6982 0 12.5931C0 12.4881 0.0268539 12.3848 0.0780112 12.293L6.47985 1.08982C6.53679 1.00399 6.61408 0.933574 6.70484 0.884867C6.7956 0.836159 6.897 0.810669 7 0.810669C7.103 0.810669 7.2044 0.836159 7.29516 0.884867C7.38592 0.933574 7.46321 1.00399 7.52015 1.08982L13.922 12.293C13.9731 12.3848 14 12.4881 14 12.5931C14 12.6982 13.9731 12.8015 13.922 12.8932C13.8682 12.9832 13.792 13.0578 13.7009 13.1097C13.6097 13.1615 13.5067 13.189 13.4018 13.1893ZM1.63046 11.989H12.3695L7 2.59425L1.63046 11.989Z",fill:"currentColor"},null,-1),f("path",{d:"M6.99996 8.78801C6.84143 8.78594 6.68997 8.72204 6.57787 8.60993C6.46576 8.49782 6.40186 8.34637 6.39979 8.18784V5.38703C6.39979 5.22786 6.46302 5.0752 6.57557 4.96265C6.68813 4.85009 6.84078 4.78686 6.99996 4.78686C7.15914 4.78686 7.31179 4.85009 7.42435 4.96265C7.5369 5.0752 7.60013 5.22786 7.60013 5.38703V8.18784C7.59806 8.34637 7.53416 8.49782 7.42205 8.60993C7.30995 8.72204 7.15849 8.78594 6.99996 8.78801Z",fill:"currentColor"},null,-1),f("path",{d:"M6.99996 11.1887C6.84143 11.1866 6.68997 11.1227 6.57787 11.0106C6.46576 10.8985 6.40186 10.7471 6.39979 10.5885V10.1884C6.39979 10.0292 6.46302 9.87658 6.57557 9.76403C6.68813 9.65147 6.84078 9.58824 6.99996 9.58824C7.15914 9.58824 7.31179 9.65147 7.42435 9.76403C7.5369 9.87658 7.60013 10.0292 7.60013 10.1884V10.5885C7.59806 10.7471 7.53416 10.8985 7.42205 11.0106C7.30995 11.1227 7.15849 11.1866 6.99996 11.1887Z",fill:"currentColor"},null,-1)]),16)}Y.render=Qe;var J={name:"InfoCircleIcon",extends:ve};function qe(e,n,t,s,i,o){return c(),p("svg",b({width:"14",height:"14",viewBox:"0 0 14 14",fill:"none",xmlns:"http://www.w3.org/2000/svg"},e.pti()),n[0]||(n[0]=[f("path",{"fill-rule":"evenodd","clip-rule":"evenodd",d:"M3.11101 12.8203C4.26215 13.5895 5.61553 14 7 14C8.85652 14 10.637 13.2625 11.9497 11.9497C13.2625 10.637 14 8.85652 14 7C14 5.61553 13.5895 4.26215 12.8203 3.11101C12.0511 1.95987 10.9579 1.06266 9.67879 0.532846C8.3997 0.00303296 6.99224 -0.13559 5.63437 0.134506C4.2765 0.404603 3.02922 1.07129 2.05026 2.05026C1.07129 3.02922 0.404603 4.2765 0.134506 5.63437C-0.13559 6.99224 0.00303296 8.3997 0.532846 9.67879C1.06266 10.9579 1.95987 12.0511 3.11101 12.8203ZM3.75918 2.14976C4.71846 1.50879 5.84628 1.16667 7 1.16667C8.5471 1.16667 10.0308 1.78125 11.1248 2.87521C12.2188 3.96918 12.8333 5.45291 12.8333 7C12.8333 8.15373 12.4912 9.28154 11.8502 10.2408C11.2093 11.2001 10.2982 11.9478 9.23232 12.3893C8.16642 12.8308 6.99353 12.9463 5.86198 12.7212C4.73042 12.4962 3.69102 11.9406 2.87521 11.1248C2.05941 10.309 1.50384 9.26958 1.27876 8.13803C1.05367 7.00647 1.16919 5.83358 1.61071 4.76768C2.05222 3.70178 2.79989 2.79074 3.75918 2.14976ZM7.00002 4.8611C6.84594 4.85908 6.69873 4.79698 6.58977 4.68801C6.48081 4.57905 6.4187 4.43185 6.41669 4.27776V3.88888C6.41669 3.73417 6.47815 3.58579 6.58754 3.4764C6.69694 3.367 6.84531 3.30554 7.00002 3.30554C7.15473 3.30554 7.3031 3.367 7.4125 3.4764C7.52189 3.58579 7.58335 3.73417 7.58335 3.88888V4.27776C7.58134 4.43185 7.51923 4.57905 7.41027 4.68801C7.30131 4.79698 7.1541 4.85908 7.00002 4.8611ZM7.00002 10.6945C6.84594 10.6925 6.69873 10.6304 6.58977 10.5214C6.48081 10.4124 6.4187 10.2652 6.41669 10.1111V6.22225C6.41669 6.06754 6.47815 5.91917 6.58754 5.80977C6.69694 5.70037 6.84531 5.63892 7.00002 5.63892C7.15473 5.63892 7.3031 5.70037 7.4125 5.80977C7.52189 5.91917 7.58335 6.06754 7.58335 6.22225V10.1111C7.58134 10.2652 7.51923 10.4124 7.41027 10.5214C7.30131 10.6304 7.1541 10.6925 7.00002 10.6945Z",fill:"currentColor"},null,-1)]),16)}J.render=qe;var et={name:"BaseToast",extends:$,props:{group:{type:String,default:null},position:{type:String,default:"top-right"},autoZIndex:{type:Boolean,default:!0},baseZIndex:{type:Number,default:0},breakpoints:{type:Object,default:null},closeIcon:{type:String,default:void 0},infoIcon:{type:String,default:void 0},warnIcon:{type:String,default:void 0},errorIcon:{type:String,default:void 0},successIcon:{type:String,default:void 0},closeButtonProps:{type:null,default:null}},style:Je,provide:function(){return{$pcToast:this,$parentInstance:this}}},Te={name:"ToastMessage",hostName:"Toast",extends:$,emits:["close"],closeTimeout:null,props:{message:{type:null,default:null},templates:{type:Object,default:null},closeIcon:{type:String,default:null},infoIcon:{type:String,default:null},warnIcon:{type:String,default:null},errorIcon:{type:String,default:null},successIcon:{type:String,default:null},closeButtonProps:{type:null,default:null}},mounted:function(){var n=this;this.message.life&&(this.closeTimeout=setTimeout(function(){n.close({message:n.message,type:"life-end"})},this.message.life))},beforeUnmount:function(){this.clearCloseTimeout()},methods:{close:function(n){this.$emit("close",n)},onCloseClick:function(){this.clearCloseTimeout(),this.close({message:this.message,type:"close"})},clearCloseTimeout:function(){this.closeTimeout&&(clearTimeout(this.closeTimeout),this.closeTimeout=null)}},computed:{iconComponent:function(){return{info:!this.infoIcon&&J,success:!this.successIcon&&ae,warn:!this.warnIcon&&Y,error:!this.errorIcon&&le}[this.message.severity]},closeAriaLabel:function(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.close:void 0}},components:{TimesIcon:_e,InfoCircleIcon:J,CheckIcon:ae,ExclamationTriangleIcon:Y,TimesCircleIcon:le},directives:{ripple:Ie}};function K(e){"@babel/helpers - typeof";return K=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(n){return typeof n}:function(n){return n&&typeof Symbol=="function"&&n.constructor===Symbol&&n!==Symbol.prototype?"symbol":typeof n},K(e)}function ce(e,n){var t=Object.keys(e);if(Object.getOwnPropertySymbols){var s=Object.getOwnPropertySymbols(e);n&&(s=s.filter(function(i){return Object.getOwnPropertyDescriptor(e,i).enumerable})),t.push.apply(t,s)}return t}function ue(e){for(var n=1;n<arguments.length;n++){var t=arguments[n]!=null?arguments[n]:{};n%2?ce(Object(t),!0).forEach(function(s){tt(e,s,t[s])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(t)):ce(Object(t)).forEach(function(s){Object.defineProperty(e,s,Object.getOwnPropertyDescriptor(t,s))})}return e}function tt(e,n,t){return(n=nt(n))in e?Object.defineProperty(e,n,{value:t,enumerable:!0,configurable:!0,writable:!0}):e[n]=t,e}function nt(e){var n=ot(e,"string");return K(n)=="symbol"?n:n+""}function ot(e,n){if(K(e)!="object"||!e)return e;var t=e[Symbol.toPrimitive];if(t!==void 0){var s=t.call(e,n);if(K(s)!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return(n==="string"?String:Number)(e)}var st=["aria-label"];function it(e,n,t,s,i,o){var r=fe("ripple");return c(),p("div",b({class:[e.cx("message"),t.message.styleClass],role:"alert","aria-live":"assertive","aria-atomic":"true"},e.ptm("message")),[t.templates.container?(c(),y(P(t.templates.container),{key:0,message:t.message,closeCallback:o.onCloseClick},null,8,["message","closeCallback"])):(c(),p("div",b({key:1,class:[e.cx("messageContent"),t.message.contentStyleClass]},e.ptm("messageContent")),[t.templates.message?(c(),y(P(t.templates.message),{key:1,message:t.message},null,8,["message"])):(c(),p(C,{key:0},[(c(),y(P(t.templates.messageicon?t.templates.messageicon:t.templates.icon?t.templates.icon:o.iconComponent&&o.iconComponent.name?o.iconComponent:"span"),b({class:e.cx("messageIcon")},e.ptm("messageIcon")),null,16,["class"])),f("div",b({class:e.cx("messageText")},e.ptm("messageText")),[f("span",b({class:e.cx("summary")},e.ptm("summary")),k(t.message.summary),17),f("div",b({class:e.cx("detail")},e.ptm("detail")),k(t.message.detail),17)],16)],64)),t.message.closable!==!1?(c(),p("div",we(b({key:2},e.ptm("buttonContainer"))),[te((c(),p("button",b({class:e.cx("closeButton"),type:"button","aria-label":o.closeAriaLabel,onClick:n[0]||(n[0]=function(){return o.onCloseClick&&o.onCloseClick.apply(o,arguments)}),autofocus:""},ue(ue({},t.closeButtonProps),e.ptm("closeButton"))),[(c(),y(P(t.templates.closeicon||"TimesIcon"),b({class:[e.cx("closeIcon"),t.closeIcon]},e.ptm("closeIcon")),null,16,["class"]))],16,st)),[[r]])],16)):v("",!0)],16))],16)}Te.render=it;function rt(e){return ut(e)||ct(e)||lt(e)||at()}function at(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function lt(e,n){if(e){if(typeof e=="string")return Q(e,n);var t={}.toString.call(e).slice(8,-1);return t==="Object"&&e.constructor&&(t=e.constructor.name),t==="Map"||t==="Set"?Array.from(e):t==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t)?Q(e,n):void 0}}function ct(e){if(typeof Symbol<"u"&&e[Symbol.iterator]!=null||e["@@iterator"]!=null)return Array.from(e)}function ut(e){if(Array.isArray(e))return Q(e)}function Q(e,n){(n==null||n>e.length)&&(n=e.length);for(var t=0,s=Array(n);t<n;t++)s[t]=e[t];return s}var dt=0,Ce={name:"Toast",extends:et,inheritAttrs:!1,emits:["close","life-end"],data:function(){return{messages:[]}},styleElement:null,mounted:function(){O.on("add",this.onAdd),O.on("remove",this.onRemove),O.on("remove-group",this.onRemoveGroup),O.on("remove-all-groups",this.onRemoveAllGroups),this.breakpoints&&this.createStyle()},beforeUnmount:function(){this.destroyStyle(),this.$refs.container&&this.autoZIndex&&U.clear(this.$refs.container),O.off("add",this.onAdd),O.off("remove",this.onRemove),O.off("remove-group",this.onRemoveGroup),O.off("remove-all-groups",this.onRemoveAllGroups)},methods:{add:function(n){n.id==null&&(n.id=dt++),this.messages=[].concat(rt(this.messages),[n])},remove:function(n){var t=this.messages.findIndex(function(s){return s.id===n.message.id});t!==-1&&(this.messages.splice(t,1),this.$emit(n.type,{message:n.message}))},onAdd:function(n){this.group==n.group&&this.add(n)},onRemove:function(n){this.remove({message:n,type:"close"})},onRemoveGroup:function(n){this.group===n&&(this.messages=[])},onRemoveAllGroups:function(){this.messages=[]},onEnter:function(){this.autoZIndex&&U.set("modal",this.$refs.container,this.baseZIndex||this.$primevue.config.zIndex.modal)},onLeave:function(){var n=this;this.$refs.container&&this.autoZIndex&&Re(this.messages)&&setTimeout(function(){U.clear(n.$refs.container)},200)},createStyle:function(){if(!this.styleElement&&!this.isUnstyled){var n;this.styleElement=document.createElement("style"),this.styleElement.type="text/css",ke(this.styleElement,"nonce",(n=this.$primevue)===null||n===void 0||(n=n.config)===null||n===void 0||(n=n.csp)===null||n===void 0?void 0:n.nonce),document.head.appendChild(this.styleElement);var t="";for(var s in this.breakpoints){var i="";for(var o in this.breakpoints[s])i+=o+":"+this.breakpoints[s][o]+"!important;";t+=`
                        @media screen and (max-width: `.concat(s,`) {
                            .p-toast[`).concat(this.$attrSelector,`] {
                                `).concat(i,`
                            }
                        }
                    `)}this.styleElement.innerHTML=t}},destroyStyle:function(){this.styleElement&&(document.head.removeChild(this.styleElement),this.styleElement=null)}},components:{ToastMessage:Te,Portal:ye}};function H(e){"@babel/helpers - typeof";return H=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(n){return typeof n}:function(n){return n&&typeof Symbol=="function"&&n.constructor===Symbol&&n!==Symbol.prototype?"symbol":typeof n},H(e)}function de(e,n){var t=Object.keys(e);if(Object.getOwnPropertySymbols){var s=Object.getOwnPropertySymbols(e);n&&(s=s.filter(function(i){return Object.getOwnPropertyDescriptor(e,i).enumerable})),t.push.apply(t,s)}return t}function mt(e){for(var n=1;n<arguments.length;n++){var t=arguments[n]!=null?arguments[n]:{};n%2?de(Object(t),!0).forEach(function(s){pt(e,s,t[s])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(t)):de(Object(t)).forEach(function(s){Object.defineProperty(e,s,Object.getOwnPropertyDescriptor(t,s))})}return e}function pt(e,n,t){return(n=ft(n))in e?Object.defineProperty(e,n,{value:t,enumerable:!0,configurable:!0,writable:!0}):e[n]=t,e}function ft(e){var n=bt(e,"string");return H(n)=="symbol"?n:n+""}function bt(e,n){if(H(e)!="object"||!e)return e;var t=e[Symbol.toPrimitive];if(t!==void 0){var s=t.call(e,n);if(H(s)!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return(n==="string"?String:Number)(e)}function ht(e,n,t,s,i,o){var r=N("ToastMessage"),d=N("Portal");return c(),y(d,null,{default:w(function(){return[f("div",b({ref:"container",class:e.cx("root"),style:e.sx("root",!0,{position:e.position})},e.ptmi("root")),[I(Le,b({name:"p-toast-message",tag:"div",onEnter:o.onEnter,onLeave:o.onLeave},mt({},e.ptm("transition"))),{default:w(function(){return[(c(!0),p(C,null,B(i.messages,function(u){return c(),y(r,{key:u.id,message:u,templates:e.$slots,closeIcon:e.closeIcon,infoIcon:e.infoIcon,warnIcon:e.warnIcon,errorIcon:e.errorIcon,successIcon:e.successIcon,closeButtonProps:e.closeButtonProps,unstyled:e.unstyled,onClose:n[0]||(n[0]=function(m){return o.remove(m)}),pt:e.pt},null,8,["message","templates","closeIcon","infoIcon","warnIcon","errorIcon","successIcon","closeButtonProps","unstyled","pt"])}),128))]}),_:1},16,["onEnter","onLeave"])],16)]}),_:1})}Ce.render=ht;/*! js-cookie v3.0.5 | MIT */function V(e){for(var n=1;n<arguments.length;n++){var t=arguments[n];for(var s in t)e[s]=t[s]}return e}var gt={read:function(e){return e[0]==='"'&&(e=e.slice(1,-1)),e.replace(/(%[\dA-F]{2})+/gi,decodeURIComponent)},write:function(e){return encodeURIComponent(e).replace(/%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g,decodeURIComponent)}};function q(e,n){function t(i,o,r){if(!(typeof document>"u")){r=V({},n,r),typeof r.expires=="number"&&(r.expires=new Date(Date.now()+r.expires*864e5)),r.expires&&(r.expires=r.expires.toUTCString()),i=encodeURIComponent(i).replace(/%(2[346B]|5E|60|7C)/g,decodeURIComponent).replace(/[()]/g,escape);var d="";for(var u in r)r[u]&&(d+="; "+u,r[u]!==!0&&(d+="="+r[u].split(";")[0]));return document.cookie=i+"="+e.write(o,i)+d}}function s(i){if(!(typeof document>"u"||arguments.length&&!i)){for(var o=document.cookie?document.cookie.split("; "):[],r={},d=0;d<o.length;d++){var u=o[d].split("="),m=u.slice(1).join("=");try{var l=decodeURIComponent(u[0]);if(r[l]=e.read(m,l),i===l)break}catch{}}return i?r[i]:r}}return Object.create({set:t,get:s,remove:function(i,o){t(i,"",V({},o,{expires:-1}))},withAttributes:function(i){return q(this.converter,V({},this.attributes,i))},withConverter:function(i){return q(V({},this.converter,i),this.attributes)}},{attributes:{value:Object.freeze(n)},converter:{value:Object.freeze(e)}})}var Se=q(gt,{path:"/"});const R=be({preset:"Aura",primary:"emerald",surface:null,darkTheme:!1,menuMode:"static"}),S=be({staticMenuDesktopInactive:Se.get("menuState")==="true"||!1,overlayMenuActive:!1,profileSidebarVisible:!1,configSidebarVisible:!1,staticMenuMobileActive:!1,menuHoverActive:!1,activeMenuItem:null});function oe(){const e=u=>{S.activeMenuItem=u.value||u},n=()=>{if(!document.startViewTransition){t();return}document.startViewTransition(()=>t(event))},t=()=>{R.darkTheme=!R.darkTheme,document.documentElement.classList.toggle("app-dark")},s=()=>{R.menuMode==="overlay"&&(S.overlayMenuActive=!S.overlayMenuActive),window.innerWidth>991?(S.staticMenuDesktopInactive=!S.staticMenuDesktopInactive,Se.set("menuState",S.staticMenuDesktopInactive)):S.staticMenuMobileActive=!S.staticMenuMobileActive},i=T(()=>S.overlayMenuActive||S.staticMenuMobileActive),o=T(()=>R.darkTheme),r=T(()=>R.primary),d=T(()=>R.surface);return{layoutConfig:R,layoutState:S,toggleMenu:s,isSidebarActive:i,isDarkTheme:o,getPrimary:r,getSurface:d,setActiveMenuItem:e,toggleDarkMode:n}}const Et={},yt={class:"layout-footer"};function vt(e,n){return c(),p("div",yt,n[0]||(n[0]=[he(" ECommerce by "),f("a",{href:"#",target:"_blank",rel:"noopener noreferrer",class:"text-primary font-bold hover:underline"},"Sunwarul Islam",-1)]))}const It=je(Et,[["render",vt]]);function De(){const e=ne(),n=T(()=>{var l;return((l=e.props.auth)==null?void 0:l.user)??null}),t=T(()=>{var l;return((l=e.props.auth)==null?void 0:l.roles)??[]}),s=T(()=>{var l;return((l=e.props.auth)==null?void 0:l.permissions)??[]});return{user:n,roles:t,permissions:s,can:l=>s.value.includes(l),hasPermission:l=>l?s.value.includes(l):!0,hasAnyPermission:(l=[])=>l.length?(Array.isArray(l)||(l=[l]),l.some(g=>s.value.includes(g))):!0,hasAllPermissions:(l=[])=>l.length?(Array.isArray(l)||(l=[l]),l.every(g=>s.value.includes(g))):!0,hasRole:l=>l?t.value.includes(l):!0,hasAnyRole:(l=[])=>l.length?(Array.isArray(l)||(l=[l]),l.some(g=>t.value.includes(g))):!0}}const a=Object.freeze({CATEGORY:{INDEX:"Category Index",STORE:"Category Store",UPDATE:"Category Update",EDIT:"Category Edit",DELETE:"Category Delete",SEARCH:"Category Search",EXPORT:"Category Export",BULK_DELETE:"Category Bulk Delete",BULK_RESTORE:"Category Bulk Restore"},BRAND:{INDEX:"Brand Index",STORE:"Brand Store",UPDATE:"Brand Update",EDIT:"Brand Edit",DELETE:"Brand Delete",SEARCH:"Brand Search",EXPORT:"Brand Export",BULK_DELETE:"Brand Bulk Delete",BULK_RESTORE:"Brand Bulk Restore"},TAG:{INDEX:"Tag Index",STORE:"Tag Store",UPDATE:"Tag Update",EDIT:"Tag Edit",DELETE:"Tag Delete",SEARCH:"Tag Search",EXPORT:"Tag Export",BULK_DELETE:"Tag Bulk Delete",BULK_RESTORE:"Tag Bulk Restore"},TAX:{INDEX:"Tax Index",STORE:"Tax Store",UPDATE:"Tax Update",EDIT:"Tax Edit",DELETE:"Tax Delete",SEARCH:"Tax Search",EXPORT:"Tax Export",BULK_DELETE:"Tax Bulk Delete",BULK_RESTORE:"Tax Bulk Restore"},PAYMENT_METHOD:{INDEX:"Payment Methods Index",STORE:"Payment Methods Store",UPDATE:"Payment Methods Update",EDIT:"Payment Methods Edit",DELETE:"Payment Methods Delete",SEARCH:"Payment Methods Search",EXPORT:"Payment Methods Export",BULK_DELETE:"Payment Methods Bulk Delete",BULK_RESTORE:"Payment Methods Bulk Restore"},PRODUCT:{INDEX:"Product Index",STORE:"Product Store",UPDATE:"Product Update",EDIT:"Product Edit",DELETE:"Product Delete",MOVEMENT:"Product Movement",SEARCH:"Product Search",EXPORT:"Product Export",BULK_DELETE:"Product Bulk Delete",BULK_RESTORE:"Product Bulk Restore"},POS:{INDEX:"POS Index"},ORDER:{INDEX:"Order Index",VIEW:"Order View",INVOICE:"Order Invoice",COMPLETE:"Order Complete",VOID:"Order Void"},ROLE:{INDEX:"Role Index",STORE:"Role Store",UPDATE:"Role Update",EDIT:"Role Edit",DELETE:"Role Delete",SEARCH:"Role Search",EXPORT:"Role Export",BULK_DELETE:"Role Bulk Delete",BULK_RESTORE:"Role Bulk Restore"},PERMISSION:{INDEX:"Permission Index",STORE:"Permission Store"},USER:{INDEX:"User Index",STORE:"User Store",UPDATE:"User Update",EDIT:"User Edit",DELETE:"User Delete",SEARCH:"User Search",EXPORT:"User Export",BULK_DELETE:"User Bulk Delete",BULK_RESTORE:"User Bulk Restore"},BRANCH:{INDEX:"Branch Index",STORE:"Branch Store",UPDATE:"Branch Update",EDIT:"Branch Edit",DELETE:"Branch Delete",SEARCH:"Branch Search",EXPORT:"Branch Export",BULK_DELETE:"Branch Bulk Delete",BULK_RESTORE:"Branch Bulk Restore"},WAREHOUSE:{INDEX:"Warehouse Index",STORE:"Warehouse Store",UPDATE:"Warehouse Update",EDIT:"Warehouse Edit",DELETE:"Warehouse Delete",SEARCH:"Warehouse Search",EXPORT:"Warehouse Export",BULK_DELETE:"Warehouse Bulk Delete",BULK_RESTORE:"Warehouse Bulk Restore"},BASE_UNIT:{INDEX:"Base Unit Index",STORE:"Base Unit Store",UPDATE:"Base Unit Update",EDIT:"Base Unit Edit",DELETE:"Base Unit Delete",SEARCH:"Base Unit Search",EXPORT:"Base Unit Export",BULK_DELETE:"Base Unit Bulk Delete",BULK_RESTORE:"Base Unit Bulk Restore"},UNIT:{INDEX:"Unit Index",STORE:"Unit Store",UPDATE:"Unit Update",EDIT:"Unit Edit",DELETE:"Unit Delete",SEARCH:"Unit Search",EXPORT:"Unit Export",BULK_DELETE:"Unit Bulk Delete",BULK_RESTORE:"Unit Bulk Restore"},SUPPLIER:{INDEX:"Supplier Index",STORE:"Supplier Store",UPDATE:"Supplier Update",EDIT:"Supplier Edit",DELETE:"Supplier Delete",SEARCH:"Supplier Search",EXPORT:"Supplier Export",BULK_DELETE:"Supplier Bulk Delete",BULK_RESTORE:"Supplier Bulk Restore"},CUSTOMER:{INDEX:"Customer Index",STORE:"Customer Store",UPDATE:"Customer Update",EDIT:"Customer Edit",DELETE:"Customer Delete",SEARCH:"Customer Search",EXPORT:"Customer Export",BULK_DELETE:"Customer Bulk Delete",BULK_RESTORE:"Customer Bulk Restore"},EXPENSE_CATEGORY:{INDEX:"Expense Categories Index",STORE:"Expense Categories Store",UPDATE:"Expense Categories Update",EDIT:"Expense Categories Edit",DELETE:"Expense Categories Delete",SEARCH:"Expense Categories Search",EXPORT:"Expense Categories Export",BULK_DELETE:"Expense Categories Bulk Delete",BULK_RESTORE:"Expense Categories Bulk Restore"},EXPENSE:{INDEX:"Expense Index",STORE:"Expense Store",UPDATE:"Expense Update",EDIT:"Expense Edit",DELETE:"Expense Delete",SEARCH:"Expense Search",EXPORT:"Expense Export",BULK_DELETE:"Expense Bulk Delete",BULK_RESTORE:"Expense Bulk Restore"},COUNTRY:{INDEX:"Country Index",STORE:"Country Store",UPDATE:"Country Update",EDIT:"Country Edit",DELETE:"Country Delete",SEARCH:"Country Search",EXPORT:"Country Export",BULK_DELETE:"Country Bulk Delete",BULK_RESTORE:"Country Bulk Restore"},SETTING:{INDEX:"Setting Index"}}),Tt={key:0,class:"layout-menuitem-root-text"},Ct=["href","target"],St={class:"layout-menuitem-text"},Dt={key:0,class:"pi pi-fw pi-angle-down layout-submenu-toggler"},xt={class:"layout-menuitem-text"},Ot={class:"layout-submenu"},kt={__name:"AppMenuItem",props:{item:{type:Object,default:()=>({})},index:{type:Number,default:0},root:{type:Boolean,default:!0},parentItemKey:{type:String,default:null}},setup(e){const{layoutState:n,setActiveMenuItem:t,toggleMenu:s}=oe(),i=e,o=ne(),r=T(()=>o.url),d=G(!1),u=G("");function m(h){var E;if(!h)return!1;if(h.to&&!h.items)try{const L=new URL(h.to,window.location.origin).pathname;return r.value===L||r.value.startsWith(L+"/")}catch{return r.value===h.to||r.value.startsWith(h.to+"/")}return(E=h.items)!=null&&E.length?h.items.some(m):!1}Ae(()=>{u.value=i.parentItemKey?`${i.parentItemKey}-${i.index}`:String(i.index);const h=n.activeMenuItem;d.value=h===u.value||(h?h.startsWith(u.value+"-"):!1),i.item.items&&m(i.item)&&t(u.value)}),Z(()=>n.activeMenuItem,h=>{d.value=h===u.value||!!h&&h.startsWith(u.value+"-")}),Z(()=>r.value,()=>{i.item.items&&m(i.item)&&t(u.value)});function l(h,E){if(E.disabled){h.preventDefault();return}(E.to||E.url)&&(n.staticMenuMobileActive||n.overlayMenuActive)&&s(),E.command&&E.command({originalEvent:h,item:E});const L=E.items&&d.value?i.parentItemKey:u.value;t(L)}function g(h){return m(h)}return(h,E)=>{const L=N("AppMenuItem",!0);return c(),p("li",{class:x({"layout-root-menuitem":e.root,"active-menuitem":d.value})},[e.root&&e.item.visible!==!1?(c(),p("div",Tt,k(e.item.label),1)):v("",!0),(!e.item.to||e.item.items)&&e.item.visible!==!1?(c(),p("a",{key:1,href:e.item.url||"#",onClick:E[0]||(E[0]=Pe(A=>l(A,e.item),["prevent"])),class:x(e.item.class),target:e.item.target,tabindex:"0"},[f("i",{class:x([e.item.icon,"layout-menuitem-icon"])},null,2),f("span",St,k(e.item.label),1),e.item.items?(c(),p("i",Dt)):v("",!0)],10,Ct)):v("",!0),e.item.to&&!e.item.items&&e.item.visible!==!1?(c(),y(D(ge),{key:2,onClick:E[1]||(E[1]=A=>l(A,e.item)),class:x([e.item.class,{"active-route":g(e.item)}]),tabindex:"0",href:e.item.to},{default:w(()=>[f("i",{class:x([e.item.icon,"layout-menuitem-icon"])},null,2),f("span",xt,k(e.item.label),1)]),_:1},8,["class","href"])):v("",!0),e.item.items&&e.item.visible!==!1?(c(),y(Ee,{key:3,name:"layout-submenu"},{default:w(()=>[te(f("ul",Ot,[(c(!0),p(C,null,B(e.item.items,(A,se)=>(c(),y(L,{key:`${A.label}-${se}`,index:se,item:A,parentItemKey:u.value,root:!1},null,8,["index","item","parentItemKey"]))),128))],512),[[Ue,e.root?!0:d.value]])]),_:1})):v("",!0)],2)}}},Rt={class:"layout-menu"},wt={key:1,class:"menu-separator"},Lt={__name:"AppMenu",setup(e){const n=G([{label:"Products",icon:"pi pi-box",permissionMode:"any",permissions:[a.CATEGORY.INDEX,a.BRAND.INDEX,a.TAG.INDEX,a.TAX.INDEX,a.PAYMENT_METHOD.INDEX,a.PRODUCT.INDEX],items:[{label:"Catalog",icon:"pi pi-list",permissionMode:"any",permissions:[a.CATEGORY.INDEX,a.BRAND.INDEX,a.TAG.INDEX,a.TAX.INDEX,a.PAYMENT_METHOD.INDEX],items:[{label:"Category",icon:"pi pi-id-card",to:route("categories.index"),permissions:[a.CATEGORY.INDEX]},{label:"Brand",icon:"pi pi-check-square",to:route("brands.index"),permissions:[a.BRAND.INDEX]},{label:"Tag",icon:"pi pi-check-square",to:route("tags.index"),permissions:[a.TAG.INDEX]},{label:"Tax",icon:"pi pi-check-square",to:route("taxes.index"),permissions:[a.TAX.INDEX]},{label:"Payment Method",icon:"pi pi-credit-card",to:route("payment-methods.index"),permissions:[a.PAYMENT_METHOD.INDEX]}]},{label:"Products",icon:"pi pi-shopping-bag",to:route("products.index"),permissions:[a.PRODUCT.INDEX]}]},{label:"Sales",icon:"pi pi-shopping-cart",permissionMode:"any",permissions:[a.POS.INDEX,a.ORDER.INDEX],items:[{label:"Point of Sale",icon:"pi pi-desktop",to:route("pos.index"),permissions:[a.POS.INDEX]},{label:"Orders",icon:"pi pi-receipt",to:route("pos.orders.index"),permissions:[a.ORDER.INDEX]}]},{label:"User Management",icon:"pi pi-users",permissionMode:"any",permissions:[a.ROLE.INDEX,a.PERMISSION.INDEX,a.USER.INDEX],items:[{label:"Access Control",icon:"pi pi-shield",permissionMode:"any",permissions:[a.ROLE.INDEX,a.PERMISSION.INDEX],items:[{label:"Role",icon:"pi pi-id-card",to:route("roles.index"),permissions:[a.ROLE.INDEX]},{label:"Permission",icon:"pi pi-lock",to:route("permissions.index"),permissions:[a.PERMISSION.INDEX]}]},{label:"Users",icon:"pi pi-user",to:route("users.index"),permissions:[a.USER.INDEX]}]},{label:"Setup",icon:"pi pi-cog",permissionMode:"any",permissions:[a.BRANCH.INDEX,a.WAREHOUSE.INDEX,a.BASE_UNIT.INDEX,a.UNIT.INDEX,a.SUPPLIER.INDEX,a.CUSTOMER.INDEX,a.EXPENSE_CATEGORY.INDEX,a.EXPENSE.INDEX,a.COUNTRY.INDEX,a.SETTING.INDEX],items:[{label:"Organization",icon:"pi pi-building",permissionMode:"any",permissions:[a.BRANCH.INDEX,a.WAREHOUSE.INDEX],items:[{label:"Branches",icon:"pi pi-building",to:route("branches.index"),permissions:[a.BRANCH.INDEX]},{label:"Warehouses",icon:"pi pi-warehouse",to:route("warehouses.index"),permissions:[a.WAREHOUSE.INDEX]}]},{label:"Units & Inventory",icon:"pi pi-clone",permissionMode:"any",permissions:[a.BASE_UNIT.INDEX,a.UNIT.INDEX,a.SUPPLIER.INDEX,a.CUSTOMER.INDEX],items:[{label:"Base Units",icon:"pi pi-clone",to:route("base-units.index"),permissions:[a.BASE_UNIT.INDEX]},{label:"Units",icon:"pi pi-clone",to:route("units.index"),permissions:[a.UNIT.INDEX]},{label:"Suppliers",icon:"pi pi-truck",to:route("suppliers.index"),permissions:[a.SUPPLIER.INDEX]},{label:"Customers",icon:"pi pi-users",to:route("customers.index"),permissions:[a.CUSTOMER.INDEX]}]},{label:"Finance",icon:"pi pi-wallet",permissionMode:"any",permissions:[a.EXPENSE_CATEGORY.INDEX,a.EXPENSE.INDEX],items:[{label:"Expense Categories",icon:"pi pi-list",to:route("expense-categories.index"),permissions:[a.EXPENSE_CATEGORY.INDEX]},{label:"Expenses",icon:"pi pi-wallet",to:route("expenses.index"),permissions:[a.EXPENSE.INDEX]},{label:"Currencies",icon:"pi pi-money-bill",to:route("currencies.index"),permissions:[a.COUNTRY.INDEX]}]},{label:"Settings",icon:"pi pi-cog",to:route("settings.general"),permissions:[a.SETTING.INDEX]}]}]),{hasAnyPermission:t}=De(),s=T(()=>{const i=(o=[])=>o.map(r=>{const d=r.items?i(r.items):[],u=t(r.permissions||[]),m=d.length>0;return!u&&!m?null:{...r,items:r.items?d:void 0}}).filter(Boolean);return i(n.value)});return(i,o)=>(c(),p("ul",Rt,[(c(!0),p(C,null,B(s.value,(r,d)=>(c(),p(C,{key:`${r.label}-${d}`},[r.separator?(c(),p("li",wt)):(c(),y(kt,{key:0,item:r,index:d,root:!0},null,8,["item","index"]))],64))),128))]))}},At={class:"layout-sidebar card"},Pt={__name:"AppSidebar",setup(e){return(n,t)=>(c(),p("div",At,[I(Lt)]))}};var Ut=function(n){var t=n.dt;return`
.p-menu {
    background: `.concat(t("menu.background"),`;
    color: `).concat(t("menu.color"),`;
    border: 1px solid `).concat(t("menu.border.color"),`;
    border-radius: `).concat(t("menu.border.radius"),`;
    min-width: 12.5rem;
}

.p-menu-list {
    margin: 0;
    padding: `).concat(t("menu.list.padding"),`;
    outline: 0 none;
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: `).concat(t("menu.list.gap"),`;
}

.p-menu-item-content {
    transition: background `).concat(t("menu.transition.duration"),", color ").concat(t("menu.transition.duration"),`;
    border-radius: `).concat(t("menu.item.border.radius"),`;
    color: `).concat(t("menu.item.color"),`;
}

.p-menu-item-link {
    cursor: pointer;
    display: flex;
    align-items: center;
    text-decoration: none;
    overflow: hidden;
    position: relative;
    color: inherit;
    padding: `).concat(t("menu.item.padding"),`;
    gap: `).concat(t("menu.item.gap"),`;
    user-select: none;
    outline: 0 none;
}

.p-menu-item-label {
    line-height: 1;
}

.p-menu-item-icon {
    color: `).concat(t("menu.item.icon.color"),`;
}

.p-menu-item.p-focus .p-menu-item-content {
    color: `).concat(t("menu.item.focus.color"),`;
    background: `).concat(t("menu.item.focus.background"),`;
}

.p-menu-item.p-focus .p-menu-item-icon {
    color: `).concat(t("menu.item.icon.focus.color"),`;
}

.p-menu-item:not(.p-disabled) .p-menu-item-content:hover {
    color: `).concat(t("menu.item.focus.color"),`;
    background: `).concat(t("menu.item.focus.background"),`;
}

.p-menu-item:not(.p-disabled) .p-menu-item-content:hover .p-menu-item-icon {
    color: `).concat(t("menu.item.icon.focus.color"),`;
}

.p-menu-overlay {
    box-shadow: `).concat(t("menu.shadow"),`;
}

.p-menu-submenu-label {
    background: `).concat(t("menu.submenu.label.background"),`;
    padding: `).concat(t("menu.submenu.label.padding"),`;
    color: `).concat(t("menu.submenu.label.color"),`;
    font-weight: `).concat(t("menu.submenu.label.font.weight"),`;
}

.p-menu-separator {
    border-block-start: 1px solid `).concat(t("menu.separator.border.color"),`;
}
`)},Bt={root:function(n){var t=n.props;return["p-menu p-component",{"p-menu-overlay":t.popup}]},start:"p-menu-start",list:"p-menu-list",submenuLabel:"p-menu-submenu-label",separator:"p-menu-separator",end:"p-menu-end",item:function(n){var t=n.instance;return["p-menu-item",{"p-focus":t.id===t.focusedOptionId,"p-disabled":t.disabled()}]},itemContent:"p-menu-item-content",itemLink:"p-menu-item-link",itemIcon:"p-menu-item-icon",itemLabel:"p-menu-item-label"},Mt=pe.extend({name:"menu",theme:Ut,classes:Bt}),Nt={name:"BaseMenu",extends:$,props:{popup:{type:Boolean,default:!1},model:{type:Array,default:null},appendTo:{type:[String,Object],default:"body"},autoZIndex:{type:Boolean,default:!0},baseZIndex:{type:Number,default:0},tabindex:{type:Number,default:0},ariaLabel:{type:String,default:null},ariaLabelledby:{type:String,default:null}},style:Mt,provide:function(){return{$pcMenu:this,$parentInstance:this}}},xe={name:"Menuitem",hostName:"Menu",extends:$,inheritAttrs:!1,emits:["item-click","item-mousemove"],props:{item:null,templates:null,id:null,focusedOptionId:null,index:null},methods:{getItemProp:function(n,t){return n&&n.item?Xe(n.item[t]):void 0},getPTOptions:function(n){return this.ptm(n,{context:{item:this.item,index:this.index,focused:this.isItemFocused(),disabled:this.disabled()}})},isItemFocused:function(){return this.focusedOptionId===this.id},onItemClick:function(n){var t=this.getItemProp(this.item,"command");t&&t({originalEvent:n,item:this.item.item}),this.$emit("item-click",{originalEvent:n,item:this.item,id:this.id})},onItemMouseMove:function(n){this.$emit("item-mousemove",{originalEvent:n,item:this.item,id:this.id})},visible:function(){return typeof this.item.visible=="function"?this.item.visible():this.item.visible!==!1},disabled:function(){return typeof this.item.disabled=="function"?this.item.disabled():this.item.disabled},label:function(){return typeof this.item.label=="function"?this.item.label():this.item.label},getMenuItemProps:function(n){return{action:b({class:this.cx("itemLink"),tabindex:"-1"},this.getPTOptions("itemLink")),icon:b({class:[this.cx("itemIcon"),n.icon]},this.getPTOptions("itemIcon")),label:b({class:this.cx("itemLabel")},this.getPTOptions("itemLabel"))}}},directives:{ripple:Ie}},Xt=["id","aria-label","aria-disabled","data-p-focused","data-p-disabled"],Kt=["href","target"];function Ht(e,n,t,s,i,o){var r=fe("ripple");return o.visible()?(c(),p("li",b({key:0,id:t.id,class:[e.cx("item"),t.item.class],role:"menuitem",style:t.item.style,"aria-label":o.label(),"aria-disabled":o.disabled()},o.getPTOptions("item"),{"data-p-focused":o.isItemFocused(),"data-p-disabled":o.disabled()||!1}),[f("div",b({class:e.cx("itemContent"),onClick:n[0]||(n[0]=function(d){return o.onItemClick(d)}),onMousemove:n[1]||(n[1]=function(d){return o.onItemMouseMove(d)})},o.getPTOptions("itemContent")),[t.templates.item?t.templates.item?(c(),y(P(t.templates.item),{key:1,item:t.item,label:o.label(),props:o.getMenuItemProps(t.item)},null,8,["item","label","props"])):v("",!0):te((c(),p("a",b({key:0,href:t.item.url,class:e.cx("itemLink"),target:t.item.target,tabindex:"-1"},o.getPTOptions("itemLink")),[t.templates.itemicon?(c(),y(P(t.templates.itemicon),{key:0,item:t.item,class:x(e.cx("itemIcon"))},null,8,["item","class"])):t.item.icon?(c(),p("span",b({key:1,class:[e.cx("itemIcon"),t.item.icon]},o.getPTOptions("itemIcon")),null,16)):v("",!0),f("span",b({class:e.cx("itemLabel")},o.getPTOptions("itemLabel")),k(o.label()),17)],16,Kt)),[[r]])],16)],16,Xt)):v("",!0)}xe.render=Ht;function me(e){return Vt(e)||zt(e)||jt(e)||_t()}function _t(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function jt(e,n){if(e){if(typeof e=="string")return ee(e,n);var t={}.toString.call(e).slice(8,-1);return t==="Object"&&e.constructor&&(t=e.constructor.name),t==="Map"||t==="Set"?Array.from(e):t==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t)?ee(e,n):void 0}}function zt(e){if(typeof Symbol<"u"&&e[Symbol.iterator]!=null||e["@@iterator"]!=null)return Array.from(e)}function Vt(e){if(Array.isArray(e))return ee(e)}function ee(e,n){(n==null||n>e.length)&&(n=e.length);for(var t=0,s=Array(n);t<n;t++)s[t]=e[t];return s}var Oe={name:"Menu",extends:Nt,inheritAttrs:!1,emits:["show","hide","focus","blur"],data:function(){return{id:this.$attrs.id,overlayVisible:!1,focused:!1,focusedOptionIndex:-1,selectedOptionIndex:-1}},watch:{"$attrs.id":function(n){this.id=n||re()}},target:null,outsideClickListener:null,scrollHandler:null,resizeListener:null,container:null,list:null,mounted:function(){this.id=this.id||re(),this.popup||(this.bindResizeListener(),this.bindOutsideClickListener())},beforeUnmount:function(){this.unbindResizeListener(),this.unbindOutsideClickListener(),this.scrollHandler&&(this.scrollHandler.destroy(),this.scrollHandler=null),this.target=null,this.container&&this.autoZIndex&&U.clear(this.container),this.container=null},methods:{itemClick:function(n){var t=n.item;this.disabled(t)||(t.command&&t.command(n),this.overlayVisible&&this.hide(),!this.popup&&this.focusedOptionIndex!==n.id&&(this.focusedOptionIndex=n.id))},itemMouseMove:function(n){this.focused&&(this.focusedOptionIndex=n.id)},onListFocus:function(n){this.focused=!0,!this.popup&&this.changeFocusedOptionIndex(0),this.$emit("focus",n)},onListBlur:function(n){this.focused=!1,this.focusedOptionIndex=-1,this.$emit("blur",n)},onListKeyDown:function(n){switch(n.code){case"ArrowDown":this.onArrowDownKey(n);break;case"ArrowUp":this.onArrowUpKey(n);break;case"Home":this.onHomeKey(n);break;case"End":this.onEndKey(n);break;case"Enter":case"NumpadEnter":this.onEnterKey(n);break;case"Space":this.onSpaceKey(n);break;case"Escape":this.popup&&(_(this.target),this.hide());case"Tab":this.overlayVisible&&this.hide();break}},onArrowDownKey:function(n){var t=this.findNextOptionIndex(this.focusedOptionIndex);this.changeFocusedOptionIndex(t),n.preventDefault()},onArrowUpKey:function(n){if(n.altKey&&this.popup)_(this.target),this.hide(),n.preventDefault();else{var t=this.findPrevOptionIndex(this.focusedOptionIndex);this.changeFocusedOptionIndex(t),n.preventDefault()}},onHomeKey:function(n){this.changeFocusedOptionIndex(0),n.preventDefault()},onEndKey:function(n){this.changeFocusedOptionIndex(j(this.container,'li[data-pc-section="item"][data-p-disabled="false"]').length-1),n.preventDefault()},onEnterKey:function(n){var t=ie(this.list,'li[id="'.concat("".concat(this.focusedOptionIndex),'"]')),s=t&&ie(t,'a[data-pc-section="itemlink"]');this.popup&&_(this.target),s?s.click():t&&t.click(),n.preventDefault()},onSpaceKey:function(n){this.onEnterKey(n)},findNextOptionIndex:function(n){var t=j(this.container,'li[data-pc-section="item"][data-p-disabled="false"]'),s=me(t).findIndex(function(i){return i.id===n});return s>-1?s+1:0},findPrevOptionIndex:function(n){var t=j(this.container,'li[data-pc-section="item"][data-p-disabled="false"]'),s=me(t).findIndex(function(i){return i.id===n});return s>-1?s-1:0},changeFocusedOptionIndex:function(n){var t=j(this.container,'li[data-pc-section="item"][data-p-disabled="false"]'),s=n>=t.length?t.length-1:n<0?0:n;s>-1&&(this.focusedOptionIndex=t[s].getAttribute("id"))},toggle:function(n){this.overlayVisible?this.hide():this.show(n)},show:function(n){this.overlayVisible=!0,this.target=n.currentTarget},hide:function(){this.overlayVisible=!1,this.target=null},onEnter:function(n){Ne(n,{position:"absolute",top:"0",left:"0"}),this.alignOverlay(),this.bindOutsideClickListener(),this.bindResizeListener(),this.bindScrollListener(),this.autoZIndex&&U.set("menu",n,this.baseZIndex+this.$primevue.config.zIndex.menu),this.popup&&_(this.list),this.$emit("show")},onLeave:function(){this.unbindOutsideClickListener(),this.unbindResizeListener(),this.unbindScrollListener(),this.$emit("hide")},onAfterLeave:function(n){this.autoZIndex&&U.clear(n)},alignOverlay:function(){Me(this.container,this.target);var n=F(this.target);n>F(this.container)&&(this.container.style.minWidth=F(this.target)+"px")},bindOutsideClickListener:function(){var n=this;this.outsideClickListener||(this.outsideClickListener=function(t){var s=n.container&&!n.container.contains(t.target),i=!(n.target&&(n.target===t.target||n.target.contains(t.target)));n.overlayVisible&&s&&i?n.hide():!n.popup&&s&&i&&(n.focusedOptionIndex=-1)},document.addEventListener("click",this.outsideClickListener))},unbindOutsideClickListener:function(){this.outsideClickListener&&(document.removeEventListener("click",this.outsideClickListener),this.outsideClickListener=null)},bindScrollListener:function(){var n=this;this.scrollHandler||(this.scrollHandler=new He(this.target,function(){n.overlayVisible&&n.hide()})),this.scrollHandler.bindScrollListener()},unbindScrollListener:function(){this.scrollHandler&&this.scrollHandler.unbindScrollListener()},bindResizeListener:function(){var n=this;this.resizeListener||(this.resizeListener=function(){n.overlayVisible&&!Be()&&n.hide()},window.addEventListener("resize",this.resizeListener))},unbindResizeListener:function(){this.resizeListener&&(window.removeEventListener("resize",this.resizeListener),this.resizeListener=null)},visible:function(n){return typeof n.visible=="function"?n.visible():n.visible!==!1},disabled:function(n){return typeof n.disabled=="function"?n.disabled():n.disabled},label:function(n){return typeof n.label=="function"?n.label():n.label},onOverlayClick:function(n){Ke.emit("overlay-click",{originalEvent:n,target:this.target})},containerRef:function(n){this.container=n},listRef:function(n){this.list=n}},computed:{focusedOptionId:function(){return this.focusedOptionIndex!==-1?this.focusedOptionIndex:null}},components:{PVMenuitem:xe,Portal:ye}},Wt=["id"],Gt=["id","tabindex","aria-activedescendant","aria-label","aria-labelledby"],$t=["id"];function Ft(e,n,t,s,i,o){var r=N("PVMenuitem"),d=N("Portal");return c(),y(d,{appendTo:e.appendTo,disabled:!e.popup},{default:w(function(){return[I(Ee,b({name:"p-connected-overlay",onEnter:o.onEnter,onLeave:o.onLeave,onAfterLeave:o.onAfterLeave},e.ptm("transition")),{default:w(function(){return[!e.popup||i.overlayVisible?(c(),p("div",b({key:0,ref:o.containerRef,id:i.id,class:e.cx("root"),onClick:n[3]||(n[3]=function(){return o.onOverlayClick&&o.onOverlayClick.apply(o,arguments)})},e.ptmi("root")),[e.$slots.start?(c(),p("div",b({key:0,class:e.cx("start")},e.ptm("start")),[W(e.$slots,"start")],16)):v("",!0),f("ul",b({ref:o.listRef,id:i.id+"_list",class:e.cx("list"),role:"menu",tabindex:e.tabindex,"aria-activedescendant":i.focused?o.focusedOptionId:void 0,"aria-label":e.ariaLabel,"aria-labelledby":e.ariaLabelledby,onFocus:n[0]||(n[0]=function(){return o.onListFocus&&o.onListFocus.apply(o,arguments)}),onBlur:n[1]||(n[1]=function(){return o.onListBlur&&o.onListBlur.apply(o,arguments)}),onKeydown:n[2]||(n[2]=function(){return o.onListKeyDown&&o.onListKeyDown.apply(o,arguments)})},e.ptm("list")),[(c(!0),p(C,null,B(e.model,function(u,m){return c(),p(C,{key:o.label(u)+m.toString()},[u.items&&o.visible(u)&&!u.separator?(c(),p(C,{key:0},[u.items?(c(),p("li",b({key:0,id:i.id+"_"+m,class:[e.cx("submenuLabel"),u.class],role:"none",ref_for:!0},e.ptm("submenuLabel")),[W(e.$slots,e.$slots.submenulabel?"submenulabel":"submenuheader",{item:u},function(){return[he(k(o.label(u)),1)]})],16,$t)):v("",!0),(c(!0),p(C,null,B(u.items,function(l,g){return c(),p(C,{key:l.label+m+"_"+g},[o.visible(l)&&!l.separator?(c(),y(r,{key:0,id:i.id+"_"+m+"_"+g,item:l,templates:e.$slots,focusedOptionId:o.focusedOptionId,unstyled:e.unstyled,onItemClick:o.itemClick,onItemMousemove:o.itemMouseMove,pt:e.pt},null,8,["id","item","templates","focusedOptionId","unstyled","onItemClick","onItemMousemove","pt"])):o.visible(l)&&l.separator?(c(),p("li",b({key:"separator"+m+g,class:[e.cx("separator"),u.class],style:l.style,role:"separator",ref_for:!0},e.ptm("separator")),null,16)):v("",!0)],64)}),128))],64)):o.visible(u)&&u.separator?(c(),p("li",b({key:"separator"+m.toString(),class:[e.cx("separator"),u.class],style:u.style,role:"separator",ref_for:!0},e.ptm("separator")),null,16)):(c(),y(r,{key:o.label(u)+m.toString(),id:i.id+"_"+m,item:u,index:m,templates:e.$slots,focusedOptionId:o.focusedOptionId,unstyled:e.unstyled,onItemClick:o.itemClick,onItemMousemove:o.itemMouseMove,pt:e.pt},null,8,["id","item","index","templates","focusedOptionId","unstyled","onItemClick","onItemMousemove","pt"]))],64)}),128))],16,Gt),e.$slots.end?(c(),p("div",b({key:1,class:e.cx("end")},e.ptm("end")),[W(e.$slots,"end")],16)):v("",!0)],16,Wt)):v("",!0)]}),_:3},16,["onEnter","onLeave","onAfterLeave"])]}),_:3},8,["appendTo","disabled"])}Oe.render=Ft;const Zt={class:"flex items-center gap-2"},Yt=["value"],Jt=["value"],Qt={__name:"BranchSwitcher",setup(e){const n=ne(),t=T(()=>n.props.branches||[]),s=T(()=>n.props.auth.user.branch_id),i=o=>{M.post(route("branch.switch"),{branch_id:o.target.value},{preserveScroll:!0,preserveState:!0})};return(o,r)=>(c(),p("div",Zt,[r[1]||(r[1]=f("label",{class:"text-sm text-slate-600 font-medium hidden md:block"}," Branch ",-1)),f("select",{class:"px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 hover:border-indigo-400 transition",value:s.value,onChange:i},[r[0]||(r[0]=f("option",{disabled:"",value:""},"Select Branch",-1)),(c(!0),p(C,null,B(t.value,d=>(c(),p("option",{key:d.id,value:d.id},k(d.name),9,Jt))),128))],40,Yt)]))}},qt={class:"layout-topbar flex items-center justify-between px-4 bg-white border-b shadow-sm"},en={class:"flex items-center gap-3"},tn={class:"flex items-center gap-4"},nn={__name:"AppTopbar",setup(e){const{toggleMenu:n}=oe(),{user:t}=De(),s=T(()=>route().current()),i=o=>s.value.startsWith(o);return(o,r)=>{const d=Oe,u=ze;return c(),p("div",qt,[f("div",en,[f("button",{class:"layout-menu-button layout-topbar-action",onClick:r[0]||(r[0]=(...m)=>D(n)&&D(n)(...m))},r[4]||(r[4]=[f("i",{class:"pi pi-bars text-xl"},null,-1)])),I(D(ge),{href:"/",class:"layout-topbar-logo flex items-center gap-2"},{default:w(()=>[I(Ve,{class:"h-8 w-auto"})]),_:1})]),f("div",tn,[I(Qt),f("button",{onClick:r[1]||(r[1]=m=>D(M).visit(o.route("pos.index"))),class:x(["flex items-center gap-2 px-3 py-2 rounded-lg transition",i("pos.index")?"bg-indigo-600 text-white shadow-sm":"hover:bg-indigo-50 text-slate-700"])},r[5]||(r[5]=[f("i",{class:"pi pi-desktop text-lg"},null,-1),f("span",{class:"text-sm font-medium"},"POS",-1)]),2),f("button",{onClick:r[2]||(r[2]=m=>D(M).visit(o.route("pos.orders.index"))),class:x(["flex items-center gap-2 px-3 py-2 rounded-lg transition",i("pos.orders.index")?"bg-emerald-600 text-white shadow-sm":"hover:bg-emerald-50 text-slate-700"])},r[6]||(r[6]=[f("i",{class:"pi pi-receipt text-lg"},null,-1),f("span",{class:"text-sm font-medium"},"Orders",-1)]),2),I(d,{ref:"menu",popup:!0,id:"top_profile_menu",model:[{label:"Settings",icon:"pi pi-cog",command:()=>D(M).visit(o.route("profile.edit"))},{label:"Logout",icon:"pi pi-sign-out",command:()=>D(M).post(o.route("logout"))}]},null,8,["model"]),I(u,{type:"button",label:D(t).name,icon:"pi pi-user",class:"layout-topbar-action1",variant:"outlined",onClick:r[3]||(r[3]=m=>o.$refs.menu.toggle(m))},null,8,["label"])])])}}},on={class:"layout-main-container"},sn={class:"layout-main"},gn={__name:"AuthenticatedLayout",setup(e){const{layoutConfig:n,layoutState:t,isSidebarActive:s}=oe(),i=G(null);Z(s,m=>{m?r():d()});const o=T(()=>({"layout-overlay":n.menuMode==="overlay","layout-static":n.menuMode==="static","layout-static-inactive":t.staticMenuDesktopInactive&&n.menuMode==="static","layout-overlay-active":t.overlayMenuActive,"layout-mobile-active":t.staticMenuMobileActive}));function r(){i.value||(i.value=m=>{u(m)&&(t.overlayMenuActive=!1,t.staticMenuMobileActive=!1,t.menuHoverActive=!1)},document.addEventListener("click",i.value))}function d(){i.value&&(document.removeEventListener("click",i),i.value=null)}function u(m){const l=document.querySelector(".layout-sidebar"),g=document.querySelector(".layout-menu-button");return!(l.isSameNode(m.target)||l.contains(m.target)||g.isSameNode(m.target)||g.contains(m.target))}return(m,l)=>{const g=Ce;return c(),p(C,null,[I(We),f("div",{class:x(["layout-wrapper bg-blue-50",o.value])},[I(nn),I(Pt),f("div",on,[f("div",sn,[W(m.$slots,"default")]),I(It)]),l[0]||(l[0]=f("div",{class:"layout-mask animate-fadein"},null,-1))],2),I(g)],64)}}};export{gn as _};
