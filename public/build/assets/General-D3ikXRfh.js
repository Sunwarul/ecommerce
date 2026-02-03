import{_ as j}from"./AuthenticatedLayout-DT9N2QIJ.js";import{B as E,m as v,l as z,D as x,c as m,o as c,G as y,d as w,K as A,a4 as N,g as P,w as V,I,F as G,a6 as k,a7 as K,j as M,h as U,V as C,A as _,b as r,H,t as R,C as W,a as p,u as l,a3 as Y}from"./app-D6VZwh9m.js";import{a as q}from"./index-B6Pde6JK.js";import{s as T}from"./index-CeJHjz4Z.js";import{s as f}from"./index-epzkeV3O.js";import{s as O}from"./index-dySuYGkv.js";import{U as $}from"./index-n8HDM0GN.js";import{s as J}from"./index-BTtZo0JK.js";import{s as Q}from"./index-Cztb6yLy.js";import{R as X}from"./index-Bullbt4E.js";import{s as Z}from"./index-fKkumUib.js";import"./index-BkgCVwsV.js";import"./index-DCyjV8R-.js";import"./index-DHpg9Iw7.js";import"./index-Bm_3Bs0b.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./FavIcons-BO6-abF3.js";import"./index-BpCuBuyp.js";import"./index-nBDTfL0O.js";var ee={root:function(e){var t=e.instance;return["p-tabpanel",{"p-tabpanel-active":t.active}]}},te=E.extend({name:"tabpanel",classes:ee}),ne={name:"BaseTabPanel",extends:O,props:{value:{type:[String,Number],default:void 0},as:{type:[String,Object],default:"DIV"},asChild:{type:Boolean,default:!1},header:null,headerStyle:null,headerClass:null,headerProps:null,headerActionProps:null,contentStyle:null,contentClass:null,contentProps:null,disabled:Boolean},style:te,provide:function(){return{$pcTabPanel:this,$parentInstance:this}}},S={name:"TabPanel",extends:ne,inheritAttrs:!1,inject:["$pcTabs"],computed:{active:function(){var e;return z((e=this.$pcTabs)===null||e===void 0?void 0:e.d_value,this.value)},id:function(){var e;return"".concat((e=this.$pcTabs)===null||e===void 0?void 0:e.id,"_tabpanel_").concat(this.value)},ariaLabelledby:function(){var e;return"".concat((e=this.$pcTabs)===null||e===void 0?void 0:e.id,"_tab_").concat(this.value)},attrs:function(){return v(this.a11yAttrs,this.ptmi("root",this.ptParams))},a11yAttrs:function(){var e;return{id:this.id,tabindex:(e=this.$pcTabs)===null||e===void 0?void 0:e.tabindex,role:"tabpanel","aria-labelledby":this.ariaLabelledby,"data-pc-name":"tabpanel","data-p-active":this.active}},ptParams:function(){return{context:{active:this.active}}}}};function ae(n,e,t,i,d,o){var h,u;return o.$pcTabs?(c(),m(y,{key:1},[n.asChild?x(n.$slots,"default",{key:1,class:G(n.cx("root")),active:o.active,a11yAttrs:o.a11yAttrs}):(c(),m(y,{key:0},[!((h=o.$pcTabs)!==null&&h!==void 0&&h.lazy)||o.active?A((c(),P(I(n.as),v({key:0,class:n.cx("root")},o.attrs),{default:V(function(){return[x(n.$slots,"default")]}),_:3},16,["class"])),[[N,(u=o.$pcTabs)!==null&&u!==void 0&&u.lazy?!0:o.active]]):w("",!0)],64))],64)):x(n.$slots,"default",{key:0})}S.render=ae;var le=function(e){var t=e.dt;return`
.p-tabview-tablist-container {
    position: relative;
}

.p-tabview-scrollable > .p-tabview-tablist-container {
    overflow: hidden;
}

.p-tabview-tablist-scroll-container {
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    scrollbar-width: none;
    overscroll-behavior: contain auto;
}

.p-tabview-tablist-scroll-container::-webkit-scrollbar {
    display: none;
}

.p-tabview-tablist {
    display: flex;
    margin: 0;
    padding: 0;
    list-style-type: none;
    flex: 1 1 auto;
    background: `.concat(t("tabview.tab.list.background"),`;
    border: 1px solid `).concat(t("tabview.tab.list.border.color"),`;
    border-width: 0 0 1px 0;
    position: relative;
}

.p-tabview-tab-header {
    cursor: pointer;
    user-select: none;
    display: flex;
    align-items: center;
    text-decoration: none;
    position: relative;
    overflow: hidden;
    border-style: solid;
    border-width: 0 0 1px 0;
    border-color: transparent transparent `).concat(t("tabview.tab.border.color"),` transparent;
    color: `).concat(t("tabview.tab.color"),`;
    padding: 1rem 1.125rem;
    font-weight: 600;
    border-top-right-radius: `).concat(t("border.radius.md"),`;
    border-top-left-radius: `).concat(t("border.radius.md"),`;
    transition: color `).concat(t("tabview.transition.duration"),", outline-color ").concat(t("tabview.transition.duration"),`;
    margin: 0 0 -1px 0;
    outline-color: transparent;
}

.p-tabview-tablist-item:not(.p-disabled) .p-tabview-tab-header:focus-visible {
    outline: `).concat(t("focus.ring.width")," ").concat(t("focus.ring.style")," ").concat(t("focus.ring.color"),`;
    outline-offset: -1px;
}

.p-tabview-tablist-item:not(.p-highlight):not(.p-disabled):hover > .p-tabview-tab-header {
    color: `).concat(t("tabview.tab.hover.color"),`;
}

.p-tabview-tablist-item.p-highlight > .p-tabview-tab-header {
    color: `).concat(t("tabview.tab.active.color"),`;
}

.p-tabview-tab-title {
    line-height: 1;
    white-space: nowrap;
}

.p-tabview-next-button,
.p-tabview-prev-button {
    position: absolute;
    top: 0;
    margin: 0;
    padding: 0;
    z-index: 2;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: `).concat(t("tabview.nav.button.background"),`;
    color: `).concat(t("tabview.nav.button.color"),`;
    width: 2.5rem;
    border-radius: 0;
    outline-color: transparent;
    transition: color `).concat(t("tabview.transition.duration"),", outline-color ").concat(t("tabview.transition.duration"),`;
    box-shadow: `).concat(t("tabview.nav.button.shadow"),`;
    border: none;
    cursor: pointer;
    user-select: none;
}

.p-tabview-next-button:focus-visible,
.p-tabview-prev-button:focus-visible {
    outline: `).concat(t("focus.ring.width")," ").concat(t("focus.ring.style")," ").concat(t("focus.ring.color"),`;
    outline-offset: `).concat(t("focus.ring.offset"),`;
}

.p-tabview-next-button:hover,
.p-tabview-prev-button:hover {
    color: `).concat(t("tabview.nav.button.hover.color"),`;
}

.p-tabview-prev-button {
    left: 0;
}

.p-tabview-next-button {
    right: 0;
}

.p-tabview-panels {
    background: `).concat(t("tabview.tab.panel.background"),`;
    color: `).concat(t("tabview.tab.panel.color"),`;
    padding: 0.875rem 1.125rem 1.125rem 1.125rem;
}

.p-tabview-ink-bar {
    z-index: 1;
    display: block;
    position: absolute;
    bottom: -1px;
    height: 1px;
    background: `).concat(t("tabview.tab.active.border.color"),`;
    transition: 250ms cubic-bezier(0.35, 0, 0.25, 1);
}
`)},ie={root:function(e){var t=e.props;return["p-tabview p-component",{"p-tabview-scrollable":t.scrollable}]},navContainer:"p-tabview-tablist-container",prevButton:"p-tabview-prev-button",navContent:"p-tabview-tablist-scroll-container",nav:"p-tabview-tablist",tab:{header:function(e){var t=e.instance,i=e.tab,d=e.index;return["p-tabview-tablist-item",t.getTabProp(i,"headerClass"),{"p-tabview-tablist-item-active":t.d_activeIndex===d,"p-disabled":t.getTabProp(i,"disabled")}]},headerAction:"p-tabview-tab-header",headerTitle:"p-tabview-tab-title",content:function(e){var t=e.instance,i=e.tab;return["p-tabview-panel",t.getTabProp(i,"contentClass")]}},inkbar:"p-tabview-ink-bar",nextButton:"p-tabview-next-button",panelContainer:"p-tabview-panels"},oe=E.extend({name:"tabview",theme:le,classes:ie}),re={name:"BaseTabView",extends:O,props:{activeIndex:{type:Number,default:0},lazy:{type:Boolean,default:!1},scrollable:{type:Boolean,default:!1},tabindex:{type:Number,default:0},selectOnFocus:{type:Boolean,default:!1},prevButtonProps:{type:null,default:null},nextButtonProps:{type:null,default:null},prevIcon:{type:String,default:void 0},nextIcon:{type:String,default:void 0}},style:oe,provide:function(){return{$pcTabs:void 0,$pcTabView:this,$parentInstance:this}}},F={name:"TabView",extends:re,inheritAttrs:!1,emits:["update:activeIndex","tab-change","tab-click"],data:function(){return{id:this.$attrs.id,d_activeIndex:this.activeIndex,isPrevButtonDisabled:!0,isNextButtonDisabled:!1}},watch:{"$attrs.id":function(e){this.id=e||$()},activeIndex:function(e){this.d_activeIndex=e,this.scrollInView({index:e})}},mounted:function(){console.warn("Deprecated since v4. Use Tabs component instead."),this.id=this.id||$(),this.updateInkBar(),this.scrollable&&this.updateButtonState()},updated:function(){this.updateInkBar(),this.scrollable&&this.updateButtonState()},methods:{isTabPanel:function(e){return e.type.name==="TabPanel"},isTabActive:function(e){return this.d_activeIndex===e},getTabProp:function(e,t){return e.props?e.props[t]:void 0},getKey:function(e,t){return this.getTabProp(e,"header")||t},getTabHeaderActionId:function(e){return"".concat(this.id,"_").concat(e,"_header_action")},getTabContentId:function(e){return"".concat(this.id,"_").concat(e,"_content")},getTabPT:function(e,t,i){var d=this.tabs.length,o={props:e.props,parent:{instance:this,props:this.$props,state:this.$data},context:{index:i,count:d,first:i===0,last:i===d-1,active:this.isTabActive(i)}};return v(this.ptm("tabpanel.".concat(t),{tabpanel:o}),this.ptm("tabpanel.".concat(t),o),this.ptmo(this.getTabProp(e,"pt"),t,o))},onScroll:function(e){this.scrollable&&this.updateButtonState(),e.preventDefault()},onPrevButtonClick:function(){var e=this.$refs.content,t=k(e),i=e.scrollLeft-t;e.scrollLeft=i<=0?0:i},onNextButtonClick:function(){var e=this.$refs.content,t=k(e)-this.getVisibleButtonWidths(),i=e.scrollLeft+t,d=e.scrollWidth-t;e.scrollLeft=i>=d?d:i},onTabClick:function(e,t,i){this.changeActiveIndex(e,t,i),this.$emit("tab-click",{originalEvent:e,index:i})},onTabKeyDown:function(e,t,i){switch(e.code){case"ArrowLeft":this.onTabArrowLeftKey(e);break;case"ArrowRight":this.onTabArrowRightKey(e);break;case"Home":this.onTabHomeKey(e);break;case"End":this.onTabEndKey(e);break;case"PageDown":this.onPageDownKey(e);break;case"PageUp":this.onPageUpKey(e);break;case"Enter":case"NumpadEnter":case"Space":this.onTabEnterKey(e,t,i);break}},onTabArrowRightKey:function(e){var t=this.findNextHeaderAction(e.target.parentElement);t?this.changeFocusedTab(e,t):this.onTabHomeKey(e),e.preventDefault()},onTabArrowLeftKey:function(e){var t=this.findPrevHeaderAction(e.target.parentElement);t?this.changeFocusedTab(e,t):this.onTabEndKey(e),e.preventDefault()},onTabHomeKey:function(e){var t=this.findFirstHeaderAction();this.changeFocusedTab(e,t),e.preventDefault()},onTabEndKey:function(e){var t=this.findLastHeaderAction();this.changeFocusedTab(e,t),e.preventDefault()},onPageDownKey:function(e){this.scrollInView({index:this.$refs.nav.children.length-2}),e.preventDefault()},onPageUpKey:function(e){this.scrollInView({index:0}),e.preventDefault()},onTabEnterKey:function(e,t,i){this.changeActiveIndex(e,t,i),e.preventDefault()},findNextHeaderAction:function(e){var t=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1,i=t?e:e.nextElementSibling;return i?C(i,"data-p-disabled")||C(i,"data-pc-section")==="inkbar"?this.findNextHeaderAction(i):U(i,'[data-pc-section="headeraction"]'):null},findPrevHeaderAction:function(e){var t=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1,i=t?e:e.previousElementSibling;return i?C(i,"data-p-disabled")||C(i,"data-pc-section")==="inkbar"?this.findPrevHeaderAction(i):U(i,'[data-pc-section="headeraction"]'):null},findFirstHeaderAction:function(){return this.findNextHeaderAction(this.$refs.nav.firstElementChild,!0)},findLastHeaderAction:function(){return this.findPrevHeaderAction(this.$refs.nav.lastElementChild,!0)},changeActiveIndex:function(e,t,i){!this.getTabProp(t,"disabled")&&this.d_activeIndex!==i&&(this.d_activeIndex=i,this.$emit("update:activeIndex",i),this.$emit("tab-change",{originalEvent:e,index:i}),this.scrollInView({index:i}))},changeFocusedTab:function(e,t){if(t&&(M(t),this.scrollInView({element:t}),this.selectOnFocus)){var i=parseInt(t.parentElement.dataset.pcIndex,10),d=this.tabs[i];this.changeActiveIndex(e,d,i)}},scrollInView:function(e){var t=e.element,i=e.index,d=i===void 0?-1:i,o=t||this.$refs.nav.children[d];o&&o.scrollIntoView&&o.scrollIntoView({block:"nearest"})},updateInkBar:function(){var e=this.$refs.nav.children[this.d_activeIndex];this.$refs.inkbar.style.width=k(e)+"px",this.$refs.inkbar.style.left=K(e).left-K(this.$refs.nav).left+"px"},updateButtonState:function(){var e=this.$refs.content,t=e.scrollLeft,i=e.scrollWidth,d=k(e);this.isPrevButtonDisabled=t===0,this.isNextButtonDisabled=parseInt(t)===i-d},getVisibleButtonWidths:function(){var e=this.$refs,t=e.prevBtn,i=e.nextBtn;return[t,i].reduce(function(d,o){return o?d+k(o):d},0)}},computed:{tabs:function(){var e=this;return this.$slots.default().reduce(function(t,i){return e.isTabPanel(i)?t.push(i):i.children&&i.children instanceof Array&&i.children.forEach(function(d){e.isTabPanel(d)&&t.push(d)}),t},[])},prevButtonAriaLabel:function(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.previous:void 0},nextButtonAriaLabel:function(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.next:void 0}},directives:{ripple:X},components:{ChevronLeftIcon:J,ChevronRightIcon:Q}};function B(n){"@babel/helpers - typeof";return B=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(e){return typeof e}:function(e){return e&&typeof Symbol=="function"&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},B(n)}function L(n,e){var t=Object.keys(n);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(n);e&&(i=i.filter(function(d){return Object.getOwnPropertyDescriptor(n,d).enumerable})),t.push.apply(t,i)}return t}function g(n){for(var e=1;e<arguments.length;e++){var t=arguments[e]!=null?arguments[e]:{};e%2?L(Object(t),!0).forEach(function(i){se(n,i,t[i])}):Object.getOwnPropertyDescriptors?Object.defineProperties(n,Object.getOwnPropertyDescriptors(t)):L(Object(t)).forEach(function(i){Object.defineProperty(n,i,Object.getOwnPropertyDescriptor(t,i))})}return n}function se(n,e,t){return(e=ue(e))in n?Object.defineProperty(n,e,{value:t,enumerable:!0,configurable:!0,writable:!0}):n[e]=t,n}function ue(n){var e=de(n,"string");return B(e)=="symbol"?e:e+""}function de(n,e){if(B(n)!="object"||!n)return n;var t=n[Symbol.toPrimitive];if(t!==void 0){var i=t.call(n,e);if(B(i)!="object")return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return(e==="string"?String:Number)(n)}var pe=["tabindex","aria-label"],be=["data-p-active","data-p-disabled","data-pc-index"],ce=["id","tabindex","aria-disabled","aria-selected","aria-controls","onClick","onKeydown"],ve=["tabindex","aria-label"],fe=["id","aria-labelledby","data-pc-index","data-p-active"];function me(n,e,t,i,d,o){var h=_("ripple");return c(),m("div",v({class:n.cx("root"),role:"tablist"},n.ptmi("root")),[r("div",v({class:n.cx("navContainer")},n.ptm("navContainer")),[n.scrollable&&!d.isPrevButtonDisabled?A((c(),m("button",v({key:0,ref:"prevBtn",type:"button",class:n.cx("prevButton"),tabindex:n.tabindex,"aria-label":o.prevButtonAriaLabel,onClick:e[0]||(e[0]=function(){return o.onPrevButtonClick&&o.onPrevButtonClick.apply(o,arguments)})},g(g({},n.prevButtonProps),n.ptm("prevButton")),{"data-pc-group-section":"navbutton"}),[x(n.$slots,"previcon",{},function(){return[(c(),P(I(n.prevIcon?"span":"ChevronLeftIcon"),v({"aria-hidden":"true",class:n.prevIcon},n.ptm("prevIcon")),null,16,["class"]))]})],16,pe)),[[h]]):w("",!0),r("div",v({ref:"content",class:n.cx("navContent"),onScroll:e[1]||(e[1]=function(){return o.onScroll&&o.onScroll.apply(o,arguments)})},n.ptm("navContent")),[r("ul",v({ref:"nav",class:n.cx("nav")},n.ptm("nav")),[(c(!0),m(y,null,H(o.tabs,function(u,b){return c(),m("li",v({key:o.getKey(u,b),style:o.getTabProp(u,"headerStyle"),class:n.cx("tab.header",{tab:u,index:b}),role:"presentation",ref_for:!0},g(g(g({},o.getTabProp(u,"headerProps")),o.getTabPT(u,"root",b)),o.getTabPT(u,"header",b)),{"data-pc-name":"tabpanel","data-p-active":d.d_activeIndex===b,"data-p-disabled":o.getTabProp(u,"disabled"),"data-pc-index":b}),[A((c(),m("a",v({id:o.getTabHeaderActionId(b),class:n.cx("tab.headerAction"),tabindex:o.getTabProp(u,"disabled")||!o.isTabActive(b)?-1:n.tabindex,role:"tab","aria-disabled":o.getTabProp(u,"disabled"),"aria-selected":o.isTabActive(b),"aria-controls":o.getTabContentId(b),onClick:function(a){return o.onTabClick(a,u,b)},onKeydown:function(a){return o.onTabKeyDown(a,u,b)},ref_for:!0},g(g({},o.getTabProp(u,"headerActionProps")),o.getTabPT(u,"headerAction",b))),[u.props&&u.props.header?(c(),m("span",v({key:0,class:n.cx("tab.headerTitle"),ref_for:!0},o.getTabPT(u,"headerTitle",b)),R(u.props.header),17)):w("",!0),u.children&&u.children.header?(c(),P(I(u.children.header),{key:1})):w("",!0)],16,ce)),[[h]])],16,be)}),128)),r("li",v({ref:"inkbar",class:n.cx("inkbar"),role:"presentation","aria-hidden":"true"},n.ptm("inkbar")),null,16)],16)],16),n.scrollable&&!d.isNextButtonDisabled?A((c(),m("button",v({key:1,ref:"nextBtn",type:"button",class:n.cx("nextButton"),tabindex:n.tabindex,"aria-label":o.nextButtonAriaLabel,onClick:e[2]||(e[2]=function(){return o.onNextButtonClick&&o.onNextButtonClick.apply(o,arguments)})},g(g({},n.nextButtonProps),n.ptm("nextButton")),{"data-pc-group-section":"navbutton"}),[x(n.$slots,"nexticon",{},function(){return[(c(),P(I(n.nextIcon?"span":"ChevronRightIcon"),v({"aria-hidden":"true",class:n.nextIcon},n.ptm("nextIcon")),null,16,["class"]))]})],16,ve)),[[h]]):w("",!0)],16),r("div",v({class:n.cx("panelContainer")},n.ptm("panelContainer")),[(c(!0),m(y,null,H(o.tabs,function(u,b){return c(),m(y,{key:o.getKey(u,b)},[!n.lazy||o.isTabActive(b)?A((c(),m("div",v({key:0,id:o.getTabContentId(b),style:o.getTabProp(u,"contentStyle"),class:n.cx("tab.content",{tab:u}),role:"tabpanel","aria-labelledby":o.getTabHeaderActionId(b),ref_for:!0},g(g(g({},o.getTabProp(u,"contentProps")),o.getTabPT(u,"root",b)),o.getTabPT(u,"content",b)),{"data-pc-name":"tabpanel","data-pc-index":b,"data-p-active":d.d_activeIndex===b}),[(c(),P(I(u)))],16,fe)),[[N,n.lazy?!0:o.isTabActive(b)]]):w("",!0)],64)}),128))],16)],16)}F.render=me;const ge={class:"p-6 max-w-4xl mx-auto"},he={class:"grid gap-4"},we={class:"grid gap-4"},ye={class:"grid gap-4"},Te={class:"mt-6"},je={__name:"General",props:{settings:Object},setup(n){const t=W({settings:{...n.settings}}),i=[{label:"English",value:"English"}],d=[{label:"(GMT/UTC 05:30)Dhaka",value:"(GMT/UTC 05:30)Dhaka"},{label:"(GMT/UTC 05:30)Kolkata",value:"(GMT/UTC 05:30)Kolkata"},{label:"(GMT/UTC 05:30)New Delhi",value:"(GMT/UTC 05:30)New Delhi"},{label:"(GMT/UTC 05:30)Tashkent",value:"(GMT/UTC 05:30)Tashkent"}],o=[{label:"d-m-Y",value:"d-m-Y"},{label:"m/d/Y",value:"m/d/Y"},{label:"Y-m-d",value:"Y-m-d"}],h=[{label:"24 Hours",value:"24 Hours"},{label:"12 Hours",value:"12 Hours"}],u=[{label:"Twilio",value:"twilio"},{label:"Vonage",value:"vonage"},{label:"Disabled",value:"null"}];function b(){t.put(route("settings.update"))}return(D,a)=>(c(),P(j,null,{default:V(()=>[p(l(Y),{title:"Settings"}),r("div",ge,[p(l(F),null,{default:V(()=>[p(l(S),{header:"General"},{default:V(()=>[r("div",he,[r("div",null,[a[22]||(a[22]=r("label",null,"Application Name",-1)),p(l(f),{modelValue:l(t).settings.general_application_name,"onUpdate:modelValue":a[0]||(a[0]=s=>l(t).settings.general_application_name=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[23]||(a[23]=r("label",null,"Address",-1)),p(l(f),{modelValue:l(t).settings.general_address,"onUpdate:modelValue":a[1]||(a[1]=s=>l(t).settings.general_address=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[24]||(a[24]=r("label",null,"Phone",-1)),p(l(f),{modelValue:l(t).settings.general_phone,"onUpdate:modelValue":a[2]||(a[2]=s=>l(t).settings.general_phone=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[25]||(a[25]=r("label",null,"Footer Text",-1)),p(l(Z),{rows:"2",modelValue:l(t).settings.general_footer_text,"onUpdate:modelValue":a[3]||(a[3]=s=>l(t).settings.general_footer_text=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[26]||(a[26]=r("label",null,"Language",-1)),p(l(T),{class:"w-full",options:i,optionLabel:"label",optionValue:"value",modelValue:l(t).settings.general_language,"onUpdate:modelValue":a[4]||(a[4]=s=>l(t).settings.general_language=s)},null,8,["modelValue"])]),r("div",null,[a[27]||(a[27]=r("label",null,"Timezone",-1)),p(l(T),{class:"w-full",filter:"",options:d,optionLabel:"label",optionValue:"value",modelValue:l(t).settings.general_timezone,"onUpdate:modelValue":a[5]||(a[5]=s=>l(t).settings.general_timezone=s)},null,8,["modelValue"])]),r("div",null,[a[28]||(a[28]=r("label",null,"Date Format",-1)),p(l(T),{class:"w-full",options:o,optionLabel:"label",optionValue:"value",modelValue:l(t).settings.general_date_format,"onUpdate:modelValue":a[6]||(a[6]=s=>l(t).settings.general_date_format=s)},null,8,["modelValue"])]),r("div",null,[a[29]||(a[29]=r("label",null,"Time Format",-1)),p(l(T),{class:"w-full",options:h,optionLabel:"label",optionValue:"value",modelValue:l(t).settings.general_time_format,"onUpdate:modelValue":a[7]||(a[7]=s=>l(t).settings.general_time_format=s)},null,8,["modelValue"])])])]),_:1}),p(l(S),{header:"Email (SMTP)"},{default:V(()=>[r("div",we,[r("div",null,[a[30]||(a[30]=r("label",null,"Host",-1)),p(l(f),{modelValue:l(t).settings.mail_host,"onUpdate:modelValue":a[8]||(a[8]=s=>l(t).settings.mail_host=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[31]||(a[31]=r("label",null,"Port",-1)),p(l(f),{modelValue:l(t).settings.mail_port,"onUpdate:modelValue":a[9]||(a[9]=s=>l(t).settings.mail_port=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[32]||(a[32]=r("label",null,"Username",-1)),p(l(f),{modelValue:l(t).settings.mail_username,"onUpdate:modelValue":a[10]||(a[10]=s=>l(t).settings.mail_username=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[33]||(a[33]=r("label",null,"Password",-1)),p(l(f),{type:"password",modelValue:l(t).settings.mail_password,"onUpdate:modelValue":a[11]||(a[11]=s=>l(t).settings.mail_password=s),placeholder:"******",class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[34]||(a[34]=r("label",null,"Encryption",-1)),p(l(T),{class:"w-full",options:[{label:"TLS",value:"tls"},{label:"SSL",value:"ssl"},{label:"None",value:"null"}],optionLabel:"label",optionValue:"value",modelValue:l(t).settings.mail_encryption,"onUpdate:modelValue":a[12]||(a[12]=s=>l(t).settings.mail_encryption=s)},null,8,["modelValue"])]),r("div",null,[a[35]||(a[35]=r("label",null,"From Address",-1)),p(l(f),{modelValue:l(t).settings.mail_from_address,"onUpdate:modelValue":a[13]||(a[13]=s=>l(t).settings.mail_from_address=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[36]||(a[36]=r("label",null,"From Name",-1)),p(l(f),{modelValue:l(t).settings.mail_from_name,"onUpdate:modelValue":a[14]||(a[14]=s=>l(t).settings.mail_from_name=s),class:"w-full"},null,8,["modelValue"])])])]),_:1}),p(l(S),{header:"SMS"},{default:V(()=>[r("div",ye,[r("div",null,[a[37]||(a[37]=r("label",null,"Driver",-1)),p(l(T),{class:"w-full",options:u,optionLabel:"label",optionValue:"value",modelValue:l(t).settings.sms_driver,"onUpdate:modelValue":a[15]||(a[15]=s=>l(t).settings.sms_driver=s)},null,8,["modelValue"])]),l(t).settings.sms_driver==="twilio"?(c(),m(y,{key:0},[r("div",null,[a[38]||(a[38]=r("label",null,"Twilio SID",-1)),p(l(f),{modelValue:l(t).settings.sms_twilio_sid,"onUpdate:modelValue":a[16]||(a[16]=s=>l(t).settings.sms_twilio_sid=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[39]||(a[39]=r("label",null,"Twilio Token",-1)),p(l(f),{type:"password",modelValue:l(t).settings.sms_twilio_token,"onUpdate:modelValue":a[17]||(a[17]=s=>l(t).settings.sms_twilio_token=s),placeholder:"******",class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[40]||(a[40]=r("label",null,"From",-1)),p(l(f),{modelValue:l(t).settings.sms_twilio_from,"onUpdate:modelValue":a[18]||(a[18]=s=>l(t).settings.sms_twilio_from=s),class:"w-full"},null,8,["modelValue"])])],64)):l(t).settings.sms_driver==="vonage"?(c(),m(y,{key:1},[r("div",null,[a[41]||(a[41]=r("label",null,"API Key",-1)),p(l(f),{modelValue:l(t).settings.sms_vonage_api_key,"onUpdate:modelValue":a[19]||(a[19]=s=>l(t).settings.sms_vonage_api_key=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[42]||(a[42]=r("label",null,"API Secret",-1)),p(l(f),{type:"password",modelValue:l(t).settings.sms_vonage_api_secret,"onUpdate:modelValue":a[20]||(a[20]=s=>l(t).settings.sms_vonage_api_secret=s),placeholder:"******",class:"w-full"},null,8,["modelValue"])]),r("div",null,[a[43]||(a[43]=r("label",null,"From",-1)),p(l(f),{modelValue:l(t).settings.sms_vonage_from,"onUpdate:modelValue":a[21]||(a[21]=s=>l(t).settings.sms_vonage_from=s),class:"w-full"},null,8,["modelValue"])])],64)):w("",!0)])]),_:1})]),_:1}),r("div",Te,[p(l(q),{loading:l(t).processing,label:"Save All",onClick:b},null,8,["loading"])])])]),_:1}))}};export{je as default};
