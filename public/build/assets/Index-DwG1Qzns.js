import{T as ke}from"./index-BbXgw24U.js";import{b as xe,c as we}from"./index-ZQIpPJKl.js";import{B as J,k as B,l as D,q as X,v as Le,z as Pe,y as G,A as Se,p as C,D as R,U as Te,V as ae,m as c,X as Me,G as O,H as se,I as L,o as l,w as I,c as b,d as w,F as V,i as Q,b as g,N as E,L as _,j as F,t as S,T as oe,a as m,J as A,M as ee,Y as q,Z as Ce,$ as te,a0 as Ke,f as ne,r as j,Q as ie,W as P,u as h,e as W,a1 as Ee}from"./app-DkT2A0GX.js";import{d as Ae,s as De}from"./debounce-76bAcy72.js";import{r as Ve}from"./imageHelper-BscTlunE.js";import{a as K,s as re}from"./index-Dx3VxCfQ.js";import{b as Fe,a as Be,s as M}from"./index-DagPqkZe.js";import{s as _e}from"./index-BQBsDN_Y.js";import{s as N}from"./index-Den17IzW.js";import{s as Oe,C as ze,Z as Y,O as Ne,U as $}from"./index-7stLpnNp.js";import{s as Re}from"./index-DvJvTJ0T.js";import{s as H}from"./index-B5cosmNn.js";import{R as ue}from"./index-7GFiA92J.js";import{_ as $e}from"./AuthenticatedLayout-nuWeTT7f.js";import"./index-Bnbor56d.js";import"./index-DFxSo9Al.js";import"./index-plH-LA0Y.js";import"./index-Bo6UZdGS.js";import"./index-1ZxLlwO_.js";import"./index-Dw0qEVNE.js";import"./index-Bo-OBHSb.js";import"./index-CW45MO4N.js";import"./index-CYUnySpj.js";import"./index-Bhg74Hxi.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./FavIcons-CSrJPvb7.js";var He=function(e){var t=e.dt;return`
.p-tieredmenu {
    background: `.concat(t("tieredmenu.background"),`;
    color: `).concat(t("tieredmenu.color"),`;
    border: 1px solid `).concat(t("tieredmenu.border.color"),`;
    border-radius: `).concat(t("tieredmenu.border.radius"),`;
    min-width: 12.5rem;
}

.p-tieredmenu-root-list,
.p-tieredmenu-submenu {
    margin: 0;
    padding: `).concat(t("tieredmenu.list.padding"),`;
    list-style: none;
    outline: 0 none;
    display: flex;
    flex-direction: column;
    gap: `).concat(t("tieredmenu.list.gap"),`;
}

.p-tieredmenu-submenu {
    position: absolute;
    min-width: 100%;
    z-index: 1;
    background: `).concat(t("tieredmenu.background"),`;
    color: `).concat(t("tieredmenu.color"),`;
    border: 1px solid `).concat(t("tieredmenu.border.color"),`;
    border-radius: `).concat(t("tieredmenu.border.radius"),`;
    box-shadow: `).concat(t("tieredmenu.shadow"),`;
}

.p-tieredmenu-item {
    position: relative;
}

.p-tieredmenu-item-content {
    transition: background `).concat(t("tieredmenu.transition.duration"),", color ").concat(t("tieredmenu.transition.duration"),`;
    border-radius: `).concat(t("tieredmenu.item.border.radius"),`;
    color: `).concat(t("tieredmenu.item.color"),`;
}

.p-tieredmenu-item-link {
    cursor: pointer;
    display: flex;
    align-items: center;
    text-decoration: none;
    overflow: hidden;
    position: relative;
    color: inherit;
    padding: `).concat(t("tieredmenu.item.padding"),`;
    gap: `).concat(t("tieredmenu.item.gap"),`;
    user-select: none;
    outline: 0 none;
}

.p-tieredmenu-item-label {
    line-height: 1;
}

.p-tieredmenu-item-icon {
    color: `).concat(t("tieredmenu.item.icon.color"),`;
}

.p-tieredmenu-submenu-icon {
    color: `).concat(t("tieredmenu.submenu.icon.color"),`;
    margin-left: auto;
    font-size: `).concat(t("tieredmenu.submenu.icon.size"),`;
    width: `).concat(t("tieredmenu.submenu.icon.size"),`;
    height: `).concat(t("tieredmenu.submenu.icon.size"),`;
}

.p-tieredmenu-submenu-icon:dir(rtl) {
    margin-left: 0;
    margin-right: auto;
}

.p-tieredmenu-item.p-focus > .p-tieredmenu-item-content {
    color: `).concat(t("tieredmenu.item.focus.color"),`;
    background: `).concat(t("tieredmenu.item.focus.background"),`;
}

.p-tieredmenu-item.p-focus > .p-tieredmenu-item-content .p-tieredmenu-item-icon {
    color: `).concat(t("tieredmenu.item.icon.focus.color"),`;
}

.p-tieredmenu-item.p-focus > .p-tieredmenu-item-content .p-tieredmenu-submenu-icon {
    color: `).concat(t("tieredmenu.submenu.icon.focus.color"),`;
}

.p-tieredmenu-item:not(.p-disabled) > .p-tieredmenu-item-content:hover {
    color: `).concat(t("tieredmenu.item.focus.color"),`;
    background: `).concat(t("tieredmenu.item.focus.background"),`;
}

.p-tieredmenu-item:not(.p-disabled) > .p-tieredmenu-item-content:hover .p-tieredmenu-item-icon {
    color: `).concat(t("tieredmenu.item.icon.focus.color"),`;
}

.p-tieredmenu-item:not(.p-disabled) > .p-tieredmenu-item-content:hover .p-tieredmenu-submenu-icon {
    color: `).concat(t("tieredmenu.submenu.icon.focus.color"),`;
}

.p-tieredmenu-item-active > .p-tieredmenu-item-content {
    color: `).concat(t("tieredmenu.item.active.color"),`;
    background: `).concat(t("tieredmenu.item.active.background"),`;
}

.p-tieredmenu-item-active > .p-tieredmenu-item-content .p-tieredmenu-item-icon {
    color: `).concat(t("tieredmenu.item.icon.active.color"),`;
}

.p-tieredmenu-item-active > .p-tieredmenu-item-content .p-tieredmenu-submenu-icon {
    color: `).concat(t("tieredmenu.submenu.icon.active.color"),`;
}

.p-tieredmenu-separator {
    border-block-start: 1px solid `).concat(t("tieredmenu.separator.border.color"),`;
}

.p-tieredmenu-overlay {
    box-shadow: `).concat(t("tieredmenu.shadow"),`;
}

.p-tieredmenu-enter-from,
.p-tieredmenu-leave-active {
    opacity: 0;
}

.p-tieredmenu-enter-active {
    transition: opacity 250ms;
}

.p-tieredmenu-mobile .p-tieredmenu-submenu {
    position: static;
    box-shadow: none;
    border: 0 none;
    padding-inline-start: `).concat(t("tieredmenu.submenu.mobile.indent"),`;
    padding-inline-end: 0;
}

.p-tieredmenu-mobile .p-tieredmenu-submenu:dir(rtl) {
    padding-inline-start: 0;
    padding-inline-end: `).concat(t("tieredmenu.submenu.mobile.indent"),`;
}

.p-tieredmenu-mobile .p-tieredmenu-submenu-icon {
    transition: transform 0.2s;
    transform: rotate(90deg);
}

.p-tieredmenu-mobile .p-tieredmenu-item-active > .p-tieredmenu-item-content .p-tieredmenu-submenu-icon {
    transform: rotate(-90deg);
}
`)},Ue={submenu:function(e){var t=e.instance,r=e.processedItem;return{display:t.isItemActive(r)?"flex":"none"}}},Ze={root:function(e){var t=e.props,r=e.instance;return["p-tieredmenu p-component",{"p-tieredmenu-overlay":t.popup,"p-tieredmenu-mobile":r.queryMatches}]},start:"p-tieredmenu-start",rootList:"p-tieredmenu-root-list",item:function(e){var t=e.instance,r=e.processedItem;return["p-tieredmenu-item",{"p-tieredmenu-item-active":t.isItemActive(r),"p-focus":t.isItemFocused(r),"p-disabled":t.isItemDisabled(r)}]},itemContent:"p-tieredmenu-item-content",itemLink:"p-tieredmenu-item-link",itemIcon:"p-tieredmenu-item-icon",itemLabel:"p-tieredmenu-item-label",submenuIcon:"p-tieredmenu-submenu-icon",submenu:"p-tieredmenu-submenu",separator:"p-tieredmenu-separator",end:"p-tieredmenu-end"},Ge=J.extend({name:"tieredmenu",theme:He,classes:Ze,inlineStyles:Ue}),qe={name:"BaseTieredMenu",extends:H,props:{popup:{type:Boolean,default:!1},model:{type:Array,default:null},appendTo:{type:[String,Object],default:"body"},breakpoint:{type:String,default:"960px"},autoZIndex:{type:Boolean,default:!0},baseZIndex:{type:Number,default:0},disabled:{type:Boolean,default:!1},tabindex:{type:Number,default:0},ariaLabelledby:{type:String,default:null},ariaLabel:{type:String,default:null}},style:Ge,provide:function(){return{$pcTieredMenu:this,$parentInstance:this}}},le={name:"TieredMenuSub",hostName:"TieredMenu",extends:H,emits:["item-click","item-mouseenter","item-mousemove"],container:null,props:{menuId:{type:String,default:null},focusedItemId:{type:String,default:null},items:{type:Array,default:null},visible:{type:Boolean,default:!1},level:{type:Number,default:0},templates:{type:Object,default:null},activeItemPath:{type:Object,default:null},tabindex:{type:Number,default:0}},methods:{getItemId:function(e){return"".concat(this.menuId,"_").concat(e.key)},getItemKey:function(e){return this.getItemId(e)},getItemProp:function(e,t,r){return e&&e.item?ae(e.item[t],r):void 0},getItemLabel:function(e){return this.getItemProp(e,"label")},getItemLabelId:function(e){return"".concat(this.menuId,"_").concat(e.key,"_label")},getPTOptions:function(e,t,r){return this.ptm(r,{context:{item:e.item,index:t,active:this.isItemActive(e),focused:this.isItemFocused(e),disabled:this.isItemDisabled(e)}})},isItemActive:function(e){return this.activeItemPath.some(function(t){return t.key===e.key})},isItemVisible:function(e){return this.getItemProp(e,"visible")!==!1},isItemDisabled:function(e){return this.getItemProp(e,"disabled")},isItemFocused:function(e){return this.focusedItemId===this.getItemId(e)},isItemGroup:function(e){return B(e.items)},onEnter:function(){Me(this.container,this.level)},onItemClick:function(e,t){this.getItemProp(t,"command",{originalEvent:e,item:t.item}),this.$emit("item-click",{originalEvent:e,processedItem:t,isFocus:!0})},onItemMouseEnter:function(e,t){this.$emit("item-mouseenter",{originalEvent:e,processedItem:t})},onItemMouseMove:function(e,t){this.$emit("item-mousemove",{originalEvent:e,processedItem:t})},getAriaSetSize:function(){var e=this;return this.items.filter(function(t){return e.isItemVisible(t)&&!e.getItemProp(t,"separator")}).length},getAriaPosInset:function(e){var t=this;return e-this.items.slice(0,e).filter(function(r){return t.isItemVisible(r)&&t.getItemProp(r,"separator")}).length+1},getMenuItemProps:function(e,t){return{action:c({class:this.cx("itemLink"),tabindex:-1},this.getPTOptions(e,t,"itemLink")),icon:c({class:[this.cx("itemIcon"),this.getItemProp(e,"icon")]},this.getPTOptions(e,t,"itemIcon")),label:c({class:this.cx("itemLabel")},this.getPTOptions(e,t,"itemLabel")),submenuicon:c({class:this.cx("submenuIcon")},this.getPTOptions(e,t,"submenuIcon"))}},containerRef:function(e){this.container=e}},components:{AngleRightIcon:Fe},directives:{ripple:ue}},je=["tabindex"],We=["id","aria-label","aria-disabled","aria-expanded","aria-haspopup","aria-level","aria-setsize","aria-posinset","data-p-active","data-p-focused","data-p-disabled"],Ye=["onClick","onMouseenter","onMousemove"],Je=["href","target"],Qe=["id"],Xe=["id"];function et(n,e,t,r,s,i){var p=O("AngleRightIcon"),d=O("TieredMenuSub",!0),u=se("ripple");return l(),L(oe,c({name:"p-tieredmenu",onEnter:i.onEnter},n.ptm("menu.transition")),{default:I(function(){return[t.level===0||t.visible?(l(),b("ul",{key:0,ref:i.containerRef,tabindex:t.tabindex},[(l(!0),b(V,null,Q(t.items,function(a,v){return l(),b(V,{key:i.getItemKey(a)},[i.isItemVisible(a)&&!i.getItemProp(a,"separator")?(l(),b("li",c({key:0,id:i.getItemId(a),style:i.getItemProp(a,"style"),class:[n.cx("item",{processedItem:a}),i.getItemProp(a,"class")],role:"menuitem","aria-label":i.getItemLabel(a),"aria-disabled":i.isItemDisabled(a)||void 0,"aria-expanded":i.isItemGroup(a)?i.isItemActive(a):void 0,"aria-haspopup":i.isItemGroup(a)&&!i.getItemProp(a,"to")?"menu":void 0,"aria-level":t.level+1,"aria-setsize":i.getAriaSetSize(),"aria-posinset":i.getAriaPosInset(v),ref_for:!0},i.getPTOptions(a,v,"item"),{"data-p-active":i.isItemActive(a),"data-p-focused":i.isItemFocused(a),"data-p-disabled":i.isItemDisabled(a)}),[g("div",c({class:n.cx("itemContent"),onClick:function(x){return i.onItemClick(x,a)},onMouseenter:function(x){return i.onItemMouseEnter(x,a)},onMousemove:function(x){return i.onItemMouseMove(x,a)},ref_for:!0},i.getPTOptions(a,v,"itemContent")),[t.templates.item?(l(),L(_(t.templates.item),{key:1,item:a.item,hasSubmenu:i.getItemProp(a,"items"),label:i.getItemLabel(a),props:i.getMenuItemProps(a,v)},null,8,["item","hasSubmenu","label","props"])):E((l(),b("a",c({key:0,href:i.getItemProp(a,"url"),class:n.cx("itemLink"),target:i.getItemProp(a,"target"),tabindex:"-1",ref_for:!0},i.getPTOptions(a,v,"itemLink")),[t.templates.itemicon?(l(),L(_(t.templates.itemicon),{key:0,item:a.item,class:F(n.cx("itemIcon"))},null,8,["item","class"])):i.getItemProp(a,"icon")?(l(),b("span",c({key:1,class:[n.cx("itemIcon"),i.getItemProp(a,"icon")],ref_for:!0},i.getPTOptions(a,v,"itemIcon")),null,16)):w("",!0),g("span",c({id:i.getItemLabelId(a),class:n.cx("itemLabel"),ref_for:!0},i.getPTOptions(a,v,"itemLabel")),S(i.getItemLabel(a)),17,Qe),i.getItemProp(a,"items")?(l(),b(V,{key:2},[t.templates.submenuicon?(l(),L(_(t.templates.submenuicon),c({key:0,class:n.cx("submenuIcon"),active:i.isItemActive(a),ref_for:!0},i.getPTOptions(a,v,"submenuIcon")),null,16,["class","active"])):(l(),L(p,c({key:1,class:n.cx("submenuIcon"),ref_for:!0},i.getPTOptions(a,v,"submenuIcon")),null,16,["class"]))],64)):w("",!0)],16,Je)),[[u]])],16,Ye),i.isItemVisible(a)&&i.isItemGroup(a)?(l(),L(d,c({key:0,id:i.getItemId(a)+"_list",class:n.cx("submenu"),style:n.sx("submenu",!0,{processedItem:a}),"aria-labelledby":i.getItemLabelId(a),role:"menu",menuId:t.menuId,focusedItemId:t.focusedItemId,items:a.items,templates:t.templates,activeItemPath:t.activeItemPath,level:t.level+1,visible:i.isItemActive(a)&&i.isItemGroup(a),pt:n.pt,unstyled:n.unstyled,onItemClick:e[0]||(e[0]=function(y){return n.$emit("item-click",y)}),onItemMouseenter:e[1]||(e[1]=function(y){return n.$emit("item-mouseenter",y)}),onItemMousemove:e[2]||(e[2]=function(y){return n.$emit("item-mousemove",y)}),ref_for:!0},n.ptm("submenu")),null,16,["id","class","style","aria-labelledby","menuId","focusedItemId","items","templates","activeItemPath","level","visible","pt","unstyled"])):w("",!0)],16,We)):w("",!0),i.isItemVisible(a)&&i.getItemProp(a,"separator")?(l(),b("li",c({key:1,id:i.getItemId(a),style:i.getItemProp(a,"style"),class:[n.cx("separator"),i.getItemProp(a,"class")],role:"separator",ref_for:!0},n.ptm("separator")),null,16,Xe)):w("",!0)],64)}),128))],8,je)):w("",!0)]}),_:1},16,["onEnter"])}le.render=et;var de={name:"TieredMenu",extends:qe,inheritAttrs:!1,emits:["focus","blur","before-show","before-hide","hide","show"],outsideClickListener:null,matchMediaListener:null,scrollHandler:null,resizeListener:null,target:null,container:null,menubar:null,searchTimeout:null,searchValue:null,data:function(){return{id:this.$attrs.id,focused:!1,focusedItemInfo:{index:-1,level:0,parentKey:""},activeItemPath:[],visible:!this.popup,submenuVisible:!1,dirty:!1,query:null,queryMatches:!1}},watch:{"$attrs.id":function(e){this.id=e||$()},activeItemPath:function(e){this.popup||(B(e)?(this.bindOutsideClickListener(),this.bindResizeListener()):(this.unbindOutsideClickListener(),this.unbindResizeListener()))}},mounted:function(){this.id=this.id||$(),this.bindMatchMediaListener()},beforeUnmount:function(){this.unbindOutsideClickListener(),this.unbindResizeListener(),this.unbindMatchMediaListener(),this.scrollHandler&&(this.scrollHandler.destroy(),this.scrollHandler=null),this.container&&this.autoZIndex&&Y.clear(this.container),this.target=null,this.container=null},methods:{getItemProp:function(e,t){return e?ae(e[t]):void 0},getItemLabel:function(e){return this.getItemProp(e,"label")},isItemDisabled:function(e){return this.getItemProp(e,"disabled")},isItemVisible:function(e){return this.getItemProp(e,"visible")!==!1},isItemGroup:function(e){return B(this.getItemProp(e,"items"))},isItemSeparator:function(e){return this.getItemProp(e,"separator")},getProccessedItemLabel:function(e){return e?this.getItemLabel(e.item):void 0},isProccessedItemGroup:function(e){return e&&B(e.items)},toggle:function(e){this.visible?this.hide(e,!0):this.show(e)},show:function(e,t){this.popup&&(this.$emit("before-show"),this.visible=!0,this.target=this.target||e.currentTarget,this.relatedTarget=e.relatedTarget||null),t&&C(this.menubar)},hide:function(e,t){this.popup&&(this.$emit("before-hide"),this.visible=!1),this.activeItemPath=[],this.focusedItemInfo={index:-1,level:0,parentKey:""},t&&C(this.relatedTarget||this.target||this.menubar),this.dirty=!1},onFocus:function(e){this.focused=!0,this.popup||(this.focusedItemInfo=this.focusedItemInfo.index!==-1?this.focusedItemInfo:{index:this.findFirstFocusedItemIndex(),level:0,parentKey:""}),this.$emit("focus",e)},onBlur:function(e){this.focused=!1,this.focusedItemInfo={index:-1,level:0,parentKey:""},this.searchValue="",this.dirty=!1,this.$emit("blur",e)},onKeyDown:function(e){if(this.disabled){e.preventDefault();return}var t=e.metaKey||e.ctrlKey;switch(e.code){case"ArrowDown":this.onArrowDownKey(e);break;case"ArrowUp":this.onArrowUpKey(e);break;case"ArrowLeft":this.onArrowLeftKey(e);break;case"ArrowRight":this.onArrowRightKey(e);break;case"Home":this.onHomeKey(e);break;case"End":this.onEndKey(e);break;case"Space":this.onSpaceKey(e);break;case"Enter":case"NumpadEnter":this.onEnterKey(e);break;case"Escape":this.onEscapeKey(e);break;case"Tab":this.onTabKey(e);break;case"PageDown":case"PageUp":case"Backspace":case"ShiftLeft":case"ShiftRight":break;default:!t&&Te(e.key)&&this.searchItems(e,e.key);break}},onItemChange:function(e,t){var r=e.processedItem,s=e.isFocus;if(!R(r)){var i=r.index,p=r.key,d=r.level,u=r.parentKey,a=r.items,v=B(a),y=this.activeItemPath.filter(function(x){return x.parentKey!==u&&x.parentKey!==p});v&&(y.push(r),this.submenuVisible=!0),this.focusedItemInfo={index:i,level:d,parentKey:u},v&&(this.dirty=!0),s&&C(this.menubar),!(t==="hover"&&this.queryMatches)&&(this.activeItemPath=y)}},onOverlayClick:function(e){Ne.emit("overlay-click",{originalEvent:e,target:this.target})},onItemClick:function(e){var t=e.originalEvent,r=e.processedItem,s=this.isProccessedItemGroup(r),i=R(r.parent),p=this.isSelected(r);if(p){var d=r.index,u=r.key,a=r.level,v=r.parentKey;this.activeItemPath=this.activeItemPath.filter(function(x){return u!==x.key&&u.startsWith(x.key)}),this.focusedItemInfo={index:d,level:a,parentKey:v},this.dirty=!i,C(this.menubar)}else if(s)this.onItemChange(e);else{var y=i?r:this.activeItemPath.find(function(x){return x.parentKey===""});this.hide(t),this.changeFocusedItemIndex(t,y?y.index:-1),C(this.menubar)}},onItemMouseEnter:function(e){this.dirty&&this.onItemChange(e,"hover")},onItemMouseMove:function(e){this.focused&&this.changeFocusedItemIndex(e,e.processedItem.index)},onArrowDownKey:function(e){var t=this.focusedItemInfo.index!==-1?this.findNextItemIndex(this.focusedItemInfo.index):this.findFirstFocusedItemIndex();this.changeFocusedItemIndex(e,t),e.preventDefault()},onArrowUpKey:function(e){if(e.altKey){if(this.focusedItemInfo.index!==-1){var t=this.visibleItems[this.focusedItemInfo.index],r=this.isProccessedItemGroup(t);!r&&this.onItemChange({originalEvent:e,processedItem:t})}this.popup&&this.hide(e,!0),e.preventDefault()}else{var s=this.focusedItemInfo.index!==-1?this.findPrevItemIndex(this.focusedItemInfo.index):this.findLastFocusedItemIndex();this.changeFocusedItemIndex(e,s),e.preventDefault()}},onArrowLeftKey:function(e){var t=this,r=this.visibleItems[this.focusedItemInfo.index],s=this.activeItemPath.find(function(p){return p.key===r.parentKey}),i=R(r.parent);i||(this.focusedItemInfo={index:-1,parentKey:s?s.parentKey:""},this.searchValue="",this.onArrowDownKey(e)),this.activeItemPath=this.activeItemPath.filter(function(p){return p.parentKey!==t.focusedItemInfo.parentKey}),e.preventDefault()},onArrowRightKey:function(e){var t=this.visibleItems[this.focusedItemInfo.index],r=this.isProccessedItemGroup(t);r&&(this.onItemChange({originalEvent:e,processedItem:t}),this.focusedItemInfo={index:-1,parentKey:t.key},this.searchValue="",this.onArrowDownKey(e)),e.preventDefault()},onHomeKey:function(e){this.changeFocusedItemIndex(e,this.findFirstItemIndex()),e.preventDefault()},onEndKey:function(e){this.changeFocusedItemIndex(e,this.findLastItemIndex()),e.preventDefault()},onEnterKey:function(e){if(this.focusedItemInfo.index!==-1){var t=D(this.menubar,'li[id="'.concat("".concat(this.focusedItemId),'"]')),r=t&&D(t,'[data-pc-section="itemlink"]');if(r?r.click():t&&t.click(),!this.popup){var s=this.visibleItems[this.focusedItemInfo.index],i=this.isProccessedItemGroup(s);!i&&(this.focusedItemInfo.index=this.findFirstFocusedItemIndex())}}e.preventDefault()},onSpaceKey:function(e){this.onEnterKey(e)},onEscapeKey:function(e){if(this.popup||this.focusedItemInfo.level!==0){var t=this.focusedItemInfo;this.hide(e,!1),this.focusedItemInfo={index:Number(t.parentKey.split("_")[0]),level:0,parentKey:""},this.popup&&C(this.target)}e.preventDefault()},onTabKey:function(e){if(this.focusedItemInfo.index!==-1){var t=this.visibleItems[this.focusedItemInfo.index],r=this.isProccessedItemGroup(t);!r&&this.onItemChange({originalEvent:e,processedItem:t})}this.hide()},onEnter:function(e){this.autoZIndex&&Y.set("menu",e,this.baseZIndex+this.$primevue.config.zIndex.menu),Se(e,{position:"absolute",top:"0",left:"0"}),this.alignOverlay(),C(this.menubar),this.scrollInView()},onAfterEnter:function(){this.bindOutsideClickListener(),this.bindScrollListener(),this.bindResizeListener(),this.$emit("show")},onLeave:function(){this.unbindOutsideClickListener(),this.unbindScrollListener(),this.unbindResizeListener(),this.$emit("hide"),this.container=null,this.dirty=!1},onAfterLeave:function(e){this.autoZIndex&&Y.clear(e)},alignOverlay:function(){Pe(this.container,this.target);var e=G(this.target);e>G(this.container)&&(this.container.style.minWidth=G(this.target)+"px")},bindOutsideClickListener:function(){var e=this;this.outsideClickListener||(this.outsideClickListener=function(t){var r=e.container&&!e.container.contains(t.target),s=e.popup?!(e.target&&(e.target===t.target||e.target.contains(t.target))):!0;r&&s&&e.hide()},document.addEventListener("click",this.outsideClickListener))},unbindOutsideClickListener:function(){this.outsideClickListener&&(document.removeEventListener("click",this.outsideClickListener),this.outsideClickListener=null)},bindScrollListener:function(){var e=this;this.scrollHandler||(this.scrollHandler=new ze(this.target,function(t){e.hide(t,!0)})),this.scrollHandler.bindScrollListener()},unbindScrollListener:function(){this.scrollHandler&&this.scrollHandler.unbindScrollListener()},bindResizeListener:function(){var e=this;this.resizeListener||(this.resizeListener=function(t){Le()||e.hide(t,!0)},window.addEventListener("resize",this.resizeListener))},unbindResizeListener:function(){this.resizeListener&&(window.removeEventListener("resize",this.resizeListener),this.resizeListener=null)},bindMatchMediaListener:function(){var e=this;if(!this.matchMediaListener){var t=matchMedia("(max-width: ".concat(this.breakpoint,")"));this.query=t,this.queryMatches=t.matches,this.matchMediaListener=function(){e.queryMatches=t.matches},this.query.addEventListener("change",this.matchMediaListener)}},unbindMatchMediaListener:function(){this.matchMediaListener&&(this.query.removeEventListener("change",this.matchMediaListener),this.matchMediaListener=null)},isItemMatched:function(e){var t;return this.isValidItem(e)&&((t=this.getProccessedItemLabel(e))===null||t===void 0?void 0:t.toLocaleLowerCase().startsWith(this.searchValue.toLocaleLowerCase()))},isValidItem:function(e){return!!e&&!this.isItemDisabled(e.item)&&!this.isItemSeparator(e.item)&&this.isItemVisible(e.item)},isValidSelectedItem:function(e){return this.isValidItem(e)&&this.isSelected(e)},isSelected:function(e){return this.activeItemPath.some(function(t){return t.key===e.key})},findFirstItemIndex:function(){var e=this;return this.visibleItems.findIndex(function(t){return e.isValidItem(t)})},findLastItemIndex:function(){var e=this;return X(this.visibleItems,function(t){return e.isValidItem(t)})},findNextItemIndex:function(e){var t=this,r=e<this.visibleItems.length-1?this.visibleItems.slice(e+1).findIndex(function(s){return t.isValidItem(s)}):-1;return r>-1?r+e+1:e},findPrevItemIndex:function(e){var t=this,r=e>0?X(this.visibleItems.slice(0,e),function(s){return t.isValidItem(s)}):-1;return r>-1?r:e},findSelectedItemIndex:function(){var e=this;return this.visibleItems.findIndex(function(t){return e.isValidSelectedItem(t)})},findFirstFocusedItemIndex:function(){var e=this.findSelectedItemIndex();return e<0?this.findFirstItemIndex():e},findLastFocusedItemIndex:function(){var e=this.findSelectedItemIndex();return e<0?this.findLastItemIndex():e},searchItems:function(e,t){var r=this;this.searchValue=(this.searchValue||"")+t;var s=-1,i=!1;return this.focusedItemInfo.index!==-1?(s=this.visibleItems.slice(this.focusedItemInfo.index).findIndex(function(p){return r.isItemMatched(p)}),s=s===-1?this.visibleItems.slice(0,this.focusedItemInfo.index).findIndex(function(p){return r.isItemMatched(p)}):s+this.focusedItemInfo.index):s=this.visibleItems.findIndex(function(p){return r.isItemMatched(p)}),s!==-1&&(i=!0),s===-1&&this.focusedItemInfo.index===-1&&(s=this.findFirstFocusedItemIndex()),s!==-1&&this.changeFocusedItemIndex(e,s),this.searchTimeout&&clearTimeout(this.searchTimeout),this.searchTimeout=setTimeout(function(){r.searchValue="",r.searchTimeout=null},500),i},changeFocusedItemIndex:function(e,t){this.focusedItemInfo.index!==t&&(this.focusedItemInfo.index=t,this.scrollInView())},scrollInView:function(){var e=arguments.length>0&&arguments[0]!==void 0?arguments[0]:-1,t=e!==-1?"".concat(this.id,"_").concat(e):this.focusedItemId,r=D(this.menubar,'li[id="'.concat(t,'"]'));r&&r.scrollIntoView&&r.scrollIntoView({block:"nearest",inline:"start"})},createProcessedItems:function(e){var t=this,r=arguments.length>1&&arguments[1]!==void 0?arguments[1]:0,s=arguments.length>2&&arguments[2]!==void 0?arguments[2]:{},i=arguments.length>3&&arguments[3]!==void 0?arguments[3]:"",p=[];return e&&e.forEach(function(d,u){var a=(i!==""?i+"_":"")+u,v={item:d,index:u,level:r,key:a,parent:s,parentKey:i};v.items=t.createProcessedItems(d.items,r+1,v,a),p.push(v)}),p},containerRef:function(e){this.container=e},menubarRef:function(e){this.menubar=e?e.$el:void 0}},computed:{processedItems:function(){return this.createProcessedItems(this.model||[])},visibleItems:function(){var e=this,t=this.activeItemPath.find(function(r){return r.key===e.focusedItemInfo.parentKey});return t?t.items:this.processedItems},focusedItemId:function(){return this.focusedItemInfo.index!==-1?"".concat(this.id).concat(B(this.focusedItemInfo.parentKey)?"_"+this.focusedItemInfo.parentKey:"","_").concat(this.focusedItemInfo.index):null}},components:{TieredMenuSub:le,Portal:Oe}},tt=["id"];function nt(n,e,t,r,s,i){var p=O("TieredMenuSub"),d=O("Portal");return l(),L(d,{appendTo:n.appendTo,disabled:!n.popup},{default:I(function(){return[m(oe,c({name:"p-connected-overlay",onEnter:i.onEnter,onAfterEnter:i.onAfterEnter,onLeave:i.onLeave,onAfterLeave:i.onAfterLeave},n.ptm("transition")),{default:I(function(){return[s.visible?(l(),b("div",c({key:0,ref:i.containerRef,id:s.id,class:n.cx("root"),onClick:e[0]||(e[0]=function(){return i.onOverlayClick&&i.onOverlayClick.apply(i,arguments)})},n.ptmi("root")),[n.$slots.start?(l(),b("div",c({key:0,class:n.cx("start")},n.ptm("start")),[A(n.$slots,"start")],16)):w("",!0),m(p,c({ref:i.menubarRef,id:s.id+"_list",class:n.cx("rootList"),tabindex:n.disabled?-1:n.tabindex,role:"menubar","aria-label":n.ariaLabel,"aria-labelledby":n.ariaLabelledby,"aria-disabled":n.disabled||void 0,"aria-orientation":"vertical","aria-activedescendant":s.focused?i.focusedItemId:void 0,menuId:s.id,focusedItemId:s.focused?i.focusedItemId:void 0,items:i.processedItems,templates:n.$slots,activeItemPath:s.activeItemPath,level:0,visible:s.submenuVisible,pt:n.pt,unstyled:n.unstyled,onFocus:i.onFocus,onBlur:i.onBlur,onKeydown:i.onKeyDown,onItemClick:i.onItemClick,onItemMouseenter:i.onItemMouseEnter,onItemMousemove:i.onItemMouseMove},n.ptm("rootList")),null,16,["id","class","tabindex","aria-label","aria-labelledby","aria-disabled","aria-activedescendant","menuId","focusedItemId","items","templates","activeItemPath","visible","pt","unstyled","onFocus","onBlur","onKeydown","onItemClick","onItemMouseenter","onItemMousemove"]),n.$slots.end?(l(),b("div",c({key:1,class:n.cx("end")},n.ptm("end")),[A(n.$slots,"end")],16)):w("",!0)],16,tt)):w("",!0)]}),_:3},16,["onEnter","onAfterEnter","onLeave","onAfterLeave"])]}),_:3},8,["appendTo","disabled"])}de.render=nt;var it=function(e){var t=e.dt;return`
.p-splitbutton {
    display: inline-flex;
    position: relative;
    border-radius: `.concat(t("splitbutton.border.radius"),`;
}

.p-splitbutton-button {
    border-start-end-radius: 0;
    border-end-end-radius: 0;
    border-inline-end: 0 none;
}

.p-splitbutton-button:focus-visible,
.p-splitbutton-dropdown:focus-visible {
    z-index: 1;
}

.p-splitbutton-button:not(:disabled):hover,
.p-splitbutton-button:not(:disabled):active {
    border-inline-end: 0 none;
}

.p-splitbutton-dropdown {
    border-start-start-radius: 0;
    border-end-start-radius: 0;
}

.p-splitbutton .p-menu {
    min-width: 100%;
}

.p-splitbutton-fluid {
    display: flex;
}

.p-splitbutton-rounded .p-splitbutton-dropdown {
    border-start-end-radius: `).concat(t("splitbutton.rounded.border.radius"),`;
    border-end-end-radius: `).concat(t("splitbutton.rounded.border.radius"),`;
}

.p-splitbutton-rounded .p-splitbutton-button {
    border-start-start-radius: `).concat(t("splitbutton.rounded.border.radius"),`;
    border-end-start-radius: `).concat(t("splitbutton.rounded.border.radius"),`;
}

.p-splitbutton-raised {
    box-shadow: `).concat(t("splitbutton.raised.shadow"),`;
}
`)},rt={root:function(e){var t=e.instance,r=e.props;return["p-splitbutton p-component",{"p-splitbutton-raised":r.raised,"p-splitbutton-rounded":r.rounded,"p-splitbutton-fluid":t.hasFluid}]},pcButton:"p-splitbutton-button",pcDropdown:"p-splitbutton-dropdown"},at=J.extend({name:"splitbutton",theme:it,classes:rt}),st={name:"BaseSplitButton",extends:H,props:{label:{type:String,default:null},icon:{type:String,default:null},model:{type:Array,default:null},autoZIndex:{type:Boolean,default:!0},baseZIndex:{type:Number,default:0},appendTo:{type:[String,Object],default:"body"},disabled:{type:Boolean,default:!1},fluid:{type:Boolean,default:null},class:{type:null,default:null},style:{type:null,default:null},buttonProps:{type:null,default:null},menuButtonProps:{type:null,default:null},menuButtonIcon:{type:String,default:void 0},dropdownIcon:{type:String,default:void 0},severity:{type:String,default:null},raised:{type:Boolean,default:!1},rounded:{type:Boolean,default:!1},text:{type:Boolean,default:!1},outlined:{type:Boolean,default:!1},size:{type:String,default:null},plain:{type:Boolean,default:!1}},style:at,provide:function(){return{$pcSplitButton:this,$parentInstance:this}}},ce={name:"SplitButton",extends:st,inheritAttrs:!1,emits:["click"],inject:{$pcFluid:{default:null}},data:function(){return{id:this.$attrs.id,isExpanded:!1}},watch:{"$attrs.id":function(e){this.id=e||$()}},mounted:function(){var e=this;this.id=this.id||$(),this.$watch("$refs.menu.visible",function(t){e.isExpanded=t})},methods:{onDropdownButtonClick:function(e){e&&e.preventDefault(),this.$refs.menu.toggle({currentTarget:this.$el,relatedTarget:this.$refs.button.$el}),this.isExpanded=this.$refs.menu.visible},onDropdownKeydown:function(e){(e.code==="ArrowDown"||e.code==="ArrowUp")&&(this.onDropdownButtonClick(),e.preventDefault())},onDefaultButtonClick:function(e){this.isExpanded&&this.$refs.menu.hide(e),this.$emit("click",e)}},computed:{containerClass:function(){return[this.cx("root"),this.class]},hasFluid:function(){return R(this.fluid)?!!this.$pcFluid:this.fluid}},components:{PVSButton:K,PVSMenu:de,ChevronDownIcon:Re}},ot=["data-p-severity"];function ut(n,e,t,r,s,i){var p=O("PVSButton"),d=O("PVSMenu");return l(),b("div",c({class:i.containerClass,style:n.style},n.ptmi("root"),{"data-p-severity":n.severity}),[m(p,c({type:"button",class:n.cx("pcButton"),label:n.label,disabled:n.disabled,severity:n.severity,text:n.text,icon:n.icon,outlined:n.outlined,size:n.size,fluid:n.fluid,"aria-label":n.label,onClick:i.onDefaultButtonClick},n.buttonProps,{pt:n.ptm("pcButton"),unstyled:n.unstyled}),ee({default:I(function(){return[A(n.$slots,"default")]}),_:2},[n.$slots.icon?{name:"icon",fn:I(function(u){return[A(n.$slots,"icon",{class:F(u.class)},function(){return[g("span",c({class:[n.icon,u.class]},n.ptm("pcButton").icon,{"data-pc-section":"buttonicon"}),null,16)]})]}),key:"0"}:void 0]),1040,["class","label","disabled","severity","text","icon","outlined","size","fluid","aria-label","onClick","pt","unstyled"]),m(p,c({ref:"button",type:"button",class:n.cx("pcDropdown"),disabled:n.disabled,"aria-haspopup":"true","aria-expanded":s.isExpanded,"aria-controls":s.id+"_overlay",onClick:i.onDropdownButtonClick,onKeydown:i.onDropdownKeydown,severity:n.severity,text:n.text,outlined:n.outlined,size:n.size,unstyled:n.unstyled},n.menuButtonProps,{pt:n.ptm("pcDropdown")}),{icon:I(function(u){return[A(n.$slots,n.$slots.dropdownicon?"dropdownicon":"menubuttonicon",{class:F(u.class)},function(){return[(l(),L(_(n.menuButtonIcon||n.dropdownIcon?"span":"ChevronDownIcon"),c({class:[n.dropdownIcon||n.menuButtonIcon,u.class]},n.ptm("pcDropdown").icon,{"data-pc-section":"menubuttonicon"}),null,16,["class"]))]})]}),_:3},16,["class","disabled","aria-expanded","aria-controls","onClick","onKeydown","severity","text","outlined","size","unstyled","pt"]),m(d,{ref:"menu",id:s.id+"_overlay",model:n.model,popup:!0,autoZIndex:n.autoZIndex,baseZIndex:n.baseZIndex,appendTo:n.appendTo,unstyled:n.unstyled,pt:n.ptm("pcMenu")},ee({_:2},[n.$slots.menuitemicon?{name:"itemicon",fn:I(function(u){return[A(n.$slots,"menuitemicon",{item:u.item,class:F(u.class)})]}),key:"0"}:void 0,n.$slots.item?{name:"item",fn:I(function(u){return[A(n.$slots,"item",{item:u.item,hasSubmenu:u.hasSubmenu,label:u.label,props:u.props})]}),key:"1"}:void 0]),1032,["id","model","autoZIndex","baseZIndex","appendTo","unstyled","pt"])],16,ot)}ce.render=ut;var lt=function(e){var t=e.dt;return`
.p-tabmenu {
    overflow-x: auto;
}

.p-tabmenu-tablist {
    display: flex;
    margin: 0;
    padding: 0;
    list-style-type: none;
    background: `.concat(t("tabmenu.tablist.background"),`;
    border-style: solid;
    border-color: `).concat(t("tabmenu.tablist.border.color"),`;
    border-width: `).concat(t("tabmenu.tablist.border.width"),`;
    position: relative;
}

.p-tabmenu-item-link {
    cursor: pointer;
    user-select: none;
    display: flex;
    align-items: center;
    text-decoration: none;
    position: relative;
    overflow: hidden;
    background: `).concat(t("tabmenu.item.background"),`;
    border-style: solid;
    border-width: `).concat(t("tabmenu.item.border.width"),`;
    border-color: `).concat(t("tabmenu.item.border.color"),`;
    color: `).concat(t("tabmenu.item.color"),`;
    padding: `).concat(t("tabmenu.item.padding"),`;
    font-weight: `).concat(t("tabmenu.item.font.weight"),`;
    transition: background `).concat(t("tabmenu.transition.duration"),", border-color ").concat(t("tabmenu.transition.duration"),", color ").concat(t("tabmenu.transition.duration"),", outline-color ").concat(t("tabmenu.transition.duration"),", box-shadow ").concat(t("tabmenu.transition.duration"),`;
    margin: `).concat(t("tabmenu.item.margin"),`;
    outline-color: transparent;
    gap: `).concat(t("tabmenu.item.gap"),`;
}

.p-tabmenu-item-link:focus-visible {
    z-index: 1;
    box-shadow: `).concat(t("tabmenu.item.focus.ring.shadow"),`;
    outline: `).concat(t("tabmenu.item.focus.ring.width")," ").concat(t("tabmenu.item.focus.ring.style")," ").concat(t("tabmenu.item.focus.ring.color"),`;
    outline-offset: `).concat(t("tabmenu.item.focus.ring.offset"),`;
}

.p-tabmenu-item-icon {
    color: `).concat(t("tabmenu.item.icon.color"),`;
    transition: background `).concat(t("tabmenu.transition.duration"),", border-color ").concat(t("tabmenu.transition.duration"),", color ").concat(t("tabmenu.transition.duration"),", outline-color ").concat(t("tabmenu.transition.duration"),", box-shadow ").concat(t("tabmenu.transition.duration"),`;
}

.p-tabmenu-item-label {
    line-height: 1;
}

.p-tabmenu-item:not(.p-tabmenu-item-active):not(.p-disabled):hover .p-tabmenu-item-link {
    background: `).concat(t("tabmenu.item.hover.background"),`;
    border-color: `).concat(t("tabmenu.item.hover.border.color"),`;
    color: `).concat(t("tabmenu.item.hover.color"),`;
}

.p-tabmenu-item:not(.p-tabmenu-item-active):not(.p-disabled):hover .p-tabmenu-item-icon {
    color: `).concat(t("tabmenu.item.icon.hover.color"),`;
}

.p-tabmenu-item-active .p-tabmenu-item-link {
    background: `).concat(t("tabmenu.item.active.background"),`;
    border-color: `).concat(t("tabmenu.item.active.border.color"),`;
    color: `).concat(t("tabmenu.item.active.color"),`;
}

.p-tabmenu-item-active .p-tabmenu-item-icon {
    color: `).concat(t("tabmenu.item.icon.active.color"),`;
}

.p-tabmenu-active-bar {
    z-index: 1;
    display: block;
    position: absolute;
    bottom: `).concat(t("tabmenu.active.bar.bottom"),`;
    height: `).concat(t("tabmenu.active.bar.height"),`;
    background: `).concat(t("tabmenu.active.bar.background"),`;
    transition: 250ms cubic-bezier(0.35, 0, 0.25, 1);
}

.p-tabmenu::-webkit-scrollbar {
    display: none;
}
`)},dt={root:"p-tabmenu p-component",tablist:"p-tabmenu-tablist",item:function(e){var t=e.instance,r=e.index,s=e.item;return["p-tabmenu-item",{"p-tabmenu-item-active":t.d_activeIndex===r,"p-disabled":t.disabled(s)}]},itemLink:"p-tabmenu-item-link",itemIcon:"p-tabmenu-item-icon",itemLabel:"p-tabmenu-item-label",activeBar:"p-tabmenu-active-bar"},ct=J.extend({name:"tabmenu",theme:lt,classes:dt}),mt={name:"BaseTabMenu",extends:H,props:{model:{type:Array,default:null},activeIndex:{type:Number,default:0},ariaLabelledby:{type:String,default:null},ariaLabel:{type:String,default:null}},style:ct,provide:function(){return{$pcTabMenu:this,$parentInstance:this}}},me={name:"TabMenu",extends:mt,inheritAttrs:!1,emits:["update:activeIndex","tab-change"],data:function(){return{d_activeIndex:this.activeIndex}},watch:{activeIndex:{flush:"post",handler:function(e){this.d_activeIndex=e,this.updateInkBar()}}},mounted:function(){var e=this;this.$nextTick(function(){e.updateInkBar()});var t=this.findActiveItem();t&&(t.tabIndex="0")},updated:function(){this.updateInkBar()},methods:{getPTOptions:function(e,t,r){return this.ptm(e,{context:{item:t,index:r}})},onItemClick:function(e,t,r){if(this.disabled(t)){e.preventDefault();return}t.command&&t.command({originalEvent:e,item:t}),r!==this.d_activeIndex&&(this.d_activeIndex=r,this.$emit("update:activeIndex",this.d_activeIndex)),this.$emit("tab-change",{originalEvent:e,index:r})},onKeydownItem:function(e,t,r){switch(e.code){case"ArrowRight":{this.navigateToNextItem(e.target),e.preventDefault();break}case"ArrowLeft":{this.navigateToPrevItem(e.target),e.preventDefault();break}case"Home":{this.navigateToFirstItem(e.target),e.preventDefault();break}case"End":{this.navigateToLastItem(e.target),e.preventDefault();break}case"Space":case"NumpadEnter":case"Enter":{this.onItemClick(e,t,r),e.preventDefault();break}case"Tab":{this.onTabKey();break}}},navigateToNextItem:function(e){var t=this.findNextItem(e);t&&this.setFocusToMenuitem(e,t)},navigateToPrevItem:function(e){var t=this.findPrevItem(e);t&&this.setFocusToMenuitem(e,t)},navigateToFirstItem:function(e){var t=this.findFirstItem(e);t&&this.setFocusToMenuitem(e,t)},navigateToLastItem:function(e){var t=this.findLastItem(e);t&&this.setFocusToMenuitem(e,t)},findNextItem:function(e){var t=e.parentElement.nextElementSibling;return t?q(t,"data-p-disabled")===!0?this.findNextItem(t.children[0]):t.children[0]:null},findPrevItem:function(e){var t=e.parentElement.previousElementSibling;return t?q(t,"data-p-disabled")===!0?this.findPrevItem(t.children[0]):t.children[0]:null},findFirstItem:function(){var e=D(this.$refs.nav,'[data-pc-section="item"][data-p-disabled="false"]');return e?e.children[0]:null},findLastItem:function(){var e=Ke(this.$refs.nav,'[data-pc-section="item"][data-p-disabled="false"]');return e?e[e.length-1].children[0]:null},findActiveItem:function(){var e=D(this.$refs.nav,'[data-pc-section="item"][data-p-disabled="false"][data-p-active="true"]');return e?e.children[0]:null},setFocusToMenuitem:function(e,t){e.tabIndex="-1",t.tabIndex="0",t.focus()},onTabKey:function(){var e=D(this.$refs.nav,'[data-pc-section="item"][data-p-disabled="false"][data-p-active="true"]'),t=D(this.$refs.nav,'[data-pc-section="itemlink"][tabindex="0"]');t!==e.children[0]&&(e&&(e.children[0].tabIndex="0"),t.tabIndex="-1")},visible:function(e){return typeof e.visible=="function"?e.visible():e.visible!==!1},disabled:function(e){return typeof e.disabled=="function"?e.disabled():e.disabled===!0},label:function(e){return typeof e.label=="function"?e.label():e.label},updateInkBar:function(){for(var e=this.$refs.nav.children,t=!1,r=0;r<e.length;r++){var s=e[r];q(s,"data-p-active")&&(this.$refs.inkbar.style.width=Ce(s)+"px",this.$refs.inkbar.style.left=te(s).left-te(this.$refs.nav).left+"px",t=!0)}t||(this.$refs.inkbar.style.width="0px",this.$refs.inkbar.style.left="0px")},getMenuItemProps:function(e,t){var r=this;return{action:c({class:this.cx("itemLink"),tabindex:-1,onClick:function(i){return r.onItemClick(i,e,t)},onKeyDown:function(i){return r.onKeydownItem(i,e,t)}},this.getPTOptions("itemLink",e,t)),icon:c({class:[this.cx("itemIcon"),e.icon]},this.getPTOptions("itemIcon",e,t)),label:c({class:this.cx("itemLabel")},this.getPTOptions("itemLabel",e,t))}}},directives:{ripple:ue}},ft=["aria-labelledby","aria-label"],pt=["onClick","onKeydown","data-p-active","data-p-disabled"],ht=["href","target","aria-label","aria-disabled"];function bt(n,e,t,r,s,i){var p=se("ripple");return l(),b("div",c({class:n.cx("root")},n.ptmi("root")),[g("ul",c({ref:"nav",class:n.cx("tablist"),role:"menubar","aria-labelledby":n.ariaLabelledby,"aria-label":n.ariaLabel},n.ptm("tablist")),[(l(!0),b(V,null,Q(n.model,function(d,u){return l(),b(V,{key:i.label(d)+"_"+u.toString()},[i.visible(d)?(l(),b("li",c({key:0,ref_for:!0,ref:"tab",class:[n.cx("item",{item:d,index:u}),d.class],role:"presentation",onClick:function(v){return i.onItemClick(v,d,u)},onKeydown:function(v){return i.onKeydownItem(v,d,u)}},i.getPTOptions("item",d,u),{"data-p-active":s.d_activeIndex===u,"data-p-disabled":i.disabled(d)}),[n.$slots.item?(l(),L(_(n.$slots.item),{key:1,item:d,index:u,active:u===s.d_activeIndex,label:i.label(d),props:i.getMenuItemProps(d,u)},null,8,["item","index","active","label","props"])):E((l(),b("a",c({key:0,ref_for:!0,ref:"tabLink",role:"menuitem",href:d.url,class:n.cx("itemLink"),target:d.target,"aria-label":i.label(d),"aria-disabled":i.disabled(d),tabindex:-1},i.getPTOptions("itemLink",d,u)),[n.$slots.itemicon?(l(),L(_(n.$slots.itemicon),{key:0,item:d,class:F(n.cx("itemIcon"))},null,8,["item","class"])):d.icon?(l(),b("span",c({key:1,class:[n.cx("itemIcon"),d.icon],ref_for:!0},i.getPTOptions("itemIcon",d,u)),null,16)):w("",!0),g("span",c({class:n.cx("itemLabel"),ref_for:!0},i.getPTOptions("itemLabel",d,u)),S(i.label(d)),17)],16,ht)),[[p]])],16,pt)):w("",!0)],64)}),128)),g("li",c({ref:"inkbar",role:"none",class:n.cx("activeBar")},n.ptm("activeBar")),null,16)],16,ft)],16)}me.render=bt;const vt={class:"space-y-6"},It={class:"card"},gt={class:"flex flex-wrap gap-2"},yt={class:"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4"},kt={class:"col-span-1"},xt={class:"p-input-icon-left w-full"},wt={class:"col-span-1"},Lt={class:"col-span-1"},Pt={class:"col-span-1 flex gap-2"},St=["src"],Tt={key:1,class:"text-gray-400 text-xs"},Mt={class:"font-bold"},Ct={class:"text-xs text-gray-500"},Kt={key:0,class:"flex gap-1"},Et={key:1,class:"flex gap-1"},At={key:0,class:"mt-4 flex flex-wrap justify-between items-center"},Dt={class:"text-sm text-gray-500"},Vt={class:"flex gap-1"},Ft=["innerHTML"],Bt=["onClick","innerHTML"],cn={__name:"Index",props:{products:{type:Object,required:!0},filters:{type:Object,default:()=>({})},categories:{type:Array,default:()=>[]},brands:{type:Array,default:()=>[]}},setup(n){const e=n,t=ne(()=>e.products||{data:[],links:[]}),r=j({search:e.filters.search||"",category_id:e.filters.category_id?Number(e.filters.category_id):null,brand_id:e.filters.brand_id?Number(e.filters.brand_id):null,status:e.filters.status||null,trashed:e.filters.trashed||null,per_page:e.filters.per_page?Number(e.filters.per_page):10}),s=[{label:"Active",value:"active"},{label:"Inactive",value:"inactive"}],i=[{label:"10",value:10},{label:"25",value:25},{label:"50",value:50},{label:"100",value:100}],p=j(e.filters.trashed==="only"?1:0),d=[{label:"All Products",icon:"pi pi-list"},{label:"Trash",icon:"pi pi-trash"}];ie(p,f=>{f===0?r.value.trashed=null:r.value.trashed="only"});const u=Ae(()=>{P.get(route("products.index"),r.value,{preserveState:!0,preserveScroll:!0,replace:!0})},300);ie(()=>r.value,()=>u(),{deep:!0});const a=j([]),v=ne(()=>{const f=[];return r.value.trashed==="only"?(f.push({label:"Restore Selected",icon:"pi pi-refresh",command:()=>x()}),f.push({label:"Force Delete Selected",icon:"pi pi-times",class:"p-button-danger",command:()=>fe()})):f.push({label:"Move to Trash",icon:"pi pi-trash",command:()=>y()}),f}),y=()=>{a.value.length&&confirm(`Move ${a.value.length} products to trash?`)&&P.delete(route("products.bulk-delete"),{data:{ids:a.value.map(f=>f.id)},onSuccess:()=>a.value=[]})},x=()=>{a.value.length&&confirm(`Restore ${a.value.length} products?`)&&P.post(route("products.bulk-restore"),{ids:a.value.map(f=>f.id)},{onSuccess:()=>a.value=[]})},fe=()=>{a.value.length&&confirm(`PERMANENTLY delete ${a.value.length} products? THIS CANNOT BE UNDONE.`)&&P.post(route("products.bulk-force-delete"),{ids:a.value.map(f=>f.id)},{onSuccess:()=>a.value=[]})},pe=f=>{f&&P.visit(f,{preserveScroll:!0,preserveState:!0})},he=()=>P.visit(route("products.create")),be=f=>P.visit(route("products.edit",f.id)),ve=f=>{confirm("Delete this product?")&&P.delete(route("products.destroy",f.id),{preserveScroll:!0,preserveState:!0})},Ie=f=>{confirm("Restore this product?")&&P.post(route("products.restore",f.id),{},{preserveScroll:!0,preserveState:!0})},ge=f=>{confirm("PERMANENTLY delete this product?")&&P.post(route("products.bulk-force-delete"),{ids:[f.id]})},U=f=>f.total_stock!==void 0&&f.total_stock!==null?Number(f.total_stock):Array.isArray(f.stocks)?f.stocks.reduce((k,Z)=>k+Number(Z.quantity||0),0):0;return(f,k)=>{const Z=we,ye=xe,z=ke;return l(),L($e,null,{default:I(()=>[g("div",vt,[g("div",It,[m(h(De),{class:"mb-4 flex flex-col md:flex-row gap-4"},{start:I(()=>[g("div",gt,[m(h(K),{label:"New Product",icon:"pi pi-plus",class:"mr-2",onClick:he}),a.value.length?(l(),L(h(ce),{key:0,label:a.value.length+" Selected",model:v.value,severity:"danger"},null,8,["label","model"])):w("",!0)])]),end:I(()=>[m(h(me),{model:d,activeIndex:p.value,"onUpdate:activeIndex":k[0]||(k[0]=o=>p.value=o),class:"w-full md:w-auto"},null,8,["activeIndex"])]),_:1}),g("div",yt,[g("div",kt,[g("span",xt,[m(ye,null,{default:I(()=>[m(Z,{class:"pi pi-search"}),m(h(_e),{type:"search",modelValue:r.value.search,"onUpdate:modelValue":k[1]||(k[1]=o=>r.value.search=o),placeholder:"Search...",class:"w-full"},null,8,["modelValue"])]),_:1})])]),g("div",wt,[m(h(N),{modelValue:r.value.category_id,"onUpdate:modelValue":k[2]||(k[2]=o=>r.value.category_id=o),options:n.categories,optionLabel:"name",optionValue:"id",placeholder:"Category",showClear:"",class:"w-full"},null,8,["modelValue","options"])]),g("div",Lt,[m(h(N),{modelValue:r.value.brand_id,"onUpdate:modelValue":k[3]||(k[3]=o=>r.value.brand_id=o),options:n.brands,optionLabel:"name",optionValue:"id",placeholder:"Brand",showClear:"",class:"w-full"},null,8,["modelValue","options"])]),g("div",Pt,[m(h(N),{modelValue:r.value.status,"onUpdate:modelValue":k[4]||(k[4]=o=>r.value.status=o),options:s,optionLabel:"label",optionValue:"value",placeholder:"Status",showClear:"",class:"w-full"},null,8,["modelValue"]),m(h(N),{modelValue:r.value.per_page,"onUpdate:modelValue":k[5]||(k[5]=o=>r.value.per_page=o),options:i,optionLabel:"label",optionValue:"value",placeholder:"Per Page",class:"w-24"},null,8,["modelValue"])])]),m(h(Be),{value:t.value.data,selection:a.value,"onUpdate:selection":k[6]||(k[6]=o=>a.value=o),dataKey:"id",paginator:!1,class:"w-full",stripedRows:"",responsiveLayout:"scroll"},{default:I(()=>[m(h(M),{selectionMode:"multiple",headerStyle:"width: 3rem"}),m(h(M),{header:"Image"},{body:I(({data:o})=>[o.thumbnail?(l(),b("img",{key:0,src:h(Ve)(o.thumbnail),alt:"Thumbnail",class:"w-12 h-12 object-cover rounded shadow-sm"},null,8,St)):(l(),b("span",Tt,"No Image"))]),_:1}),m(h(M),{field:"name",header:"Product",sortable:""},{body:I(({data:o})=>[g("div",Mt,S(o.name),1),g("div",Ct,S(o.sku),1)]),_:1}),m(h(M),{header:"Type"},{body:I(({data:o})=>[m(h(re),{severity:o.type==="variable"?"info":"secondary",value:o.type},null,8,["severity","value"])]),_:1}),m(h(M),{header:"Category"},{body:I(({data:o})=>{var T;return[W(S(((T=o.category)==null?void 0:T.name)||"-"),1)]}),_:1}),m(h(M),{field:"base_price",header:"Price",sortable:""},{body:I(({data:o})=>[W(S(o.base_price),1)]),_:1}),m(h(M),{header:"Stock",sortable:"",field:"total_stock"},{body:I(({data:o})=>[g("span",{class:F(["font-bold",{"text-red-500":U(o)<=0,"text-green-600":U(o)>0}])},S(U(o)),3)]),_:1}),m(h(M),{header:"Status"},{body:I(({data:o})=>[m(h(re),{severity:o.is_active?"success":"danger"},{default:I(()=>[W(S(o.is_active?"Active":"Inactive"),1)]),_:2},1032,["severity"])]),_:1}),m(h(M),{header:"Actions",style:{"min-width":"140px"}},{body:I(({data:o})=>[o.deleted_at?(l(),b("div",Et,[E(m(h(K),{icon:"pi pi-refresh",class:"p-button-text p-button-sm p-button-success",onClick:T=>Ie(o)},null,8,["onClick"]),[[z,"Restore"]]),E(m(h(K),{icon:"pi pi-times",class:"p-button-text p-button-sm p-button-danger",onClick:T=>ge(o)},null,8,["onClick"]),[[z,"Permanently Delete"]])])):(l(),b("div",Kt,[E(m(h(K),{icon:"pi pi-pencil",class:"p-button-text p-button-sm p-button-info",onClick:T=>be(o)},null,8,["onClick"]),[[z,"Edit"]]),E(m(h(K),{icon:"pi pi-sort-alt",class:"p-button-text p-button-sm p-button-help",onClick:T=>h(P).visit(f.route("admin.stock.move.form",o.id))},null,8,["onClick"]),[[z,"Stock Move"]]),E(m(h(K),{icon:"pi pi-trash",class:"p-button-text p-button-danger p-button-sm",onClick:T=>ve(o)},null,8,["onClick"]),[[z,"Trash"]])]))]),_:1})]),_:1},8,["value","selection"]),t.value.links&&t.value.links.length>3?(l(),b("div",At,[g("span",Dt," Showing "+S(t.value.from)+" to "+S(t.value.to)+" of "+S(t.value.total)+" entries ",1),g("div",Vt,[(l(!0),b(V,null,Q(t.value.links,(o,T)=>(l(),b(V,{key:T},[o.url===null?(l(),b("div",{key:0,class:"px-3 py-1 border rounded text-sm text-gray-400 cursor-not-allowed",innerHTML:o.label},null,8,Ft)):(l(),b("button",{key:1,class:F(["px-3 py-1 border rounded text-sm hover:bg-gray-100",{"bg-blue-500 text-white font-bold hover:bg-blue-600":o.active}]),onClick:Ee(_t=>pe(o.url),["prevent"]),innerHTML:o.label},null,10,Bt))],64))),128))])])):w("",!0)])])]),_:1})}}};export{cn as default};
