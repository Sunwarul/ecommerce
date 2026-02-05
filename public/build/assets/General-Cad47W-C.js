import{_ as z}from"./AuthenticatedLayout-BaSUhMCN.js";import{B as N,m as v,s as _,J as x,c as m,o as b,F as y,d as w,N as A,a4 as O,I as P,w as V,L as I,j as R,a6 as k,a7 as K,p as W,l as $,X as C,H as Y,b as r,i as L,t as M,O as G,C as q,a as p,u as i,h as J}from"./app-C0cgKEVJ.js";import{a as X}from"./index-BrdrWM5Y.js";import{s as T}from"./index-CEUOXfel.js";import{s as f}from"./index-CbEqa0vW.js";import{s as F}from"./index-CZnd7t6v.js";import{U}from"./index-DchAFvzt.js";import{s as Q}from"./index-AHsjsjwe.js";import{s as Z}from"./index-C4az3xXa.js";import{R as ee}from"./index-HU_mUv9Y.js";import{s as te}from"./index-xvCsfxwi.js";import"./index-RF1b40_i.js";import"./index-Bq_PLBQx.js";import"./index-DguU-g0e.js";import"./index-Do0mVlXl.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./FavIcons-xHxhwu_P.js";import"./index-CASaY_cd.js";import"./index-DB_Lcll3.js";var ne={root:function(e){var a=e.instance;return["p-tabpanel",{"p-tabpanel-active":a.active}]}},ae=N.extend({name:"tabpanel",classes:ne}),le={name:"BaseTabPanel",extends:F,props:{value:{type:[String,Number],default:void 0},as:{type:[String,Object],default:"DIV"},asChild:{type:Boolean,default:!1},header:null,headerStyle:null,headerClass:null,headerProps:null,headerActionProps:null,contentStyle:null,contentClass:null,contentProps:null,disabled:Boolean},style:ae,provide:function(){return{$pcTabPanel:this,$parentInstance:this}}},D={name:"TabPanel",extends:le,inheritAttrs:!1,inject:["$pcTabs"],computed:{active:function(){var e;return _((e=this.$pcTabs)===null||e===void 0?void 0:e.d_value,this.value)},id:function(){var e;return"".concat((e=this.$pcTabs)===null||e===void 0?void 0:e.id,"_tabpanel_").concat(this.value)},ariaLabelledby:function(){var e;return"".concat((e=this.$pcTabs)===null||e===void 0?void 0:e.id,"_tab_").concat(this.value)},attrs:function(){return v(this.a11yAttrs,this.ptmi("root",this.ptParams))},a11yAttrs:function(){var e;return{id:this.id,tabindex:(e=this.$pcTabs)===null||e===void 0?void 0:e.tabindex,role:"tabpanel","aria-labelledby":this.ariaLabelledby,"data-pc-name":"tabpanel","data-p-active":this.active}},ptParams:function(){return{context:{active:this.active}}}}};function ie(t,e,a,n,d,o){var h,u;return o.$pcTabs?(b(),m(y,{key:1},[t.asChild?x(t.$slots,"default",{key:1,class:R(t.cx("root")),active:o.active,a11yAttrs:o.a11yAttrs}):(b(),m(y,{key:0},[!((h=o.$pcTabs)!==null&&h!==void 0&&h.lazy)||o.active?A((b(),P(I(t.as),v({key:0,class:t.cx("root")},o.attrs),{default:V(function(){return[x(t.$slots,"default")]}),_:3},16,["class"])),[[O,(u=o.$pcTabs)!==null&&u!==void 0&&u.lazy?!0:o.active]]):w("",!0)],64))],64)):x(t.$slots,"default",{key:0})}D.render=ie;var oe=function(e){var a=e.dt;return`
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
    background: `.concat(a("tabview.tab.list.background"),`;
    border: 1px solid `).concat(a("tabview.tab.list.border.color"),`;
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
    border-color: transparent transparent `).concat(a("tabview.tab.border.color"),` transparent;
    color: `).concat(a("tabview.tab.color"),`;
    padding: 1rem 1.125rem;
    font-weight: 600;
    border-top-right-radius: `).concat(a("border.radius.md"),`;
    border-top-left-radius: `).concat(a("border.radius.md"),`;
    transition: color `).concat(a("tabview.transition.duration"),", outline-color ").concat(a("tabview.transition.duration"),`;
    margin: 0 0 -1px 0;
    outline-color: transparent;
}

.p-tabview-tablist-item:not(.p-disabled) .p-tabview-tab-header:focus-visible {
    outline: `).concat(a("focus.ring.width")," ").concat(a("focus.ring.style")," ").concat(a("focus.ring.color"),`;
    outline-offset: -1px;
}

.p-tabview-tablist-item:not(.p-highlight):not(.p-disabled):hover > .p-tabview-tab-header {
    color: `).concat(a("tabview.tab.hover.color"),`;
}

.p-tabview-tablist-item.p-highlight > .p-tabview-tab-header {
    color: `).concat(a("tabview.tab.active.color"),`;
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
    background: `).concat(a("tabview.nav.button.background"),`;
    color: `).concat(a("tabview.nav.button.color"),`;
    width: 2.5rem;
    border-radius: 0;
    outline-color: transparent;
    transition: color `).concat(a("tabview.transition.duration"),", outline-color ").concat(a("tabview.transition.duration"),`;
    box-shadow: `).concat(a("tabview.nav.button.shadow"),`;
    border: none;
    cursor: pointer;
    user-select: none;
}

.p-tabview-next-button:focus-visible,
.p-tabview-prev-button:focus-visible {
    outline: `).concat(a("focus.ring.width")," ").concat(a("focus.ring.style")," ").concat(a("focus.ring.color"),`;
    outline-offset: `).concat(a("focus.ring.offset"),`;
}

.p-tabview-next-button:hover,
.p-tabview-prev-button:hover {
    color: `).concat(a("tabview.nav.button.hover.color"),`;
}

.p-tabview-prev-button {
    left: 0;
}

.p-tabview-next-button {
    right: 0;
}

.p-tabview-panels {
    background: `).concat(a("tabview.tab.panel.background"),`;
    color: `).concat(a("tabview.tab.panel.color"),`;
    padding: 0.875rem 1.125rem 1.125rem 1.125rem;
}

.p-tabview-ink-bar {
    z-index: 1;
    display: block;
    position: absolute;
    bottom: -1px;
    height: 1px;
    background: `).concat(a("tabview.tab.active.border.color"),`;
    transition: 250ms cubic-bezier(0.35, 0, 0.25, 1);
}
`)},re={root:function(e){var a=e.props;return["p-tabview p-component",{"p-tabview-scrollable":a.scrollable}]},navContainer:"p-tabview-tablist-container",prevButton:"p-tabview-prev-button",navContent:"p-tabview-tablist-scroll-container",nav:"p-tabview-tablist",tab:{header:function(e){var a=e.instance,n=e.tab,d=e.index;return["p-tabview-tablist-item",a.getTabProp(n,"headerClass"),{"p-tabview-tablist-item-active":a.d_activeIndex===d,"p-disabled":a.getTabProp(n,"disabled")}]},headerAction:"p-tabview-tab-header",headerTitle:"p-tabview-tab-title",content:function(e){var a=e.instance,n=e.tab;return["p-tabview-panel",a.getTabProp(n,"contentClass")]}},inkbar:"p-tabview-ink-bar",nextButton:"p-tabview-next-button",panelContainer:"p-tabview-panels"},se=N.extend({name:"tabview",theme:oe,classes:re}),ue={name:"BaseTabView",extends:F,props:{activeIndex:{type:Number,default:0},lazy:{type:Boolean,default:!1},scrollable:{type:Boolean,default:!1},tabindex:{type:Number,default:0},selectOnFocus:{type:Boolean,default:!1},prevButtonProps:{type:null,default:null},nextButtonProps:{type:null,default:null},prevIcon:{type:String,default:void 0},nextIcon:{type:String,default:void 0}},style:se,provide:function(){return{$pcTabs:void 0,$pcTabView:this,$parentInstance:this}}},j={name:"TabView",extends:ue,inheritAttrs:!1,emits:["update:activeIndex","tab-change","tab-click"],data:function(){return{id:this.$attrs.id,d_activeIndex:this.activeIndex,isPrevButtonDisabled:!0,isNextButtonDisabled:!1}},watch:{"$attrs.id":function(e){this.id=e||U()},activeIndex:function(e){this.d_activeIndex=e,this.scrollInView({index:e})}},mounted:function(){console.warn("Deprecated since v4. Use Tabs component instead."),this.id=this.id||U(),this.updateInkBar(),this.scrollable&&this.updateButtonState()},updated:function(){this.updateInkBar(),this.scrollable&&this.updateButtonState()},methods:{isTabPanel:function(e){return e.type.name==="TabPanel"},isTabActive:function(e){return this.d_activeIndex===e},getTabProp:function(e,a){return e.props?e.props[a]:void 0},getKey:function(e,a){return this.getTabProp(e,"header")||a},getTabHeaderActionId:function(e){return"".concat(this.id,"_").concat(e,"_header_action")},getTabContentId:function(e){return"".concat(this.id,"_").concat(e,"_content")},getTabPT:function(e,a,n){var d=this.tabs.length,o={props:e.props,parent:{instance:this,props:this.$props,state:this.$data},context:{index:n,count:d,first:n===0,last:n===d-1,active:this.isTabActive(n)}};return v(this.ptm("tabpanel.".concat(a),{tabpanel:o}),this.ptm("tabpanel.".concat(a),o),this.ptmo(this.getTabProp(e,"pt"),a,o))},onScroll:function(e){this.scrollable&&this.updateButtonState(),e.preventDefault()},onPrevButtonClick:function(){var e=this.$refs.content,a=k(e),n=e.scrollLeft-a;e.scrollLeft=n<=0?0:n},onNextButtonClick:function(){var e=this.$refs.content,a=k(e)-this.getVisibleButtonWidths(),n=e.scrollLeft+a,d=e.scrollWidth-a;e.scrollLeft=n>=d?d:n},onTabClick:function(e,a,n){this.changeActiveIndex(e,a,n),this.$emit("tab-click",{originalEvent:e,index:n})},onTabKeyDown:function(e,a,n){switch(e.code){case"ArrowLeft":this.onTabArrowLeftKey(e);break;case"ArrowRight":this.onTabArrowRightKey(e);break;case"Home":this.onTabHomeKey(e);break;case"End":this.onTabEndKey(e);break;case"PageDown":this.onPageDownKey(e);break;case"PageUp":this.onPageUpKey(e);break;case"Enter":case"NumpadEnter":case"Space":this.onTabEnterKey(e,a,n);break}},onTabArrowRightKey:function(e){var a=this.findNextHeaderAction(e.target.parentElement);a?this.changeFocusedTab(e,a):this.onTabHomeKey(e),e.preventDefault()},onTabArrowLeftKey:function(e){var a=this.findPrevHeaderAction(e.target.parentElement);a?this.changeFocusedTab(e,a):this.onTabEndKey(e),e.preventDefault()},onTabHomeKey:function(e){var a=this.findFirstHeaderAction();this.changeFocusedTab(e,a),e.preventDefault()},onTabEndKey:function(e){var a=this.findLastHeaderAction();this.changeFocusedTab(e,a),e.preventDefault()},onPageDownKey:function(e){this.scrollInView({index:this.$refs.nav.children.length-2}),e.preventDefault()},onPageUpKey:function(e){this.scrollInView({index:0}),e.preventDefault()},onTabEnterKey:function(e,a,n){this.changeActiveIndex(e,a,n),e.preventDefault()},findNextHeaderAction:function(e){var a=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1,n=a?e:e.nextElementSibling;return n?C(n,"data-p-disabled")||C(n,"data-pc-section")==="inkbar"?this.findNextHeaderAction(n):$(n,'[data-pc-section="headeraction"]'):null},findPrevHeaderAction:function(e){var a=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1,n=a?e:e.previousElementSibling;return n?C(n,"data-p-disabled")||C(n,"data-pc-section")==="inkbar"?this.findPrevHeaderAction(n):$(n,'[data-pc-section="headeraction"]'):null},findFirstHeaderAction:function(){return this.findNextHeaderAction(this.$refs.nav.firstElementChild,!0)},findLastHeaderAction:function(){return this.findPrevHeaderAction(this.$refs.nav.lastElementChild,!0)},changeActiveIndex:function(e,a,n){!this.getTabProp(a,"disabled")&&this.d_activeIndex!==n&&(this.d_activeIndex=n,this.$emit("update:activeIndex",n),this.$emit("tab-change",{originalEvent:e,index:n}),this.scrollInView({index:n}))},changeFocusedTab:function(e,a){if(a&&(W(a),this.scrollInView({element:a}),this.selectOnFocus)){var n=parseInt(a.parentElement.dataset.pcIndex,10),d=this.tabs[n];this.changeActiveIndex(e,d,n)}},scrollInView:function(e){var a=e.element,n=e.index,d=n===void 0?-1:n,o=a||this.$refs.nav.children[d];o&&o.scrollIntoView&&o.scrollIntoView({block:"nearest"})},updateInkBar:function(){var e=this.$refs.nav.children[this.d_activeIndex];this.$refs.inkbar.style.width=k(e)+"px",this.$refs.inkbar.style.left=K(e).left-K(this.$refs.nav).left+"px"},updateButtonState:function(){var e=this.$refs.content,a=e.scrollLeft,n=e.scrollWidth,d=k(e);this.isPrevButtonDisabled=a===0,this.isNextButtonDisabled=parseInt(a)===n-d},getVisibleButtonWidths:function(){var e=this.$refs,a=e.prevBtn,n=e.nextBtn;return[a,n].reduce(function(d,o){return o?d+k(o):d},0)}},computed:{tabs:function(){var e=this;return this.$slots.default().reduce(function(a,n){return e.isTabPanel(n)?a.push(n):n.children&&n.children instanceof Array&&n.children.forEach(function(d){e.isTabPanel(d)&&a.push(d)}),a},[])},prevButtonAriaLabel:function(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.previous:void 0},nextButtonAriaLabel:function(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.next:void 0}},directives:{ripple:ee},components:{ChevronLeftIcon:Q,ChevronRightIcon:Z}};function B(t){"@babel/helpers - typeof";return B=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(e){return typeof e}:function(e){return e&&typeof Symbol=="function"&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},B(t)}function E(t,e){var a=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter(function(d){return Object.getOwnPropertyDescriptor(t,d).enumerable})),a.push.apply(a,n)}return a}function g(t){for(var e=1;e<arguments.length;e++){var a=arguments[e]!=null?arguments[e]:{};e%2?E(Object(a),!0).forEach(function(n){de(t,n,a[n])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(a)):E(Object(a)).forEach(function(n){Object.defineProperty(t,n,Object.getOwnPropertyDescriptor(a,n))})}return t}function de(t,e,a){return(e=pe(e))in t?Object.defineProperty(t,e,{value:a,enumerable:!0,configurable:!0,writable:!0}):t[e]=a,t}function pe(t){var e=ce(t,"string");return B(e)=="symbol"?e:e+""}function ce(t,e){if(B(t)!="object"||!t)return t;var a=t[Symbol.toPrimitive];if(a!==void 0){var n=a.call(t,e);if(B(n)!="object")return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return(e==="string"?String:Number)(t)}var be=["tabindex","aria-label"],ve=["data-p-active","data-p-disabled","data-pc-index"],fe=["id","tabindex","aria-disabled","aria-selected","aria-controls","onClick","onKeydown"],me=["tabindex","aria-label"],ge=["id","aria-labelledby","data-pc-index","data-p-active"];function he(t,e,a,n,d,o){var h=Y("ripple");return b(),m("div",v({class:t.cx("root"),role:"tablist"},t.ptmi("root")),[r("div",v({class:t.cx("navContainer")},t.ptm("navContainer")),[t.scrollable&&!d.isPrevButtonDisabled?A((b(),m("button",v({key:0,ref:"prevBtn",type:"button",class:t.cx("prevButton"),tabindex:t.tabindex,"aria-label":o.prevButtonAriaLabel,onClick:e[0]||(e[0]=function(){return o.onPrevButtonClick&&o.onPrevButtonClick.apply(o,arguments)})},g(g({},t.prevButtonProps),t.ptm("prevButton")),{"data-pc-group-section":"navbutton"}),[x(t.$slots,"previcon",{},function(){return[(b(),P(I(t.prevIcon?"span":"ChevronLeftIcon"),v({"aria-hidden":"true",class:t.prevIcon},t.ptm("prevIcon")),null,16,["class"]))]})],16,be)),[[h]]):w("",!0),r("div",v({ref:"content",class:t.cx("navContent"),onScroll:e[1]||(e[1]=function(){return o.onScroll&&o.onScroll.apply(o,arguments)})},t.ptm("navContent")),[r("ul",v({ref:"nav",class:t.cx("nav")},t.ptm("nav")),[(b(!0),m(y,null,L(o.tabs,function(u,c){return b(),m("li",v({key:o.getKey(u,c),style:o.getTabProp(u,"headerStyle"),class:t.cx("tab.header",{tab:u,index:c}),role:"presentation",ref_for:!0},g(g(g({},o.getTabProp(u,"headerProps")),o.getTabPT(u,"root",c)),o.getTabPT(u,"header",c)),{"data-pc-name":"tabpanel","data-p-active":d.d_activeIndex===c,"data-p-disabled":o.getTabProp(u,"disabled"),"data-pc-index":c}),[A((b(),m("a",v({id:o.getTabHeaderActionId(c),class:t.cx("tab.headerAction"),tabindex:o.getTabProp(u,"disabled")||!o.isTabActive(c)?-1:t.tabindex,role:"tab","aria-disabled":o.getTabProp(u,"disabled"),"aria-selected":o.isTabActive(c),"aria-controls":o.getTabContentId(c),onClick:function(S){return o.onTabClick(S,u,c)},onKeydown:function(S){return o.onTabKeyDown(S,u,c)},ref_for:!0},g(g({},o.getTabProp(u,"headerActionProps")),o.getTabPT(u,"headerAction",c))),[u.props&&u.props.header?(b(),m("span",v({key:0,class:t.cx("tab.headerTitle"),ref_for:!0},o.getTabPT(u,"headerTitle",c)),M(u.props.header),17)):w("",!0),u.children&&u.children.header?(b(),P(I(u.children.header),{key:1})):w("",!0)],16,fe)),[[h]])],16,ve)}),128)),r("li",v({ref:"inkbar",class:t.cx("inkbar"),role:"presentation","aria-hidden":"true"},t.ptm("inkbar")),null,16)],16)],16),t.scrollable&&!d.isNextButtonDisabled?A((b(),m("button",v({key:1,ref:"nextBtn",type:"button",class:t.cx("nextButton"),tabindex:t.tabindex,"aria-label":o.nextButtonAriaLabel,onClick:e[2]||(e[2]=function(){return o.onNextButtonClick&&o.onNextButtonClick.apply(o,arguments)})},g(g({},t.nextButtonProps),t.ptm("nextButton")),{"data-pc-group-section":"navbutton"}),[x(t.$slots,"nexticon",{},function(){return[(b(),P(I(t.nextIcon?"span":"ChevronRightIcon"),v({"aria-hidden":"true",class:t.nextIcon},t.ptm("nextIcon")),null,16,["class"]))]})],16,me)),[[h]]):w("",!0)],16),r("div",v({class:t.cx("panelContainer")},t.ptm("panelContainer")),[(b(!0),m(y,null,L(o.tabs,function(u,c){return b(),m(y,{key:o.getKey(u,c)},[!t.lazy||o.isTabActive(c)?A((b(),m("div",v({key:0,id:o.getTabContentId(c),style:o.getTabProp(u,"contentStyle"),class:t.cx("tab.content",{tab:u}),role:"tabpanel","aria-labelledby":o.getTabHeaderActionId(c),ref_for:!0},g(g(g({},o.getTabProp(u,"contentProps")),o.getTabPT(u,"root",c)),o.getTabPT(u,"content",c)),{"data-pc-name":"tabpanel","data-pc-index":c,"data-p-active":d.d_activeIndex===c}),[(b(),P(I(u)))],16,ge)),[[O,t.lazy?!0:o.isTabActive(c)]]):w("",!0)],64)}),128))],16)],16)}j.render=he;const we={class:"p-6 max-w-4xl mx-auto"},ye={class:"grid gap-4"},Te={class:"grid gap-4"},Ve={class:"grid gap-4"},Pe={class:"mt-6"},_e={__name:"General",props:{settings:Object},setup(t){const e=t,a=G(),n=q({settings:{...e.settings}}),d=[{label:"English",value:"English"}],o=[{label:"(GMT/UTC 6:00) Dhaka",value:"Asia/Dhaka"}],h=[{label:"d-m-Y",value:"d-m-Y"},{label:"m/d/Y",value:"m/d/Y"},{label:"Y-m-d",value:"Y-m-d"}],u=[{label:"24 Hours",value:"24 Hours"},{label:"12 Hours",value:"12 Hours"}],c=[{label:"Twilio",value:"twilio"},{label:"Vonage",value:"vonage"},{label:"Disabled",value:"null"}];function H(){n.put(route("settings.update"),{onSuccess:()=>{a.add({severity:"success",summary:"Successful!",detail:"Successfully Updated!",life:3e3})}})}return(S,l)=>(b(),P(z,null,{default:V(()=>[p(i(J),{title:"Settings"}),r("div",we,[p(i(j),null,{default:V(()=>[p(i(D),{header:"General"},{default:V(()=>[r("div",ye,[r("div",null,[l[22]||(l[22]=r("label",null,"Application Name",-1)),p(i(f),{modelValue:i(n).settings.general_application_name,"onUpdate:modelValue":l[0]||(l[0]=s=>i(n).settings.general_application_name=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[23]||(l[23]=r("label",null,"Address",-1)),p(i(f),{modelValue:i(n).settings.general_address,"onUpdate:modelValue":l[1]||(l[1]=s=>i(n).settings.general_address=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[24]||(l[24]=r("label",null,"Phone",-1)),p(i(f),{modelValue:i(n).settings.general_phone,"onUpdate:modelValue":l[2]||(l[2]=s=>i(n).settings.general_phone=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[25]||(l[25]=r("label",null,"Footer Text",-1)),p(i(te),{rows:"2",modelValue:i(n).settings.general_footer_text,"onUpdate:modelValue":l[3]||(l[3]=s=>i(n).settings.general_footer_text=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[26]||(l[26]=r("label",null,"Language",-1)),p(i(T),{class:"w-full",options:d,optionLabel:"label",optionValue:"value",modelValue:i(n).settings.general_language,"onUpdate:modelValue":l[4]||(l[4]=s=>i(n).settings.general_language=s)},null,8,["modelValue"])]),r("div",null,[l[27]||(l[27]=r("label",null,"Timezone",-1)),p(i(T),{class:"w-full",filter:"",options:o,optionLabel:"label",optionValue:"value",modelValue:i(n).settings.general_timezone,"onUpdate:modelValue":l[5]||(l[5]=s=>i(n).settings.general_timezone=s)},null,8,["modelValue"])]),r("div",null,[l[28]||(l[28]=r("label",null,"Date Format",-1)),p(i(T),{class:"w-full",options:h,optionLabel:"label",optionValue:"value",modelValue:i(n).settings.general_date_format,"onUpdate:modelValue":l[6]||(l[6]=s=>i(n).settings.general_date_format=s)},null,8,["modelValue"])]),r("div",null,[l[29]||(l[29]=r("label",null,"Time Format",-1)),p(i(T),{class:"w-full",options:u,optionLabel:"label",optionValue:"value",modelValue:i(n).settings.general_time_format,"onUpdate:modelValue":l[7]||(l[7]=s=>i(n).settings.general_time_format=s)},null,8,["modelValue"])])])]),_:1}),p(i(D),{header:"Email (SMTP)"},{default:V(()=>[r("div",Te,[r("div",null,[l[30]||(l[30]=r("label",null,"Host",-1)),p(i(f),{modelValue:i(n).settings.mail_host,"onUpdate:modelValue":l[8]||(l[8]=s=>i(n).settings.mail_host=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[31]||(l[31]=r("label",null,"Port",-1)),p(i(f),{modelValue:i(n).settings.mail_port,"onUpdate:modelValue":l[9]||(l[9]=s=>i(n).settings.mail_port=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[32]||(l[32]=r("label",null,"Username",-1)),p(i(f),{modelValue:i(n).settings.mail_username,"onUpdate:modelValue":l[10]||(l[10]=s=>i(n).settings.mail_username=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[33]||(l[33]=r("label",null,"Password",-1)),p(i(f),{type:"password",modelValue:i(n).settings.mail_password,"onUpdate:modelValue":l[11]||(l[11]=s=>i(n).settings.mail_password=s),placeholder:"******",class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[34]||(l[34]=r("label",null,"Encryption",-1)),p(i(T),{class:"w-full",options:[{label:"TLS",value:"tls"},{label:"SSL",value:"ssl"},{label:"None",value:"null"}],optionLabel:"label",optionValue:"value",modelValue:i(n).settings.mail_encryption,"onUpdate:modelValue":l[12]||(l[12]=s=>i(n).settings.mail_encryption=s)},null,8,["modelValue"])]),r("div",null,[l[35]||(l[35]=r("label",null,"From Address",-1)),p(i(f),{modelValue:i(n).settings.mail_from_address,"onUpdate:modelValue":l[13]||(l[13]=s=>i(n).settings.mail_from_address=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[36]||(l[36]=r("label",null,"From Name",-1)),p(i(f),{modelValue:i(n).settings.mail_from_name,"onUpdate:modelValue":l[14]||(l[14]=s=>i(n).settings.mail_from_name=s),class:"w-full"},null,8,["modelValue"])])])]),_:1}),p(i(D),{header:"SMS"},{default:V(()=>[r("div",Ve,[r("div",null,[l[37]||(l[37]=r("label",null,"Driver",-1)),p(i(T),{class:"w-full",options:c,optionLabel:"label",optionValue:"value",modelValue:i(n).settings.sms_driver,"onUpdate:modelValue":l[15]||(l[15]=s=>i(n).settings.sms_driver=s)},null,8,["modelValue"])]),i(n).settings.sms_driver==="twilio"?(b(),m(y,{key:0},[r("div",null,[l[38]||(l[38]=r("label",null,"Twilio SID",-1)),p(i(f),{modelValue:i(n).settings.sms_twilio_sid,"onUpdate:modelValue":l[16]||(l[16]=s=>i(n).settings.sms_twilio_sid=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[39]||(l[39]=r("label",null,"Twilio Token",-1)),p(i(f),{type:"password",modelValue:i(n).settings.sms_twilio_token,"onUpdate:modelValue":l[17]||(l[17]=s=>i(n).settings.sms_twilio_token=s),placeholder:"******",class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[40]||(l[40]=r("label",null,"From",-1)),p(i(f),{modelValue:i(n).settings.sms_twilio_from,"onUpdate:modelValue":l[18]||(l[18]=s=>i(n).settings.sms_twilio_from=s),class:"w-full"},null,8,["modelValue"])])],64)):i(n).settings.sms_driver==="vonage"?(b(),m(y,{key:1},[r("div",null,[l[41]||(l[41]=r("label",null,"API Key",-1)),p(i(f),{modelValue:i(n).settings.sms_vonage_api_key,"onUpdate:modelValue":l[19]||(l[19]=s=>i(n).settings.sms_vonage_api_key=s),class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[42]||(l[42]=r("label",null,"API Secret",-1)),p(i(f),{type:"password",modelValue:i(n).settings.sms_vonage_api_secret,"onUpdate:modelValue":l[20]||(l[20]=s=>i(n).settings.sms_vonage_api_secret=s),placeholder:"******",class:"w-full"},null,8,["modelValue"])]),r("div",null,[l[43]||(l[43]=r("label",null,"From",-1)),p(i(f),{modelValue:i(n).settings.sms_vonage_from,"onUpdate:modelValue":l[21]||(l[21]=s=>i(n).settings.sms_vonage_from=s),class:"w-full"},null,8,["modelValue"])])],64)):w("",!0)])]),_:1})]),_:1}),r("div",Pe,[p(i(X),{loading:i(n).processing,label:"Save All",onClick:H},null,8,["loading"])])])]),_:1}))}};export{_e as default};
