import{B as le,i as x,x as se,aa as oe,h as re,k as H,l as z,Y as ae,n as ce,p as ue,q as de,s as pe,v as he,j as F,Z as fe,$ as me,ac as be,y as K,z as I,A as ge,c as u,o as r,b as y,D as m,d as g,a as C,m as o,G as T,e as A,t as v,H as j,w as S,F as k,g as O,I as M,T as ye,a1 as ve,J as Oe,K as Ie}from"./app-DsieonZu.js";import{s as Se,C as ke,Z as U,O as we,U as q}from"./index-DQNAyf7j.js";import{s as Le}from"./index-C1VHlqZY.js";import{s as xe}from"./index-BCjHW8ac.js";import{b as Fe,d as Ce,c as Ve,a as Ke}from"./index-6gGX_01L.js";import{s as Te,R as Me}from"./index-B1YzyBAC.js";import{s as Ae}from"./index-CfjDihnE.js";import{s as Ee}from"./index-BZldi7SO.js";import{s as De}from"./index-Ckz4uwLP.js";import{s as Pe,a as Re}from"./index-ctHPnxG6.js";var He=function(e){var i=e.dt;return`
.p-multiselect {
    display: inline-flex;
    cursor: pointer;
    position: relative;
    user-select: none;
    background: `.concat(i("multiselect.background"),`;
    border: 1px solid `).concat(i("multiselect.border.color"),`;
    transition: background `).concat(i("multiselect.transition.duration"),", color ").concat(i("multiselect.transition.duration"),", border-color ").concat(i("multiselect.transition.duration"),", outline-color ").concat(i("multiselect.transition.duration"),", box-shadow ").concat(i("multiselect.transition.duration"),`;
    border-radius: `).concat(i("multiselect.border.radius"),`;
    outline-color: transparent;
    box-shadow: `).concat(i("multiselect.shadow"),`;
}

.p-multiselect:not(.p-disabled):hover {
    border-color: `).concat(i("multiselect.hover.border.color"),`;
}

.p-multiselect:not(.p-disabled).p-focus {
    border-color: `).concat(i("multiselect.focus.border.color"),`;
    box-shadow: `).concat(i("multiselect.focus.ring.shadow"),`;
    outline: `).concat(i("multiselect.focus.ring.width")," ").concat(i("multiselect.focus.ring.style")," ").concat(i("multiselect.focus.ring.color"),`;
    outline-offset: `).concat(i("multiselect.focus.ring.offset"),`;
}

.p-multiselect.p-variant-filled {
    background: `).concat(i("multiselect.filled.background"),`;
}

.p-multiselect.p-variant-filled:not(.p-disabled):hover {
    background: `).concat(i("multiselect.filled.hover.background"),`;
}

.p-multiselect.p-variant-filled.p-focus {
    background: `).concat(i("multiselect.filled.focus.background"),`;
}

.p-multiselect.p-invalid {
    border-color: `).concat(i("multiselect.invalid.border.color"),`;
}

.p-multiselect.p-disabled {
    opacity: 1;
    background: `).concat(i("multiselect.disabled.background"),`;
}

.p-multiselect-dropdown {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: transparent;
    color: `).concat(i("multiselect.dropdown.color"),`;
    width: `).concat(i("multiselect.dropdown.width"),`;
    border-start-end-radius: `).concat(i("multiselect.border.radius"),`;
    border-end-end-radius: `).concat(i("multiselect.border.radius"),`;
}

.p-multiselect-clear-icon {
    position: absolute;
    top: 50%;
    margin-top: -0.5rem;
    color: `).concat(i("multiselect.clear.icon.color"),`;
    inset-inline-end: `).concat(i("multiselect.dropdown.width"),`;
}

.p-multiselect-label-container {
    overflow: hidden;
    flex: 1 1 auto;
    cursor: pointer;
}

.p-multiselect-label {
    display: flex;
    align-items: center;
    gap: calc(`).concat(i("multiselect.padding.y"),` / 2);
    white-space: nowrap;
    cursor: pointer;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: `).concat(i("multiselect.padding.y")," ").concat(i("multiselect.padding.x"),`;
    color: `).concat(i("multiselect.color"),`;
}

.p-multiselect-label.p-placeholder {
    color: `).concat(i("multiselect.placeholder.color"),`;
}

.p-multiselect.p-invalid .p-multiselect-label.p-placeholder {
    color: `).concat(i("multiselect.invalid.placeholder.color"),`;
}

.p-multiselect.p-disabled .p-multiselect-label {
    color: `).concat(i("multiselect.disabled.color"),`;
}

.p-multiselect-label-empty {
    overflow: hidden;
    visibility: hidden;
}

.p-multiselect .p-multiselect-overlay {
    min-width: 100%;
}

.p-multiselect-overlay {
    position: absolute;
    top: 0;
    left: 0;
    background: `).concat(i("multiselect.overlay.background"),`;
    color: `).concat(i("multiselect.overlay.color"),`;
    border: 1px solid `).concat(i("multiselect.overlay.border.color"),`;
    border-radius: `).concat(i("multiselect.overlay.border.radius"),`;
    box-shadow: `).concat(i("multiselect.overlay.shadow"),`;
}

.p-multiselect-header {
    display: flex;
    align-items: center;
    padding: `).concat(i("multiselect.list.header.padding"),`;
}

.p-multiselect-header .p-checkbox {
    margin-inline-end: `).concat(i("multiselect.option.gap"),`;
}

.p-multiselect-filter-container {
    flex: 1 1 auto;
}

.p-multiselect-filter {
    width: 100%;
}

.p-multiselect-list-container {
    overflow: auto;
}

.p-multiselect-list {
    margin: 0;
    padding: 0;
    list-style-type: none;
    padding: `).concat(i("multiselect.list.padding"),`;
    display: flex;
    flex-direction: column;
    gap: `).concat(i("multiselect.list.gap"),`;
}

.p-multiselect-option {
    cursor: pointer;
    font-weight: normal;
    white-space: nowrap;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: `).concat(i("multiselect.option.gap"),`;
    padding: `).concat(i("multiselect.option.padding"),`;
    border: 0 none;
    color: `).concat(i("multiselect.option.color"),`;
    background: transparent;
    transition: background `).concat(i("multiselect.transition.duration"),", color ").concat(i("multiselect.transition.duration"),", border-color ").concat(i("multiselect.transition.duration"),", box-shadow ").concat(i("multiselect.transition.duration"),", outline-color ").concat(i("multiselect.transition.duration"),`;
    border-radius: `).concat(i("multiselect.option.border.radius"),`;
}

.p-multiselect-option:not(.p-multiselect-option-selected):not(.p-disabled).p-focus {
    background: `).concat(i("multiselect.option.focus.background"),`;
    color: `).concat(i("multiselect.option.focus.color"),`;
}

.p-multiselect-option.p-multiselect-option-selected {
    background: `).concat(i("multiselect.option.selected.background"),`;
    color: `).concat(i("multiselect.option.selected.color"),`;
}

.p-multiselect-option.p-multiselect-option-selected.p-focus {
    background: `).concat(i("multiselect.option.selected.focus.background"),`;
    color: `).concat(i("multiselect.option.selected.focus.color"),`;
}

.p-multiselect-option-group {
    cursor: auto;
    margin: 0;
    padding: `).concat(i("multiselect.option.group.padding"),`;
    background: `).concat(i("multiselect.option.group.background"),`;
    color: `).concat(i("multiselect.option.group.color"),`;
    font-weight: `).concat(i("multiselect.option.group.font.weight"),`;
}

.p-multiselect-empty-message {
    padding: `).concat(i("multiselect.empty.message.padding"),`;
}

.p-multiselect-label .p-chip {
    padding-block-start: calc(`).concat(i("multiselect.padding.y"),` / 2);
    padding-block-end: calc(`).concat(i("multiselect.padding.y"),` / 2);
    border-radius: `).concat(i("multiselect.chip.border.radius"),`;
}

.p-multiselect-label:has(.p-chip) {
    padding: calc(`).concat(i("multiselect.padding.y")," / 2) calc(").concat(i("multiselect.padding.x"),` / 2);
}

.p-multiselect-fluid {
    display: flex;
    width: 100%;
}

.p-multiselect-sm .p-multiselect-label {
    font-size: `).concat(i("multiselect.sm.font.size"),`;
    padding-block: `).concat(i("multiselect.sm.padding.y"),`;
    padding-inline: `).concat(i("multiselect.sm.padding.x"),`;
}

.p-multiselect-sm .p-multiselect-dropdown .p-icon {
    font-size: `).concat(i("multiselect.sm.font.size"),`;
    width: `).concat(i("multiselect.sm.font.size"),`;
    height: `).concat(i("multiselect.sm.font.size"),`;
}

.p-multiselect-lg .p-multiselect-label {
    font-size: `).concat(i("multiselect.lg.font.size"),`;
    padding-block: `).concat(i("multiselect.lg.padding.y"),`;
    padding-inline: `).concat(i("multiselect.lg.padding.x"),`;
}

.p-multiselect-lg .p-multiselect-dropdown .p-icon {
    font-size: `).concat(i("multiselect.lg.font.size"),`;
    width: `).concat(i("multiselect.lg.font.size"),`;
    height: `).concat(i("multiselect.lg.font.size"),`;
}
`)},ze={root:function(e){var i=e.props;return{position:i.appendTo==="self"?"relative":void 0}}},Be={root:function(e){var i=e.instance,n=e.props;return["p-multiselect p-component p-inputwrapper",{"p-multiselect-display-chip":n.display==="chip","p-disabled":n.disabled,"p-invalid":i.$invalid,"p-variant-filled":i.$variant==="filled","p-focus":i.focused,"p-inputwrapper-filled":i.$filled,"p-inputwrapper-focus":i.focused||i.overlayVisible,"p-multiselect-open":i.overlayVisible,"p-multiselect-fluid":i.$fluid,"p-multiselect-sm p-inputfield-sm":n.size==="small","p-multiselect-lg p-inputfield-lg":n.size==="large"}]},labelContainer:"p-multiselect-label-container",label:function(e){var i=e.instance,n=e.props;return["p-multiselect-label",{"p-placeholder":i.label===n.placeholder,"p-multiselect-label-empty":!n.placeholder&&(!n.modelValue||n.modelValue.length===0)}]},clearIcon:"p-multiselect-clear-icon",chipItem:"p-multiselect-chip-item",pcChip:"p-multiselect-chip",chipIcon:"p-multiselect-chip-icon",dropdown:"p-multiselect-dropdown",loadingIcon:"p-multiselect-loading-icon",dropdownIcon:"p-multiselect-dropdown-icon",overlay:"p-multiselect-overlay p-component",header:"p-multiselect-header",pcFilterContainer:"p-multiselect-filter-container",pcFilter:"p-multiselect-filter",listContainer:"p-multiselect-list-container",list:"p-multiselect-list",optionGroup:"p-multiselect-option-group",option:function(e){var i=e.instance,n=e.option,s=e.index,l=e.getItemOptions,d=e.props;return["p-multiselect-option",{"p-multiselect-option-selected":i.isSelected(n)&&d.highlightOnSelect,"p-focus":i.focusedOptionIndex===i.getOptionIndex(s,l),"p-disabled":i.isOptionDisabled(n)}]},emptyMessage:"p-multiselect-empty-message"},Ge=le.extend({name:"multiselect",theme:He,classes:Be,inlineStyles:ze}),Ue={name:"BaseMultiSelect",extends:Re,props:{options:Array,optionLabel:null,optionValue:null,optionDisabled:null,optionGroupLabel:null,optionGroupChildren:null,scrollHeight:{type:String,default:"14rem"},placeholder:String,inputId:{type:String,default:null},panelClass:{type:String,default:null},panelStyle:{type:null,default:null},overlayClass:{type:String,default:null},overlayStyle:{type:null,default:null},dataKey:null,showClear:{type:Boolean,default:!1},clearIcon:{type:String,default:void 0},resetFilterOnClear:{type:Boolean,default:!1},filter:Boolean,filterPlaceholder:String,filterLocale:String,filterMatchMode:{type:String,default:"contains"},filterFields:{type:Array,default:null},appendTo:{type:[String,Object],default:"body"},display:{type:String,default:"comma"},selectedItemsLabel:{type:String,default:null},maxSelectedLabels:{type:Number,default:null},selectionLimit:{type:Number,default:null},showToggleAll:{type:Boolean,default:!0},loading:{type:Boolean,default:!1},checkboxIcon:{type:String,default:void 0},dropdownIcon:{type:String,default:void 0},filterIcon:{type:String,default:void 0},loadingIcon:{type:String,default:void 0},removeTokenIcon:{type:String,default:void 0},chipIcon:{type:String,default:void 0},selectAll:{type:Boolean,default:null},resetFilterOnHide:{type:Boolean,default:!1},virtualScrollerOptions:{type:Object,default:null},autoOptionFocus:{type:Boolean,default:!1},autoFilterFocus:{type:Boolean,default:!1},focusOnHover:{type:Boolean,default:!0},highlightOnSelect:{type:Boolean,default:!1},filterMessage:{type:String,default:null},selectionMessage:{type:String,default:null},emptySelectionMessage:{type:String,default:null},emptyFilterMessage:{type:String,default:null},emptyMessage:{type:String,default:null},tabindex:{type:Number,default:0},ariaLabel:{type:String,default:null},ariaLabelledby:{type:String,default:null}},style:Ge,provide:function(){return{$pcMultiSelect:this,$parentInstance:this}}};function E(t){"@babel/helpers - typeof";return E=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(e){return typeof e}:function(e){return e&&typeof Symbol=="function"&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},E(t)}function $(t,e){var i=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter(function(s){return Object.getOwnPropertyDescriptor(t,s).enumerable})),i.push.apply(i,n)}return i}function W(t){for(var e=1;e<arguments.length;e++){var i=arguments[e]!=null?arguments[e]:{};e%2?$(Object(i),!0).forEach(function(n){Y(t,n,i[n])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(i)):$(Object(i)).forEach(function(n){Object.defineProperty(t,n,Object.getOwnPropertyDescriptor(i,n))})}return t}function Y(t,e,i){return(e=Ne(e))in t?Object.defineProperty(t,e,{value:i,enumerable:!0,configurable:!0,writable:!0}):t[e]=i,t}function Ne(t){var e=je(t,"string");return E(e)=="symbol"?e:e+""}function je(t,e){if(E(t)!="object"||!t)return t;var i=t[Symbol.toPrimitive];if(i!==void 0){var n=i.call(t,e);if(E(n)!="object")return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return(e==="string"?String:Number)(t)}function Z(t){return Ze(t)||We(t)||$e(t)||qe()}function qe(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function $e(t,e){if(t){if(typeof t=="string")return N(t,e);var i={}.toString.call(t).slice(8,-1);return i==="Object"&&t.constructor&&(i=t.constructor.name),i==="Map"||i==="Set"?Array.from(t):i==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(i)?N(t,e):void 0}}function We(t){if(typeof Symbol<"u"&&t[Symbol.iterator]!=null||t["@@iterator"]!=null)return Array.from(t)}function Ze(t){if(Array.isArray(t))return N(t)}function N(t,e){(e==null||e>t.length)&&(e=t.length);for(var i=0,n=Array(e);i<e;i++)n[i]=t[i];return n}var Je={name:"MultiSelect",extends:Ue,inheritAttrs:!1,emits:["change","focus","blur","before-show","before-hide","show","hide","filter","selectall-change"],inject:{$pcFluid:{default:null}},outsideClickListener:null,scrollHandler:null,resizeListener:null,overlay:null,list:null,virtualScroller:null,startRangeIndex:-1,searchTimeout:null,searchValue:"",selectOnFocus:!1,data:function(){return{id:this.$attrs.id,clicked:!1,focused:!1,focusedOptionIndex:-1,filterValue:null,overlayVisible:!1}},watch:{"$attrs.id":function(e){this.id=e||q()},options:function(){this.autoUpdateModel()}},mounted:function(){this.id=this.id||q(),this.autoUpdateModel()},beforeUnmount:function(){this.unbindOutsideClickListener(),this.unbindResizeListener(),this.scrollHandler&&(this.scrollHandler.destroy(),this.scrollHandler=null),this.overlay&&(U.clear(this.overlay),this.overlay=null)},methods:{getOptionIndex:function(e,i){return this.virtualScrollerDisabled?e:i&&i(e).index},getOptionLabel:function(e){return this.optionLabel?K(e,this.optionLabel):e},getOptionValue:function(e){return this.optionValue?K(e,this.optionValue):e},getOptionRenderKey:function(e,i){return this.dataKey?K(e,this.dataKey):this.getOptionLabel(e)+"_".concat(i)},getHeaderCheckboxPTOptions:function(e){return this.ptm(e,{context:{selected:this.allSelected}})},getCheckboxPTOptions:function(e,i,n,s){return this.ptm(s,{context:{selected:this.isSelected(e),focused:this.focusedOptionIndex===this.getOptionIndex(n,i),disabled:this.isOptionDisabled(e)}})},isOptionDisabled:function(e){return this.maxSelectionLimitReached&&!this.isSelected(e)?!0:this.optionDisabled?K(e,this.optionDisabled):!1},isOptionGroup:function(e){return this.optionGroupLabel&&e.optionGroup&&e.group},getOptionGroupLabel:function(e){return K(e,this.optionGroupLabel)},getOptionGroupChildren:function(e){return K(e,this.optionGroupChildren)},getAriaPosInset:function(e){var i=this;return(this.optionGroupLabel?e-this.visibleOptions.slice(0,e).filter(function(n){return i.isOptionGroup(n)}).length:e)+1},show:function(e){this.$emit("before-show"),this.overlayVisible=!0,this.focusedOptionIndex=this.focusedOptionIndex!==-1?this.focusedOptionIndex:this.autoOptionFocus?this.findFirstFocusedOptionIndex():this.findSelectedOptionIndex(),e&&F(this.$refs.focusInput)},hide:function(e){var i=this,n=function(){i.$emit("before-hide"),i.overlayVisible=!1,i.clicked=!1,i.focusedOptionIndex=-1,i.searchValue="",i.resetFilterOnHide&&(i.filterValue=null),e&&F(i.$refs.focusInput)};setTimeout(function(){n()},0)},onFocus:function(e){this.disabled||(this.focused=!0,this.overlayVisible&&(this.focusedOptionIndex=this.focusedOptionIndex!==-1?this.focusedOptionIndex:this.autoOptionFocus?this.findFirstFocusedOptionIndex():this.findSelectedOptionIndex(),this.scrollInView(this.focusedOptionIndex)),this.$emit("focus",e))},onBlur:function(e){var i,n;this.clicked=!1,this.focused=!1,this.focusedOptionIndex=-1,this.searchValue="",this.$emit("blur",e),(i=(n=this.formField).onBlur)===null||i===void 0||i.call(n)},onKeyDown:function(e){var i=this;if(this.disabled){e.preventDefault();return}var n=e.metaKey||e.ctrlKey;switch(e.code){case"ArrowDown":this.onArrowDownKey(e);break;case"ArrowUp":this.onArrowUpKey(e);break;case"Home":this.onHomeKey(e);break;case"End":this.onEndKey(e);break;case"PageDown":this.onPageDownKey(e);break;case"PageUp":this.onPageUpKey(e);break;case"Enter":case"NumpadEnter":case"Space":this.onEnterKey(e);break;case"Escape":this.onEscapeKey(e);break;case"Tab":this.onTabKey(e);break;case"ShiftLeft":case"ShiftRight":this.onShiftKey(e);break;default:if(e.code==="KeyA"&&n){var s=this.visibleOptions.filter(function(l){return i.isValidOption(l)}).map(function(l){return i.getOptionValue(l)});this.updateModel(e,s),e.preventDefault();break}!n&&be(e.key)&&(!this.overlayVisible&&this.show(),this.searchOptions(e),e.preventDefault());break}this.clicked=!1},onContainerClick:function(e){this.disabled||this.loading||e.target.tagName==="INPUT"||e.target.getAttribute("data-pc-section")==="clearicon"||e.target.closest('[data-pc-section="clearicon"]')||((!this.overlay||!this.overlay.contains(e.target))&&(this.overlayVisible?this.hide(!0):this.show(!0)),this.clicked=!0)},onClearClick:function(e){this.updateModel(e,null),this.resetFilterOnClear&&(this.filterValue=null)},onFirstHiddenFocus:function(e){var i=e.relatedTarget===this.$refs.focusInput?me(this.overlay,':not([data-p-hidden-focusable="true"])'):this.$refs.focusInput;F(i)},onLastHiddenFocus:function(e){var i=e.relatedTarget===this.$refs.focusInput?fe(this.overlay,':not([data-p-hidden-focusable="true"])'):this.$refs.focusInput;F(i)},onOptionSelect:function(e,i){var n=this,s=arguments.length>2&&arguments[2]!==void 0?arguments[2]:-1,l=arguments.length>3&&arguments[3]!==void 0?arguments[3]:!1;if(!(this.disabled||this.isOptionDisabled(i))){var d=this.isSelected(i),h=null;d?h=this.d_value.filter(function(f){return!z(f,n.getOptionValue(i),n.equalityKey)}):h=[].concat(Z(this.d_value||[]),[this.getOptionValue(i)]),this.updateModel(e,h),s!==-1&&(this.focusedOptionIndex=s),l&&F(this.$refs.focusInput)}},onOptionMouseMove:function(e,i){this.focusOnHover&&this.changeFocusedOptionIndex(e,i)},onOptionSelectRange:function(e){var i=this,n=arguments.length>1&&arguments[1]!==void 0?arguments[1]:-1,s=arguments.length>2&&arguments[2]!==void 0?arguments[2]:-1;if(n===-1&&(n=this.findNearestSelectedOptionIndex(s,!0)),s===-1&&(s=this.findNearestSelectedOptionIndex(n)),n!==-1&&s!==-1){var l=Math.min(n,s),d=Math.max(n,s),h=this.visibleOptions.slice(l,d+1).filter(function(f){return i.isValidOption(f)}).map(function(f){return i.getOptionValue(f)});this.updateModel(e,h)}},onFilterChange:function(e){var i=e.target.value;this.filterValue=i,this.focusedOptionIndex=-1,this.$emit("filter",{originalEvent:e,value:i}),!this.virtualScrollerDisabled&&this.virtualScroller.scrollToIndex(0)},onFilterKeyDown:function(e){switch(e.code){case"ArrowDown":this.onArrowDownKey(e);break;case"ArrowUp":this.onArrowUpKey(e,!0);break;case"ArrowLeft":case"ArrowRight":this.onArrowLeftKey(e,!0);break;case"Home":this.onHomeKey(e,!0);break;case"End":this.onEndKey(e,!0);break;case"Enter":case"NumpadEnter":this.onEnterKey(e);break;case"Escape":this.onEscapeKey(e);break;case"Tab":this.onTabKey(e,!0);break}},onFilterBlur:function(){this.focusedOptionIndex=-1},onFilterUpdated:function(){this.overlayVisible&&this.alignOverlay()},onOverlayClick:function(e){we.emit("overlay-click",{originalEvent:e,target:this.$el})},onOverlayKeyDown:function(e){switch(e.code){case"Escape":this.onEscapeKey(e);break}},onArrowDownKey:function(e){if(!this.overlayVisible)this.show();else{var i=this.focusedOptionIndex!==-1?this.findNextOptionIndex(this.focusedOptionIndex):this.clicked?this.findFirstOptionIndex():this.findFirstFocusedOptionIndex();e.shiftKey&&this.onOptionSelectRange(e,this.startRangeIndex,i),this.changeFocusedOptionIndex(e,i)}e.preventDefault()},onArrowUpKey:function(e){var i=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1;if(e.altKey&&!i)this.focusedOptionIndex!==-1&&this.onOptionSelect(e,this.visibleOptions[this.focusedOptionIndex]),this.overlayVisible&&this.hide(),e.preventDefault();else{var n=this.focusedOptionIndex!==-1?this.findPrevOptionIndex(this.focusedOptionIndex):this.clicked?this.findLastOptionIndex():this.findLastFocusedOptionIndex();e.shiftKey&&this.onOptionSelectRange(e,n,this.startRangeIndex),this.changeFocusedOptionIndex(e,n),!this.overlayVisible&&this.show(),e.preventDefault()}},onArrowLeftKey:function(e){var i=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1;i&&(this.focusedOptionIndex=-1)},onHomeKey:function(e){var i=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1;if(i){var n=e.currentTarget;e.shiftKey?n.setSelectionRange(0,e.target.selectionStart):(n.setSelectionRange(0,0),this.focusedOptionIndex=-1)}else{var s=e.metaKey||e.ctrlKey,l=this.findFirstOptionIndex();e.shiftKey&&s&&this.onOptionSelectRange(e,l,this.startRangeIndex),this.changeFocusedOptionIndex(e,l),!this.overlayVisible&&this.show()}e.preventDefault()},onEndKey:function(e){var i=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1;if(i){var n=e.currentTarget;if(e.shiftKey)n.setSelectionRange(e.target.selectionStart,n.value.length);else{var s=n.value.length;n.setSelectionRange(s,s),this.focusedOptionIndex=-1}}else{var l=e.metaKey||e.ctrlKey,d=this.findLastOptionIndex();e.shiftKey&&l&&this.onOptionSelectRange(e,this.startRangeIndex,d),this.changeFocusedOptionIndex(e,d),!this.overlayVisible&&this.show()}e.preventDefault()},onPageUpKey:function(e){this.scrollInView(0),e.preventDefault()},onPageDownKey:function(e){this.scrollInView(this.visibleOptions.length-1),e.preventDefault()},onEnterKey:function(e){this.overlayVisible?this.focusedOptionIndex!==-1&&(e.shiftKey?this.onOptionSelectRange(e,this.focusedOptionIndex):this.onOptionSelect(e,this.visibleOptions[this.focusedOptionIndex])):(this.focusedOptionIndex=-1,this.onArrowDownKey(e)),e.preventDefault()},onEscapeKey:function(e){this.overlayVisible&&this.hide(!0),e.preventDefault()},onTabKey:function(e){var i=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1;i||(this.overlayVisible&&this.hasFocusableElements()?(F(e.shiftKey?this.$refs.lastHiddenFocusableElementOnOverlay:this.$refs.firstHiddenFocusableElementOnOverlay),e.preventDefault()):(this.focusedOptionIndex!==-1&&this.onOptionSelect(e,this.visibleOptions[this.focusedOptionIndex]),this.overlayVisible&&this.hide(this.filter)))},onShiftKey:function(){this.startRangeIndex=this.focusedOptionIndex},onOverlayEnter:function(e){U.set("overlay",e,this.$primevue.config.zIndex.overlay),he(e,{position:"absolute",top:"0",left:"0"}),this.alignOverlay(),this.scrollInView(),this.autoFilterFocus&&F(this.$refs.filterInput.$el)},onOverlayAfterEnter:function(){this.bindOutsideClickListener(),this.bindScrollListener(),this.bindResizeListener(),this.$emit("show")},onOverlayLeave:function(){this.unbindOutsideClickListener(),this.unbindScrollListener(),this.unbindResizeListener(),this.$emit("hide"),this.overlay=null},onOverlayAfterLeave:function(e){U.clear(e)},alignOverlay:function(){this.appendTo==="self"?ue(this.overlay,this.$el):(this.overlay.style.minWidth=de(this.$el)+"px",pe(this.overlay,this.$el))},bindOutsideClickListener:function(){var e=this;this.outsideClickListener||(this.outsideClickListener=function(i){e.overlayVisible&&e.isOutsideClicked(i)&&e.hide()},document.addEventListener("click",this.outsideClickListener))},unbindOutsideClickListener:function(){this.outsideClickListener&&(document.removeEventListener("click",this.outsideClickListener),this.outsideClickListener=null)},bindScrollListener:function(){var e=this;this.scrollHandler||(this.scrollHandler=new ke(this.$refs.container,function(){e.overlayVisible&&e.hide()})),this.scrollHandler.bindScrollListener()},unbindScrollListener:function(){this.scrollHandler&&this.scrollHandler.unbindScrollListener()},bindResizeListener:function(){var e=this;this.resizeListener||(this.resizeListener=function(){e.overlayVisible&&!ce()&&e.hide()},window.addEventListener("resize",this.resizeListener))},unbindResizeListener:function(){this.resizeListener&&(window.removeEventListener("resize",this.resizeListener),this.resizeListener=null)},isOutsideClicked:function(e){return!(this.$el.isSameNode(e.target)||this.$el.contains(e.target)||this.overlay&&this.overlay.contains(e.target))},getLabelByValue:function(e){var i=this,n=this.optionGroupLabel?this.flatOptions(this.options):this.options||[],s=n.find(function(l){return!i.isOptionGroup(l)&&z(i.getOptionValue(l),e,i.equalityKey)});return s?this.getOptionLabel(s):null},getSelectedItemsLabel:function(){var e=/{(.*?)}/,i=this.selectedItemsLabel||this.$primevue.config.locale.selectionMessage;return e.test(i)?i.replace(i.match(e)[0],this.d_value.length+""):i},onToggleAll:function(e){var i=this;if(this.selectAll!==null)this.$emit("selectall-change",{originalEvent:e,checked:!this.allSelected});else{var n=this.allSelected?[]:this.visibleOptions.filter(function(s){return i.isValidOption(s)}).map(function(s){return i.getOptionValue(s)});this.updateModel(e,n)}},removeOption:function(e,i){var n=this;e.stopPropagation();var s=this.d_value.filter(function(l){return!z(l,i,n.equalityKey)});this.updateModel(e,s)},clearFilter:function(){this.filterValue=null},hasFocusableElements:function(){return ae(this.overlay,':not([data-p-hidden-focusable="true"])').length>0},isOptionMatched:function(e){var i;return this.isValidOption(e)&&typeof this.getOptionLabel(e)=="string"&&((i=this.getOptionLabel(e))===null||i===void 0?void 0:i.toLocaleLowerCase(this.filterLocale).startsWith(this.searchValue.toLocaleLowerCase(this.filterLocale)))},isValidOption:function(e){return x(e)&&!(this.isOptionDisabled(e)||this.isOptionGroup(e))},isValidSelectedOption:function(e){return this.isValidOption(e)&&this.isSelected(e)},isEquals:function(e,i){return z(e,i,this.equalityKey)},isSelected:function(e){var i=this,n=this.getOptionValue(e);return(this.d_value||[]).some(function(s){return i.isEquals(s,n)})},findFirstOptionIndex:function(){var e=this;return this.visibleOptions.findIndex(function(i){return e.isValidOption(i)})},findLastOptionIndex:function(){var e=this;return H(this.visibleOptions,function(i){return e.isValidOption(i)})},findNextOptionIndex:function(e){var i=this,n=e<this.visibleOptions.length-1?this.visibleOptions.slice(e+1).findIndex(function(s){return i.isValidOption(s)}):-1;return n>-1?n+e+1:e},findPrevOptionIndex:function(e){var i=this,n=e>0?H(this.visibleOptions.slice(0,e),function(s){return i.isValidOption(s)}):-1;return n>-1?n:e},findSelectedOptionIndex:function(){var e=this;if(this.$filled){for(var i=function(){var d=e.d_value[s],h=e.visibleOptions.findIndex(function(f){return e.isValidSelectedOption(f)&&e.isEquals(d,e.getOptionValue(f))});if(h>-1)return{v:h}},n,s=this.d_value.length-1;s>=0;s--)if(n=i(),n)return n.v}return-1},findFirstSelectedOptionIndex:function(){var e=this;return this.$filled?this.visibleOptions.findIndex(function(i){return e.isValidSelectedOption(i)}):-1},findLastSelectedOptionIndex:function(){var e=this;return this.$filled?H(this.visibleOptions,function(i){return e.isValidSelectedOption(i)}):-1},findNextSelectedOptionIndex:function(e){var i=this,n=this.$filled&&e<this.visibleOptions.length-1?this.visibleOptions.slice(e+1).findIndex(function(s){return i.isValidSelectedOption(s)}):-1;return n>-1?n+e+1:-1},findPrevSelectedOptionIndex:function(e){var i=this,n=this.$filled&&e>0?H(this.visibleOptions.slice(0,e),function(s){return i.isValidSelectedOption(s)}):-1;return n>-1?n:-1},findNearestSelectedOptionIndex:function(e){var i=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1,n=-1;return this.$filled&&(i?(n=this.findPrevSelectedOptionIndex(e),n=n===-1?this.findNextSelectedOptionIndex(e):n):(n=this.findNextSelectedOptionIndex(e),n=n===-1?this.findPrevSelectedOptionIndex(e):n)),n>-1?n:e},findFirstFocusedOptionIndex:function(){var e=this.findSelectedOptionIndex();return e<0?this.findFirstOptionIndex():e},findLastFocusedOptionIndex:function(){var e=this.findSelectedOptionIndex();return e<0?this.findLastOptionIndex():e},searchOptions:function(e){var i=this;this.searchValue=(this.searchValue||"")+e.key;var n=-1;x(this.searchValue)&&(this.focusedOptionIndex!==-1?(n=this.visibleOptions.slice(this.focusedOptionIndex).findIndex(function(s){return i.isOptionMatched(s)}),n=n===-1?this.visibleOptions.slice(0,this.focusedOptionIndex).findIndex(function(s){return i.isOptionMatched(s)}):n+this.focusedOptionIndex):n=this.visibleOptions.findIndex(function(s){return i.isOptionMatched(s)}),n===-1&&this.focusedOptionIndex===-1&&(n=this.findFirstFocusedOptionIndex()),n!==-1&&this.changeFocusedOptionIndex(e,n)),this.searchTimeout&&clearTimeout(this.searchTimeout),this.searchTimeout=setTimeout(function(){i.searchValue="",i.searchTimeout=null},500)},changeFocusedOptionIndex:function(e,i){this.focusedOptionIndex!==i&&(this.focusedOptionIndex=i,this.scrollInView(),this.selectOnFocus&&this.onOptionSelect(e,this.visibleOptions[i]))},scrollInView:function(){var e=this,i=arguments.length>0&&arguments[0]!==void 0?arguments[0]:-1;this.$nextTick(function(){var n=i!==-1?"".concat(e.id,"_").concat(i):e.focusedOptionId,s=re(e.list,'li[id="'.concat(n,'"]'));s?s.scrollIntoView&&s.scrollIntoView({block:"nearest",inline:"nearest"}):e.virtualScrollerDisabled||e.virtualScroller&&e.virtualScroller.scrollToIndex(i!==-1?i:e.focusedOptionIndex)})},autoUpdateModel:function(){if(this.selectOnFocus&&this.autoOptionFocus&&!this.$filled){this.focusedOptionIndex=this.findFirstFocusedOptionIndex();var e=this.getOptionValue(this.visibleOptions[this.focusedOptionIndex]);this.updateModel(null,[e])}},updateModel:function(e,i){this.writeValue(i,e),this.$emit("change",{originalEvent:e,value:i})},flatOptions:function(e){var i=this;return(e||[]).reduce(function(n,s,l){n.push({optionGroup:s,group:!0,index:l});var d=i.getOptionGroupChildren(s);return d&&d.forEach(function(h){return n.push(h)}),n},[])},overlayRef:function(e){this.overlay=e},listRef:function(e,i){this.list=e,i&&i(e)},virtualScrollerRef:function(e){this.virtualScroller=e}},computed:{visibleOptions:function(){var e=this,i=this.optionGroupLabel?this.flatOptions(this.options):this.options||[];if(this.filterValue){var n=oe.filter(i,this.searchFields,this.filterValue,this.filterMatchMode,this.filterLocale);if(this.optionGroupLabel){var s=this.options||[],l=[];return s.forEach(function(d){var h=e.getOptionGroupChildren(d),f=h.filter(function(B){return n.includes(B)});f.length>0&&l.push(W(W({},d),{},Y({},typeof e.optionGroupChildren=="string"?e.optionGroupChildren:"items",Z(f))))}),this.flatOptions(l)}return n}return i},label:function(){var e;if(this.d_value&&this.d_value.length){if(x(this.maxSelectedLabels)&&this.d_value.length>this.maxSelectedLabels)return this.getSelectedItemsLabel();e="";for(var i=0;i<this.d_value.length;i++)i!==0&&(e+=", "),e+=this.getLabelByValue(this.d_value[i])}else e=this.placeholder;return e},chipSelectedItems:function(){return x(this.maxSelectedLabels)&&this.d_value&&this.d_value.length>this.maxSelectedLabels},allSelected:function(){var e=this;return this.selectAll!==null?this.selectAll:x(this.visibleOptions)&&this.visibleOptions.every(function(i){return e.isOptionGroup(i)||e.isOptionDisabled(i)||e.isSelected(i)})},hasSelectedOption:function(){return this.$filled},equalityKey:function(){return this.optionValue?null:this.dataKey},searchFields:function(){return this.filterFields||[this.optionLabel]},maxSelectionLimitReached:function(){return this.selectionLimit&&this.d_value&&this.d_value.length===this.selectionLimit},filterResultMessageText:function(){return x(this.visibleOptions)?this.filterMessageText.replaceAll("{0}",this.visibleOptions.length):this.emptyFilterMessageText},filterMessageText:function(){return this.filterMessage||this.$primevue.config.locale.searchMessage||""},emptyFilterMessageText:function(){return this.emptyFilterMessage||this.$primevue.config.locale.emptySearchMessage||this.$primevue.config.locale.emptyFilterMessage||""},emptyMessageText:function(){return this.emptyMessage||this.$primevue.config.locale.emptyMessage||""},selectionMessageText:function(){return this.selectionMessage||this.$primevue.config.locale.selectionMessage||""},emptySelectionMessageText:function(){return this.emptySelectionMessage||this.$primevue.config.locale.emptySelectionMessage||""},selectedMessageText:function(){return this.$filled?this.selectionMessageText.replaceAll("{0}",this.d_value.length):this.emptySelectionMessageText},focusedOptionId:function(){return this.focusedOptionIndex!==-1?"".concat(this.id,"_").concat(this.focusedOptionIndex):null},ariaSetSize:function(){var e=this;return this.visibleOptions.filter(function(i){return!e.isOptionGroup(i)}).length},toggleAllAriaLabel:function(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria[this.allSelected?"selectAll":"unselectAll"]:void 0},listAriaLabel:function(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.listLabel:void 0},virtualScrollerDisabled:function(){return!this.virtualScrollerOptions},hasFluid:function(){return se(this.fluid)?!!this.$pcFluid:this.fluid},isClearIconVisible:function(){return this.showClear&&this.d_value!=null&&x(this.options)}},directives:{ripple:Me},components:{InputText:Pe,Checkbox:Ee,VirtualScroller:Ke,Portal:Se,Chip:De,IconField:Ve,InputIcon:Ce,TimesIcon:Ae,SearchIcon:Fe,ChevronDownIcon:xe,SpinnerIcon:Te,CheckIcon:Le}};function D(t){"@babel/helpers - typeof";return D=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(e){return typeof e}:function(e){return e&&typeof Symbol=="function"&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},D(t)}function J(t,e,i){return(e=Ye(e))in t?Object.defineProperty(t,e,{value:i,enumerable:!0,configurable:!0,writable:!0}):t[e]=i,t}function Ye(t){var e=Qe(t,"string");return D(e)=="symbol"?e:e+""}function Qe(t,e){if(D(t)!="object"||!t)return t;var i=t[Symbol.toPrimitive];if(i!==void 0){var n=i.call(t,e);if(D(n)!="object")return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return(e==="string"?String:Number)(t)}var Xe=["id","disabled","placeholder","tabindex","aria-label","aria-labelledby","aria-expanded","aria-controls","aria-activedescendant","aria-invalid"],_e={key:0},et=["id","aria-label"],tt=["id"],it=["id","aria-label","aria-selected","aria-disabled","aria-setsize","aria-posinset","onClick","onMousemove","data-p-selected","data-p-focused","data-p-disabled"];function nt(t,e,i,n,s,l){var d=I("Chip"),h=I("SpinnerIcon"),f=I("Checkbox"),B=I("InputText"),Q=I("SearchIcon"),X=I("InputIcon"),_=I("IconField"),ee=I("VirtualScroller"),te=I("Portal"),ie=ge("ripple");return r(),u("div",o({ref:"container",class:t.cx("root"),style:t.sx("root"),onClick:e[7]||(e[7]=function(){return l.onContainerClick&&l.onContainerClick.apply(l,arguments)})},t.ptmi("root")),[y("div",o({class:"p-hidden-accessible"},t.ptm("hiddenInputContainer"),{"data-p-hidden-accessible":!0}),[y("input",o({ref:"focusInput",id:t.inputId,type:"text",readonly:"",disabled:t.disabled,placeholder:t.placeholder,tabindex:t.disabled?-1:t.tabindex,role:"combobox","aria-label":t.ariaLabel,"aria-labelledby":t.ariaLabelledby,"aria-haspopup":"listbox","aria-expanded":s.overlayVisible,"aria-controls":s.id+"_list","aria-activedescendant":s.focused?l.focusedOptionId:void 0,"aria-invalid":t.invalid||void 0,onFocus:e[0]||(e[0]=function(){return l.onFocus&&l.onFocus.apply(l,arguments)}),onBlur:e[1]||(e[1]=function(){return l.onBlur&&l.onBlur.apply(l,arguments)}),onKeydown:e[2]||(e[2]=function(){return l.onKeyDown&&l.onKeyDown.apply(l,arguments)})},t.ptm("hiddenInput")),null,16,Xe)],16),y("div",o({class:t.cx("labelContainer")},t.ptm("labelContainer")),[y("div",o({class:t.cx("label")},t.ptm("label")),[m(t.$slots,"value",{value:t.d_value,placeholder:t.placeholder},function(){return[t.display==="comma"?(r(),u(T,{key:0},[A(v(l.label||"empty"),1)],64)):t.display==="chip"?(r(),u(T,{key:1},[l.chipSelectedItems?(r(),u("span",_e,v(l.label),1)):(r(!0),u(T,{key:1},j(t.d_value,function(a){return r(),u("span",o({key:l.getLabelByValue(a),class:t.cx("chipItem"),ref_for:!0},t.ptm("chipItem")),[m(t.$slots,"chip",{value:a,removeCallback:function(w){return l.removeOption(w,a)}},function(){return[C(d,{class:k(t.cx("pcChip")),label:l.getLabelByValue(a),removeIcon:t.chipIcon||t.removeTokenIcon,removable:"",unstyled:t.unstyled,onRemove:function(w){return l.removeOption(w,a)},pt:t.ptm("pcChip")},{removeicon:S(function(){return[m(t.$slots,t.$slots.chipicon?"chipicon":"removetokenicon",{class:k(t.cx("chipIcon")),item:a,removeCallback:function(w){return l.removeOption(w,a)}})]}),_:2},1032,["class","label","removeIcon","unstyled","onRemove","pt"])]})],16)}),128)),!t.d_value||t.d_value.length===0?(r(),u(T,{key:2},[A(v(t.placeholder||"empty"),1)],64)):g("",!0)],64)):g("",!0)]})],16)],16),l.isClearIconVisible?m(t.$slots,"clearicon",{key:0,class:k(t.cx("clearIcon")),clearCallback:l.onClearClick},function(){return[(r(),O(M(t.clearIcon?"i":"TimesIcon"),o({ref:"clearIcon",class:[t.cx("clearIcon"),t.clearIcon],onClick:l.onClearClick},t.ptm("clearIcon"),{"data-pc-section":"clearicon"}),null,16,["class","onClick"]))]}):g("",!0),y("div",o({class:t.cx("dropdown")},t.ptm("dropdown")),[t.loading?m(t.$slots,"loadingicon",{key:0,class:k(t.cx("loadingIcon"))},function(){return[t.loadingIcon?(r(),u("span",o({key:0,class:[t.cx("loadingIcon"),"pi-spin",t.loadingIcon],"aria-hidden":"true"},t.ptm("loadingIcon")),null,16)):(r(),O(h,o({key:1,class:t.cx("loadingIcon"),spin:"","aria-hidden":"true"},t.ptm("loadingIcon")),null,16,["class"]))]}):m(t.$slots,"dropdownicon",{key:1,class:k(t.cx("dropdownIcon"))},function(){return[(r(),O(M(t.dropdownIcon?"span":"ChevronDownIcon"),o({class:[t.cx("dropdownIcon"),t.dropdownIcon],"aria-hidden":"true"},t.ptm("dropdownIcon")),null,16,["class"]))]})],16),C(te,{appendTo:t.appendTo},{default:S(function(){return[C(ye,o({name:"p-connected-overlay",onEnter:l.onOverlayEnter,onAfterEnter:l.onOverlayAfterEnter,onLeave:l.onOverlayLeave,onAfterLeave:l.onOverlayAfterLeave},t.ptm("transition")),{default:S(function(){return[s.overlayVisible?(r(),u("div",o({key:0,ref:l.overlayRef,style:[t.panelStyle,t.overlayStyle],class:[t.cx("overlay"),t.panelClass,t.overlayClass],onClick:e[5]||(e[5]=function(){return l.onOverlayClick&&l.onOverlayClick.apply(l,arguments)}),onKeydown:e[6]||(e[6]=function(){return l.onOverlayKeyDown&&l.onOverlayKeyDown.apply(l,arguments)})},t.ptm("overlay")),[y("span",o({ref:"firstHiddenFocusableElementOnOverlay",role:"presentation","aria-hidden":"true",class:"p-hidden-accessible p-hidden-focusable",tabindex:0,onFocus:e[3]||(e[3]=function(){return l.onFirstHiddenFocus&&l.onFirstHiddenFocus.apply(l,arguments)})},t.ptm("hiddenFirstFocusableEl"),{"data-p-hidden-accessible":!0,"data-p-hidden-focusable":!0}),null,16),m(t.$slots,"header",{value:t.d_value,options:l.visibleOptions}),t.showToggleAll&&t.selectionLimit==null||t.filter?(r(),u("div",o({key:0,class:t.cx("header")},t.ptm("header")),[t.showToggleAll&&t.selectionLimit==null?(r(),O(f,{key:0,modelValue:l.allSelected,binary:!0,disabled:t.disabled,variant:t.variant,"aria-label":l.toggleAllAriaLabel,onChange:l.onToggleAll,unstyled:t.unstyled,pt:l.getHeaderCheckboxPTOptions("pcHeaderCheckbox")},{icon:S(function(a){return[t.$slots.headercheckboxicon?(r(),O(M(t.$slots.headercheckboxicon),{key:0,checked:a.checked,class:k(a.class)},null,8,["checked","class"])):a.checked?(r(),O(M(t.checkboxIcon?"span":"CheckIcon"),o({key:1,class:[a.class,J({},t.checkboxIcon,a.checked)]},l.getHeaderCheckboxPTOptions("pcHeaderCheckbox.icon")),null,16,["class"])):g("",!0)]}),_:1},8,["modelValue","disabled","variant","aria-label","onChange","unstyled","pt"])):g("",!0),t.filter?(r(),O(_,{key:1,class:k(t.cx("pcFilterContainer")),unstyled:t.unstyled,pt:t.ptm("pcFilterContainer")},{default:S(function(){return[C(B,{ref:"filterInput",value:s.filterValue,onVnodeMounted:l.onFilterUpdated,onVnodeUpdated:l.onFilterUpdated,class:k(t.cx("pcFilter")),placeholder:t.filterPlaceholder,disabled:t.disabled,variant:t.variant,unstyled:t.unstyled,role:"searchbox",autocomplete:"off","aria-owns":s.id+"_list","aria-activedescendant":l.focusedOptionId,onKeydown:l.onFilterKeyDown,onBlur:l.onFilterBlur,onInput:l.onFilterChange,pt:t.ptm("pcFilter")},null,8,["value","onVnodeMounted","onVnodeUpdated","class","placeholder","disabled","variant","unstyled","aria-owns","aria-activedescendant","onKeydown","onBlur","onInput","pt"]),C(X,{unstyled:t.unstyled,pt:t.ptm("pcFilterIconContainer")},{default:S(function(){return[m(t.$slots,"filtericon",{},function(){return[t.filterIcon?(r(),u("span",o({key:0,class:t.filterIcon},t.ptm("filterIcon")),null,16)):(r(),O(Q,ve(o({key:1},t.ptm("filterIcon"))),null,16))]})]}),_:3},8,["unstyled","pt"])]}),_:3},8,["class","unstyled","pt"])):g("",!0),t.filter?(r(),u("span",o({key:2,role:"status","aria-live":"polite",class:"p-hidden-accessible"},t.ptm("hiddenFilterResult"),{"data-p-hidden-accessible":!0}),v(l.filterResultMessageText),17)):g("",!0)],16)):g("",!0),y("div",o({class:t.cx("listContainer"),style:{"max-height":l.virtualScrollerDisabled?t.scrollHeight:""}},t.ptm("listContainer")),[C(ee,o({ref:l.virtualScrollerRef},t.virtualScrollerOptions,{items:l.visibleOptions,style:{height:t.scrollHeight},tabindex:-1,disabled:l.virtualScrollerDisabled,pt:t.ptm("virtualScroller")}),Oe({content:S(function(a){var V=a.styleClass,w=a.contentRef,P=a.items,b=a.getItemOptions,ne=a.contentStyle,R=a.itemSize;return[y("ul",o({ref:function(p){return l.listRef(p,w)},id:s.id+"_list",class:[t.cx("list"),V],style:ne,role:"listbox","aria-multiselectable":"true","aria-label":l.listAriaLabel},t.ptm("list")),[(r(!0),u(T,null,j(P,function(c,p){return r(),u(T,{key:l.getOptionRenderKey(c,l.getOptionIndex(p,b))},[l.isOptionGroup(c)?(r(),u("li",o({key:0,id:s.id+"_"+l.getOptionIndex(p,b),style:{height:R?R+"px":void 0},class:t.cx("optionGroup"),role:"option",ref_for:!0},t.ptm("optionGroup")),[m(t.$slots,"optiongroup",{option:c.optionGroup,index:l.getOptionIndex(p,b)},function(){return[A(v(l.getOptionGroupLabel(c.optionGroup)),1)]})],16,tt)):Ie((r(),u("li",o({key:1,id:s.id+"_"+l.getOptionIndex(p,b),style:{height:R?R+"px":void 0},class:t.cx("option",{option:c,index:p,getItemOptions:b}),role:"option","aria-label":l.getOptionLabel(c),"aria-selected":l.isSelected(c),"aria-disabled":l.isOptionDisabled(c),"aria-setsize":l.ariaSetSize,"aria-posinset":l.getAriaPosInset(l.getOptionIndex(p,b)),onClick:function(G){return l.onOptionSelect(G,c,l.getOptionIndex(p,b),!0)},onMousemove:function(G){return l.onOptionMouseMove(G,l.getOptionIndex(p,b))},ref_for:!0},l.getCheckboxPTOptions(c,b,p,"option"),{"data-p-selected":l.isSelected(c),"data-p-focused":s.focusedOptionIndex===l.getOptionIndex(p,b),"data-p-disabled":l.isOptionDisabled(c)}),[C(f,{defaultValue:l.isSelected(c),binary:!0,tabindex:-1,variant:t.variant,unstyled:t.unstyled,pt:l.getCheckboxPTOptions(c,b,p,"pcOptionCheckbox")},{icon:S(function(L){return[t.$slots.optioncheckboxicon||t.$slots.itemcheckboxicon?(r(),O(M(t.$slots.optioncheckboxicon||t.$slots.itemcheckboxicon),{key:0,checked:L.checked,class:k(L.class)},null,8,["checked","class"])):L.checked?(r(),O(M(t.checkboxIcon?"span":"CheckIcon"),o({key:1,class:[L.class,J({},t.checkboxIcon,L.checked)],ref_for:!0},l.getCheckboxPTOptions(c,b,p,"pcOptionCheckbox.icon")),null,16,["class"])):g("",!0)]}),_:2},1032,["defaultValue","variant","unstyled","pt"]),m(t.$slots,"option",{option:c,selected:l.isSelected(c),index:l.getOptionIndex(p,b)},function(){return[y("span",o({ref_for:!0},t.ptm("optionLabel")),v(l.getOptionLabel(c)),17)]})],16,it)),[[ie]])],64)}),128)),s.filterValue&&(!P||P&&P.length===0)?(r(),u("li",o({key:0,class:t.cx("emptyMessage"),role:"option"},t.ptm("emptyMessage")),[m(t.$slots,"emptyfilter",{},function(){return[A(v(l.emptyFilterMessageText),1)]})],16)):!t.options||t.options&&t.options.length===0?(r(),u("li",o({key:1,class:t.cx("emptyMessage"),role:"option"},t.ptm("emptyMessage")),[m(t.$slots,"empty",{},function(){return[A(v(l.emptyMessageText),1)]})],16)):g("",!0)],16,et)]}),_:2},[t.$slots.loader?{name:"loader",fn:S(function(a){var V=a.options;return[m(t.$slots,"loader",{options:V})]}),key:"0"}:void 0]),1040,["items","style","disabled","pt"])],16),m(t.$slots,"footer",{value:t.d_value,options:l.visibleOptions}),!t.options||t.options&&t.options.length===0?(r(),u("span",o({key:1,role:"status","aria-live":"polite",class:"p-hidden-accessible"},t.ptm("hiddenEmptyMessage"),{"data-p-hidden-accessible":!0}),v(l.emptyMessageText),17)):g("",!0),y("span",o({role:"status","aria-live":"polite",class:"p-hidden-accessible"},t.ptm("hiddenSelectedMessage"),{"data-p-hidden-accessible":!0}),v(l.selectedMessageText),17),y("span",o({ref:"lastHiddenFocusableElementOnOverlay",role:"presentation","aria-hidden":"true",class:"p-hidden-accessible p-hidden-focusable",tabindex:0,onFocus:e[4]||(e[4]=function(){return l.onLastHiddenFocus&&l.onLastHiddenFocus.apply(l,arguments)})},t.ptm("hiddenLastFocusableEl"),{"data-p-hidden-accessible":!0,"data-p-hidden-focusable":!0}),null,16)],16)):g("",!0)]}),_:3},16,["onEnter","onAfterEnter","onLeave","onAfterLeave"])]}),_:3},8,["appendTo"])],16)}Je.render=nt;export{Je as s};
