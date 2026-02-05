const __vite__mapDeps=(i,m=__vite__mapDeps,d=(m.f||(m.f=["assets/quill-Bn6O9OcI.js","assets/app-DsieonZu.js","assets/app-Cy5jDqfB.css"])))=>i.map(i=>d[i]);
import{B as he,U as nn,y as tn,V as te,h as ce,X as ue,z as T,A as ln,c as q,o as b,b as i,d as k,K as on,g as C,m as f,G as V,F as w,I as M,w as j,S as qe,e as Z,t as S,H as $,D as O,a as y,i as le,x as rn,n as an,p as sn,q as dn,s as cn,Y as ke,v as un,j as F,Z as pn,$ as fn,T as bn,J as mn,_ as hn,a0 as qn,a1 as yn,a2 as gn,r as D,C as vn,N as oe,P as _,f as kn,u as d}from"./app-DsieonZu.js";import{r as we}from"./imageHelper-BscTlunE.js";import{a as z}from"./index-BUoSKoQw.js";import{s as re}from"./index-lraT3xMD.js";import{s as K,a as wn,b as xn}from"./index-ctHPnxG6.js";import{s as Sn}from"./index-DQk5tCQQ.js";import{s as I}from"./index-DSOO72NQ.js";import{s as ie}from"./index-C9lRBuvg.js";import{s as Cn}from"./index-CptJlQWZ.js";import{s as On}from"./index-nljgXSqA.js";import{s as Vn,O as Tn,C as In,Z as ae,U as xe}from"./index-DQNAyf7j.js";import{s as je}from"./index-BCjHW8ac.js";import{s as Kn}from"./index-CfjDihnE.js";import{s as Pn}from"./index-Ckz4uwLP.js";import{s as Ne,R as Le}from"./index-B1YzyBAC.js";import{b as En,c as Mn,d as jn}from"./index-6gGX_01L.js";import{s as Ae}from"./index-Cs22S3sc.js";import{s as Nn}from"./index-C1VHlqZY.js";import{s as Ln}from"./index-BDVPGmky.js";import{a as An,s as $n}from"./index-BZldi7SO.js";import{_ as zn}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./index-BVddfgdl.js";import"./index-CvW0F4Ik.js";import"./index-CC_nf7EJ.js";import"./index-B6Ie5QXU.js";var Fn=function(t){var n=t.dt;return`
.p-tree {
    background: `.concat(n("tree.background"),`;
    color: `).concat(n("tree.color"),`;
    padding: `).concat(n("tree.padding"),`;
}

.p-tree-root-children,
.p-tree-node-children {
    display: flex;
    list-style-type: none;
    flex-direction: column;
    margin: 0;
    gap: `).concat(n("tree.gap"),`;
}

.p-tree-root-children {
    padding: 0;
    padding-block-start: `).concat(n("tree.gap"),`;
}

.p-tree-node-children {
    padding: 0;
    padding-block-start: `).concat(n("tree.gap"),`;
    padding-inline-start: `).concat(n("tree.indent"),`;
}

.p-tree-node {
    padding: 0;
    outline: 0 none;
}

.p-tree-node-content {
    border-radius: `).concat(n("tree.node.border.radius"),`;
    padding: `).concat(n("tree.node.padding"),`;
    display: flex;
    align-items: center;
    outline-color: transparent;
    color: `).concat(n("tree.node.color"),`;
    gap: `).concat(n("tree.node.gap"),`;
    transition: background `).concat(n("tree.transition.duration"),", color ").concat(n("tree.transition.duration"),", outline-color ").concat(n("tree.transition.duration"),", box-shadow ").concat(n("tree.transition.duration"),`;
}

.p-tree-node:focus-visible > .p-tree-node-content {
    box-shadow: `).concat(n("tree.node.focus.ring.shadow"),`;
    outline: `).concat(n("tree.node.focus.ring.width")," ").concat(n("tree.node.focus.ring.style")," ").concat(n("tree.node.focus.ring.color"),`;
    outline-offset: `).concat(n("tree.node.focus.ring.offset"),`;
}

.p-tree-node-content.p-tree-node-selectable:not(.p-tree-node-selected):hover {
    background: `).concat(n("tree.node.hover.background"),`;
    color: `).concat(n("tree.node.hover.color"),`;
}

.p-tree-node-content.p-tree-node-selectable:not(.p-tree-node-selected):hover .p-tree-node-icon {
    color: `).concat(n("tree.node.icon.hover.color"),`;
}

.p-tree-node-content.p-tree-node-selected {
    background: `).concat(n("tree.node.selected.background"),`;
    color: `).concat(n("tree.node.selected.color"),`;
}

.p-tree-node-content.p-tree-node-selected .p-tree-node-toggle-button {
    color: inherit;
}

.p-tree-node-toggle-button {
    cursor: pointer;
    user-select: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    flex-shrink: 0;
    width: `).concat(n("tree.node.toggle.button.size"),`;
    height: `).concat(n("tree.node.toggle.button.size"),`;
    color: `).concat(n("tree.node.toggle.button.color"),`;
    border: 0 none;
    background: transparent;
    border-radius: `).concat(n("tree.node.toggle.button.border.radius"),`;
    transition: background `).concat(n("tree.transition.duration"),", color ").concat(n("tree.transition.duration"),", border-color ").concat(n("tree.transition.duration"),", outline-color ").concat(n("tree.transition.duration"),", box-shadow ").concat(n("tree.transition.duration"),`;
    outline-color: transparent;
    padding: 0;
}

.p-tree-node-toggle-button:enabled:hover {
    background: `).concat(n("tree.node.toggle.button.hover.background"),`;
    color: `).concat(n("tree.node.toggle.button.hover.color"),`;
}

.p-tree-node-content.p-tree-node-selected .p-tree-node-toggle-button:hover {
    background: `).concat(n("tree.node.toggle.button.selected.hover.background"),`;
    color: `).concat(n("tree.node.toggle.button.selected.hover.color"),`;
}

.p-tree-root {
    overflow: auto;
}

.p-tree-node-selectable {
    cursor: pointer;
    user-select: none;
}

.p-tree-node-leaf > .p-tree-node-content .p-tree-node-toggle-button {
    visibility: hidden;
}

.p-tree-node-icon {
    color: `).concat(n("tree.node.icon.color"),`;
    transition: color `).concat(n("tree.transition.duration"),`;
}

.p-tree-node-content.p-tree-node-selected .p-tree-node-icon {
    color: `).concat(n("tree.node.icon.selected.color"),`;
}

.p-tree-filter {
    margin: `).concat(n("tree.filter.margin"),`;
}

.p-tree-filter-input {
    width: 100%;
}

.p-tree-loading {
    position: relative;
    height: 100%;
}

.p-tree-loading-icon {
    font-size: `).concat(n("tree.loading.icon.size"),`;
    width: `).concat(n("tree.loading.icon.size"),`;
    height: `).concat(n("tree.loading.icon.size"),`;
}

.p-tree .p-tree-mask {
    position: absolute;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.p-tree-flex-scrollable {
    display: flex;
    flex: 1;
    height: 100%;
    flex-direction: column;
}

.p-tree-flex-scrollable .p-tree-root {
    flex: 1;
}
`)},Dn={root:function(t){var n=t.props;return["p-tree p-component",{"p-tree-selectable":n.selectionMode!=null,"p-tree-loading":n.loading,"p-tree-flex-scrollable":n.scrollHeight==="flex"}]},mask:"p-tree-mask p-overlay-mask",loadingIcon:"p-tree-loading-icon",pcFilterContainer:"p-tree-filter",pcFilterInput:"p-tree-filter-input",wrapper:"p-tree-root",rootChildren:"p-tree-root-children",node:function(t){var n=t.instance;return["p-tree-node",{"p-tree-node-leaf":n.leaf}]},nodeContent:function(t){var n=t.instance;return["p-tree-node-content",n.node.styleClass,{"p-tree-node-selectable":n.selectable,"p-tree-node-selected":n.checkboxMode&&n.$parentInstance.highlightOnSelect?n.checked:n.selected}]},nodeToggleButton:"p-tree-node-toggle-button",nodeToggleIcon:"p-tree-node-toggle-icon",nodeCheckbox:"p-tree-node-checkbox",nodeIcon:"p-tree-node-icon",nodeLabel:"p-tree-node-label",nodeChildren:"p-tree-node-children"},_n=he.extend({name:"tree",theme:Fn,classes:Dn}),Un={name:"BaseTree",extends:Ae,props:{value:{type:null,default:null},expandedKeys:{type:null,default:null},selectionKeys:{type:null,default:null},selectionMode:{type:String,default:null},metaKeySelection:{type:Boolean,default:!1},loading:{type:Boolean,default:!1},loadingIcon:{type:String,default:void 0},loadingMode:{type:String,default:"mask"},filter:{type:Boolean,default:!1},filterBy:{type:[String,Function],default:"label"},filterMode:{type:String,default:"lenient"},filterPlaceholder:{type:String,default:null},filterLocale:{type:String,default:void 0},highlightOnSelect:{type:Boolean,default:!1},scrollHeight:{type:String,default:null},level:{type:Number,default:0},ariaLabelledby:{type:String,default:null},ariaLabel:{type:String,default:null}},style:_n,provide:function(){return{$pcTree:this,$parentInstance:this}}};function H(e){"@babel/helpers - typeof";return H=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},H(e)}function Se(e,t){var n=typeof Symbol<"u"&&e[Symbol.iterator]||e["@@iterator"];if(!n){if(Array.isArray(e)||(n=$e(e))||t){n&&(e=n);var l=0,c=function(){};return{s:c,n:function(){return l>=e.length?{done:!0}:{done:!1,value:e[l++]}},e:function(p){throw p},f:c}}throw new TypeError(`Invalid attempt to iterate non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}var a,m=!0,h=!1;return{s:function(){n=n.call(e)},n:function(){var p=n.next();return m=p.done,p},e:function(p){h=!0,a=p},f:function(){try{m||n.return==null||n.return()}finally{if(h)throw a}}}}function Ce(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var l=Object.getOwnPropertySymbols(e);t&&(l=l.filter(function(c){return Object.getOwnPropertyDescriptor(e,c).enumerable})),n.push.apply(n,l)}return n}function Oe(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?arguments[t]:{};t%2?Ce(Object(n),!0).forEach(function(l){Bn(e,l,n[l])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Ce(Object(n)).forEach(function(l){Object.defineProperty(e,l,Object.getOwnPropertyDescriptor(n,l))})}return e}function Bn(e,t,n){return(t=Hn(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function Hn(e){var t=Rn(e,"string");return H(t)=="symbol"?t:t+""}function Rn(e,t){if(H(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var l=n.call(e,t);if(H(l)!="object")return l;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}function U(e){return Gn(e)||Qn(e)||$e(e)||Wn()}function Wn(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function $e(e,t){if(e){if(typeof e=="string")return pe(e,t);var n={}.toString.call(e).slice(8,-1);return n==="Object"&&e.constructor&&(n=e.constructor.name),n==="Map"||n==="Set"?Array.from(e):n==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?pe(e,t):void 0}}function Qn(e){if(typeof Symbol<"u"&&e[Symbol.iterator]!=null||e["@@iterator"]!=null)return Array.from(e)}function Gn(e){if(Array.isArray(e))return pe(e)}function pe(e,t){(t==null||t>e.length)&&(t=e.length);for(var n=0,l=Array(t);n<t;n++)l[n]=e[n];return l}var ze={name:"TreeNode",hostName:"Tree",extends:Ae,emits:["node-toggle","node-click","checkbox-change"],props:{node:{type:null,default:null},expandedKeys:{type:null,default:null},loadingMode:{type:String,default:"mask"},selectionKeys:{type:null,default:null},selectionMode:{type:String,default:null},templates:{type:null,default:null},level:{type:Number,default:null},index:null},nodeTouched:!1,toggleClicked:!1,mounted:function(){this.setAllNodesTabIndexes()},methods:{toggle:function(){this.$emit("node-toggle",this.node),this.toggleClicked=!0},label:function(t){return typeof t.label=="function"?t.label():t.label},onChildNodeToggle:function(t){this.$emit("node-toggle",t)},getPTOptions:function(t){return this.ptm(t,{context:{node:this.node,index:this.index,expanded:this.expanded,selected:this.selected,checked:this.checked,partialChecked:this.partialChecked,leaf:this.leaf}})},onClick:function(t){if(this.toggleClicked||te(t.target,'[data-pc-section="nodetogglebutton"]')||te(t.target.parentElement,'[data-pc-section="nodetogglebutton"]')){this.toggleClicked=!1;return}this.isCheckboxSelectionMode()?this.node.selectable!=!1&&this.toggleCheckbox():this.$emit("node-click",{originalEvent:t,nodeTouched:this.nodeTouched,node:this.node}),this.nodeTouched=!1},onChildNodeClick:function(t){this.$emit("node-click",t)},onTouchEnd:function(){this.nodeTouched=!0},onKeyDown:function(t){if(this.isSameNode(t))switch(t.code){case"Tab":this.onTabKey(t);break;case"ArrowDown":this.onArrowDown(t);break;case"ArrowUp":this.onArrowUp(t);break;case"ArrowRight":this.onArrowRight(t);break;case"ArrowLeft":this.onArrowLeft(t);break;case"Enter":case"NumpadEnter":case"Space":this.onEnterKey(t);break}},onArrowDown:function(t){var n=t.target.getAttribute("data-pc-section")==="nodetogglebutton"?t.target.closest('[role="treeitem"]'):t.target,l=n.children[1];if(l)this.focusRowChange(n,l.children[0]);else if(n.nextElementSibling)this.focusRowChange(n,n.nextElementSibling);else{var c=this.findNextSiblingOfAncestor(n);c&&this.focusRowChange(n,c)}t.preventDefault()},onArrowUp:function(t){var n=t.target;if(n.previousElementSibling)this.focusRowChange(n,n.previousElementSibling,this.findLastVisibleDescendant(n.previousElementSibling));else{var l=this.getParentNodeElement(n);l&&this.focusRowChange(n,l)}t.preventDefault()},onArrowRight:function(t){var n=this;this.leaf||this.expanded||(t.currentTarget.tabIndex=-1,this.$emit("node-toggle",this.node),this.$nextTick(function(){n.onArrowDown(t)}))},onArrowLeft:function(t){var n=ce(t.currentTarget,'[data-pc-section="nodetogglebutton"]');if(this.level===0&&!this.expanded)return!1;if(this.expanded&&!this.leaf)return n.click(),!1;var l=this.findBeforeClickableNode(t.currentTarget);l&&this.focusRowChange(t.currentTarget,l)},onEnterKey:function(t){this.setTabIndexForSelectionMode(t,this.nodeTouched),this.onClick(t),t.preventDefault()},onTabKey:function(){this.setAllNodesTabIndexes()},setAllNodesTabIndexes:function(){var t=ue(this.$refs.currentNode.closest('[data-pc-section="rootchildren"]'),'[role="treeitem"]'),n=U(t).some(function(c){return c.getAttribute("aria-selected")==="true"||c.getAttribute("aria-checked")==="true"});if(U(t).forEach(function(c){c.tabIndex=-1}),n){var l=U(t).filter(function(c){return c.getAttribute("aria-selected")==="true"||c.getAttribute("aria-checked")==="true"});l[0].tabIndex=0;return}U(t)[0].tabIndex=0},setTabIndexForSelectionMode:function(t,n){if(this.selectionMode!==null){var l=U(ue(this.$refs.currentNode.parentElement,'[role="treeitem"]'));t.currentTarget.tabIndex=n===!1?-1:0,l.every(function(c){return c.tabIndex===-1})&&(l[0].tabIndex=0)}},focusRowChange:function(t,n,l){t.tabIndex="-1",n.tabIndex="0",this.focusNode(l||n)},findBeforeClickableNode:function(t){var n=t.closest("ul").closest("li");if(n){var l=ce(n,"button");return l&&l.style.visibility!=="hidden"?n:this.findBeforeClickableNode(t.previousElementSibling)}return null},toggleCheckbox:function(){var t=this.selectionKeys?Oe({},this.selectionKeys):{},n=!this.checked;this.propagateDown(this.node,n,t),this.$emit("checkbox-change",{node:this.node,check:n,selectionKeys:t})},propagateDown:function(t,n,l){if(n&&t.selectable!=!1?l[t.key]={checked:!0,partialChecked:!1}:delete l[t.key],t.children&&t.children.length){var c=Se(t.children),a;try{for(c.s();!(a=c.n()).done;){var m=a.value;this.propagateDown(m,n,l)}}catch(h){c.e(h)}finally{c.f()}}},propagateUp:function(t){var n=t.check,l=Oe({},t.selectionKeys),c=0,a=!1,m=Se(this.node.children),h;try{for(m.s();!(h=m.n()).done;){var r=h.value;l[r.key]&&l[r.key].checked?c++:l[r.key]&&l[r.key].partialChecked&&(a=!0)}}catch(p){m.e(p)}finally{m.f()}n&&c===this.node.children.length?l[this.node.key]={checked:!0,partialChecked:!1}:(n||delete l[this.node.key],a||c>0&&c!==this.node.children.length?l[this.node.key]={checked:!1,partialChecked:!0}:delete l[this.node.key]),this.$emit("checkbox-change",{node:t.node,check:t.check,selectionKeys:l})},onChildCheckboxChange:function(t){this.$emit("checkbox-change",t)},findNextSiblingOfAncestor:function(t){var n=this.getParentNodeElement(t);return n?n.nextElementSibling?n.nextElementSibling:this.findNextSiblingOfAncestor(n):null},findLastVisibleDescendant:function(t){var n=t.children[1];if(n){var l=n.children[n.children.length-1];return this.findLastVisibleDescendant(l)}else return t},getParentNodeElement:function(t){var n=t.parentElement.parentElement;return te(n,"role")==="treeitem"?n:null},focusNode:function(t){t.focus()},isCheckboxSelectionMode:function(){return this.selectionMode==="checkbox"},isSameNode:function(t){return t.currentTarget&&(t.currentTarget.isSameNode(t.target)||t.currentTarget.isSameNode(t.target.closest('[role="treeitem"]')))}},computed:{hasChildren:function(){return this.node.children&&this.node.children.length>0},expanded:function(){return this.expandedKeys&&this.expandedKeys[this.node.key]===!0},leaf:function(){return this.node.leaf===!1?!1:!(this.node.children&&this.node.children.length)},selectable:function(){return this.node.selectable===!1?!1:this.selectionMode!=null},selected:function(){return this.selectionMode&&this.selectionKeys?this.selectionKeys[this.node.key]===!0:!1},checkboxMode:function(){return this.selectionMode==="checkbox"&&this.node.selectable!==!1},checked:function(){return this.selectionKeys?this.selectionKeys[this.node.key]&&this.selectionKeys[this.node.key].checked:!1},partialChecked:function(){return this.selectionKeys?this.selectionKeys[this.node.key]&&this.selectionKeys[this.node.key].partialChecked:!1},ariaChecked:function(){return this.selectionMode==="single"||this.selectionMode==="multiple"?this.selected:void 0},ariaSelected:function(){return this.checkboxMode?this.checked:void 0}},components:{Checkbox:$n,ChevronDownIcon:je,ChevronRightIcon:Ln,CheckIcon:Nn,MinusIcon:An,SpinnerIcon:Ne},directives:{ripple:Le}},Jn=["aria-label","aria-selected","aria-expanded","aria-setsize","aria-posinset","aria-level","aria-checked","tabindex"],Yn=["data-p-selected","data-p-selectable"];function Zn(e,t,n,l,c,a){var m=T("SpinnerIcon"),h=T("Checkbox"),r=T("TreeNode",!0),p=ln("ripple");return b(),q("li",f({ref:"currentNode",class:e.cx("node"),role:"treeitem","aria-label":a.label(n.node),"aria-selected":a.ariaSelected,"aria-expanded":a.expanded,"aria-setsize":n.node.children?n.node.children.length:0,"aria-posinset":n.index+1,"aria-level":n.level,"aria-checked":a.ariaChecked,tabindex:n.index===0?0:-1,onKeydown:t[4]||(t[4]=function(){return a.onKeyDown&&a.onKeyDown.apply(a,arguments)})},n.level===1?a.getPTOptions("node"):e.ptm("nodeChildren")),[i("div",f({class:e.cx("nodeContent"),onClick:t[2]||(t[2]=function(){return a.onClick&&a.onClick.apply(a,arguments)}),onTouchend:t[3]||(t[3]=function(){return a.onTouchEnd&&a.onTouchEnd.apply(a,arguments)}),style:n.node.style},a.getPTOptions("nodeContent"),{"data-p-selected":a.checkboxMode?a.checked:a.selected,"data-p-selectable":a.selectable}),[on((b(),q("button",f({type:"button",class:e.cx("nodeToggleButton"),onClick:t[0]||(t[0]=function(){return a.toggle&&a.toggle.apply(a,arguments)}),tabindex:"-1"},a.getPTOptions("nodeToggleButton")),[n.node.loading&&n.loadingMode==="icon"?(b(),q(V,{key:0},[n.templates.nodetoggleicon||n.templates.nodetogglericon?(b(),C(M(n.templates.nodetoggleicon||n.templates.nodetogglericon),{key:0,node:n.node,expanded:a.expanded,class:w(e.cx("nodeToggleIcon"))},null,8,["node","expanded","class"])):(b(),C(m,f({key:1,spin:"",class:e.cx("nodeToggleIcon")},a.getPTOptions("nodeToggleIcon")),null,16,["class"]))],64)):(b(),q(V,{key:1},[n.templates.nodetoggleicon||n.templates.togglericon?(b(),C(M(n.templates.nodetoggleicon||n.templates.togglericon),{key:0,node:n.node,expanded:a.expanded,class:w(e.cx("nodeToggleIcon"))},null,8,["node","expanded","class"])):a.expanded?(b(),C(M(n.node.expandedIcon?"span":"ChevronDownIcon"),f({key:1,class:e.cx("nodeToggleIcon")},a.getPTOptions("nodeToggleIcon")),null,16,["class"])):(b(),C(M(n.node.collapsedIcon?"span":"ChevronRightIcon"),f({key:2,class:e.cx("nodeToggleIcon")},a.getPTOptions("nodeToggleIcon")),null,16,["class"]))],64))],16)),[[p]]),a.checkboxMode?(b(),C(h,{key:0,defaultValue:a.checked,binary:!0,indeterminate:a.partialChecked,class:w(e.cx("nodeCheckbox")),tabindex:-1,unstyled:e.unstyled,pt:a.getPTOptions("pcNodeCheckbox"),"data-p-partialchecked":a.partialChecked},{icon:j(function(x){return[n.templates.checkboxicon?(b(),C(M(n.templates.checkboxicon),{key:0,checked:x.checked,partialChecked:a.partialChecked,class:w(x.class)},null,8,["checked","partialChecked","class"])):k("",!0)]}),_:1},8,["defaultValue","indeterminate","class","unstyled","pt","data-p-partialchecked"])):k("",!0),n.templates.nodeicon?(b(),C(M(n.templates.nodeicon),f({key:1,node:n.node,class:[e.cx("nodeIcon")]},a.getPTOptions("nodeIcon")),null,16,["node","class"])):(b(),q("span",f({key:2,class:[e.cx("nodeIcon"),n.node.icon]},a.getPTOptions("nodeIcon")),null,16)),i("span",f({class:e.cx("nodeLabel")},a.getPTOptions("nodeLabel"),{onKeydown:t[1]||(t[1]=qe(function(){},["stop"]))}),[n.templates[n.node.type]||n.templates.default?(b(),C(M(n.templates[n.node.type]||n.templates.default),{key:0,node:n.node,expanded:a.expanded,selected:a.checkboxMode?a.checked:a.selected},null,8,["node","expanded","selected"])):(b(),q(V,{key:1},[Z(S(a.label(n.node)),1)],64))],16)],16,Yn),a.hasChildren&&a.expanded?(b(),q("ul",f({key:0,class:e.cx("nodeChildren"),role:"group"},e.ptm("nodeChildren")),[(b(!0),q(V,null,$(n.node.children,function(x){return b(),C(r,{key:x.key,node:x,templates:n.templates,level:n.level+1,loadingMode:n.loadingMode,expandedKeys:n.expandedKeys,onNodeToggle:a.onChildNodeToggle,onNodeClick:a.onChildNodeClick,selectionMode:n.selectionMode,selectionKeys:n.selectionKeys,onCheckboxChange:a.propagateUp,unstyled:e.unstyled,pt:e.pt},null,8,["node","templates","level","loadingMode","expandedKeys","onNodeToggle","onNodeClick","selectionMode","selectionKeys","onCheckboxChange","unstyled","pt"])}),128))],16)):k("",!0)],16,Jn)}ze.render=Zn;function R(e){"@babel/helpers - typeof";return R=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},R(e)}function se(e,t){var n=typeof Symbol<"u"&&e[Symbol.iterator]||e["@@iterator"];if(!n){if(Array.isArray(e)||(n=Fe(e))||t){n&&(e=n);var l=0,c=function(){};return{s:c,n:function(){return l>=e.length?{done:!0}:{done:!1,value:e[l++]}},e:function(p){throw p},f:c}}throw new TypeError(`Invalid attempt to iterate non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}var a,m=!0,h=!1;return{s:function(){n=n.call(e)},n:function(){var p=n.next();return m=p.done,p},e:function(p){h=!0,a=p},f:function(){try{m||n.return==null||n.return()}finally{if(h)throw a}}}}function Xn(e){return tt(e)||nt(e)||Fe(e)||et()}function et(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function Fe(e,t){if(e){if(typeof e=="string")return fe(e,t);var n={}.toString.call(e).slice(8,-1);return n==="Object"&&e.constructor&&(n=e.constructor.name),n==="Map"||n==="Set"?Array.from(e):n==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?fe(e,t):void 0}}function nt(e){if(typeof Symbol<"u"&&e[Symbol.iterator]!=null||e["@@iterator"]!=null)return Array.from(e)}function tt(e){if(Array.isArray(e))return fe(e)}function fe(e,t){(t==null||t>e.length)&&(t=e.length);for(var n=0,l=Array(t);n<t;n++)l[n]=e[n];return l}function Ve(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var l=Object.getOwnPropertySymbols(e);t&&(l=l.filter(function(c){return Object.getOwnPropertyDescriptor(e,c).enumerable})),n.push.apply(n,l)}return n}function A(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?arguments[t]:{};t%2?Ve(Object(n),!0).forEach(function(l){lt(e,l,n[l])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Ve(Object(n)).forEach(function(l){Object.defineProperty(e,l,Object.getOwnPropertyDescriptor(n,l))})}return e}function lt(e,t,n){return(t=ot(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function ot(e){var t=rt(e,"string");return R(t)=="symbol"?t:t+""}function rt(e,t){if(R(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var l=n.call(e,t);if(R(l)!="object")return l;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var De={name:"Tree",extends:Un,inheritAttrs:!1,emits:["node-expand","node-collapse","update:expandedKeys","update:selectionKeys","node-select","node-unselect","filter"],data:function(){return{d_expandedKeys:this.expandedKeys||{},filterValue:null}},watch:{expandedKeys:function(t){this.d_expandedKeys=t}},methods:{onNodeToggle:function(t){var n=t.key;this.d_expandedKeys[n]?(delete this.d_expandedKeys[n],this.$emit("node-collapse",t)):(this.d_expandedKeys[n]=!0,this.$emit("node-expand",t)),this.d_expandedKeys=A({},this.d_expandedKeys),this.$emit("update:expandedKeys",this.d_expandedKeys)},onNodeClick:function(t){if(this.selectionMode!=null&&t.node.selectable!==!1){var n=t.nodeTouched?!1:this.metaKeySelection,l=n?this.handleSelectionWithMetaKey(t):this.handleSelectionWithoutMetaKey(t);this.$emit("update:selectionKeys",l)}},onCheckboxChange:function(t){this.$emit("update:selectionKeys",t.selectionKeys),t.check?this.$emit("node-select",t.node):this.$emit("node-unselect",t.node)},handleSelectionWithMetaKey:function(t){var n=t.originalEvent,l=t.node,c=n.metaKey||n.ctrlKey,a=this.isNodeSelected(l),m;return a&&c?(this.isSingleSelectionMode()?m={}:(m=A({},this.selectionKeys),delete m[l.key]),this.$emit("node-unselect",l)):(this.isSingleSelectionMode()?m={}:this.isMultipleSelectionMode()&&(m=c?this.selectionKeys?A({},this.selectionKeys):{}:{}),m[l.key]=!0,this.$emit("node-select",l)),m},handleSelectionWithoutMetaKey:function(t){var n=t.node,l=this.isNodeSelected(n),c;return this.isSingleSelectionMode()?l?(c={},this.$emit("node-unselect",n)):(c={},c[n.key]=!0,this.$emit("node-select",n)):l?(c=A({},this.selectionKeys),delete c[n.key],this.$emit("node-unselect",n)):(c=this.selectionKeys?A({},this.selectionKeys):{},c[n.key]=!0,this.$emit("node-select",n)),c},isSingleSelectionMode:function(){return this.selectionMode==="single"},isMultipleSelectionMode:function(){return this.selectionMode==="multiple"},isNodeSelected:function(t){return this.selectionMode&&this.selectionKeys?this.selectionKeys[t.key]===!0:!1},isChecked:function(t){return this.selectionKeys?this.selectionKeys[t.key]&&this.selectionKeys[t.key].checked:!1},isNodeLeaf:function(t){return t.leaf===!1?!1:!(t.children&&t.children.length)},onFilterKeydown:function(t){(t.code==="Enter"||t.code==="NumpadEnter")&&t.preventDefault(),this.$emit("filter",{originalEvent:t,value:t.target.value})},findFilteredNodes:function(t,n){if(t){var l=!1;if(t.children){var c=Xn(t.children);t.children=[];var a=se(c),m;try{for(a.s();!(m=a.n()).done;){var h=m.value,r=A({},h);this.isFilterMatched(r,n)&&(l=!0,t.children.push(r))}}catch(p){a.e(p)}finally{a.f()}}if(l)return!0}},isFilterMatched:function(t,n){var l=n.searchFields,c=n.filterText,a=n.strict,m=!1,h=se(l),r;try{for(h.s();!(r=h.n()).done;){var p=r.value,x=String(tn(t,p)).toLocaleLowerCase(this.filterLocale);x.indexOf(c)>-1&&(m=!0)}}catch(N){h.e(N)}finally{h.f()}return(!m||a&&!this.isNodeLeaf(t))&&(m=this.findFilteredNodes(t,{searchFields:l,filterText:c,strict:a})||m),m}},computed:{filteredValue:function(){var t=[],n=nn(this.filterBy)?[this.filterBy]:this.filterBy.split(","),l=this.filterValue.trim().toLocaleLowerCase(this.filterLocale),c=this.filterMode==="strict",a=se(this.value),m;try{for(a.s();!(m=a.n()).done;){var h=m.value,r=A({},h),p={searchFields:n,filterText:l,strict:c};(c&&(this.findFilteredNodes(r,p)||this.isFilterMatched(r,p))||!c&&(this.isFilterMatched(r,p)||this.findFilteredNodes(r,p)))&&t.push(r)}}catch(x){a.e(x)}finally{a.f()}return t},valueToRender:function(){return this.filterValue&&this.filterValue.trim().length>0?this.filteredValue:this.value}},components:{TreeNode:ze,InputText:K,InputIcon:jn,IconField:Mn,SearchIcon:En,SpinnerIcon:Ne}};function W(e){"@babel/helpers - typeof";return W=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},W(e)}function Te(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var l=Object.getOwnPropertySymbols(e);t&&(l=l.filter(function(c){return Object.getOwnPropertyDescriptor(e,c).enumerable})),n.push.apply(n,l)}return n}function Ie(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?arguments[t]:{};t%2?Te(Object(n),!0).forEach(function(l){it(e,l,n[l])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Te(Object(n)).forEach(function(l){Object.defineProperty(e,l,Object.getOwnPropertyDescriptor(n,l))})}return e}function it(e,t,n){return(t=at(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function at(e){var t=st(e,"string");return W(t)=="symbol"?t:t+""}function st(e,t){if(W(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var l=n.call(e,t);if(W(l)!="object")return l;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var dt=["aria-labelledby","aria-label"];function ct(e,t,n,l,c,a){var m=T("SpinnerIcon"),h=T("InputText"),r=T("SearchIcon"),p=T("InputIcon"),x=T("IconField"),N=T("TreeNode");return b(),q("div",f({class:e.cx("root")},e.ptmi("root")),[e.loading&&e.loadingMode==="mask"?(b(),q("div",f({key:0,class:e.cx("mask")},e.ptm("mask")),[O(e.$slots,"loadingicon",{class:w(e.cx("loadingIcon"))},function(){return[e.loadingIcon?(b(),q("i",f({key:0,class:[e.cx("loadingIcon"),"pi-spin",e.loadingIcon]},e.ptm("loadingIcon")),null,16)):(b(),C(m,f({key:1,spin:"",class:e.cx("loadingIcon")},e.ptm("loadingIcon")),null,16,["class"]))]})],16)):k("",!0),e.filter?(b(),C(x,{key:1,unstyled:e.unstyled,pt:Ie(Ie({},e.ptm("pcFilter")),e.ptm("pcFilterContainer")),class:w(e.cx("pcFilterContainer"))},{default:j(function(){return[y(h,{modelValue:c.filterValue,"onUpdate:modelValue":t[0]||(t[0]=function(P){return c.filterValue=P}),autocomplete:"off",class:w(e.cx("pcFilterInput")),placeholder:e.filterPlaceholder,unstyled:e.unstyled,onKeydown:a.onFilterKeydown,pt:e.ptm("pcFilterInput")},null,8,["modelValue","class","placeholder","unstyled","onKeydown","pt"]),y(p,{unstyled:e.unstyled,pt:e.ptm("pcFilterIconContainer")},{default:j(function(){return[O(e.$slots,e.$slots.filtericon?"filtericon":"searchicon",{class:w(e.cx("filterIcon"))},function(){return[y(r,f({class:e.cx("filterIcon")},e.ptm("filterIcon")),null,16,["class"])]})]}),_:3},8,["unstyled","pt"])]}),_:3},8,["unstyled","pt","class"])):k("",!0),i("div",f({class:e.cx("wrapper"),style:{maxHeight:e.scrollHeight}},e.ptm("wrapper")),[O(e.$slots,"header",{value:e.value,expandedKeys:e.expandedKeys,selectionKeys:e.selectionKeys}),i("ul",f({class:e.cx("rootChildren"),role:"tree","aria-labelledby":e.ariaLabelledby,"aria-label":e.ariaLabel},e.ptm("rootChildren")),[(b(!0),q(V,null,$(a.valueToRender,function(P,X){return b(),C(N,{key:P.key,node:P,templates:e.$slots,level:e.level+1,index:X,expandedKeys:c.d_expandedKeys,onNodeToggle:a.onNodeToggle,onNodeClick:a.onNodeClick,selectionMode:e.selectionMode,selectionKeys:e.selectionKeys,onCheckboxChange:a.onCheckboxChange,loadingMode:e.loadingMode,unstyled:e.unstyled,pt:e.pt},null,8,["node","templates","level","index","expandedKeys","onNodeToggle","onNodeClick","selectionMode","selectionKeys","onCheckboxChange","loadingMode","unstyled","pt"])}),128))],16,dt),O(e.$slots,"footer",{value:e.value,expandedKeys:e.expandedKeys,selectionKeys:e.selectionKeys})],16)],16)}De.render=ct;var ut=function(t){var n=t.dt;return`
.p-treeselect {
    display: inline-flex;
    cursor: pointer;
    position: relative;
    user-select: none;
    background: `.concat(n("treeselect.background"),`;
    border: 1px solid `).concat(n("treeselect.border.color"),`;
    transition: background `).concat(n("treeselect.transition.duration"),", color ").concat(n("treeselect.transition.duration"),", border-color ").concat(n("treeselect.transition.duration"),", outline-color ").concat(n("treeselect.transition.duration"),", box-shadow ").concat(n("treeselect.transition.duration"),`;
    border-radius: `).concat(n("treeselect.border.radius"),`;
    outline-color: transparent;
    box-shadow: `).concat(n("treeselect.shadow"),`;
}

.p-treeselect:not(.p-disabled):hover {
    border-color: `).concat(n("treeselect.hover.border.color"),`;
}

.p-treeselect:not(.p-disabled).p-focus {
    border-color: `).concat(n("treeselect.focus.border.color"),`;
    box-shadow: `).concat(n("treeselect.focus.ring.shadow"),`;
    outline: `).concat(n("treeselect.focus.ring.width")," ").concat(n("treeselect.focus.ring.style")," ").concat(n("treeselect.focus.ring.color"),`;
    outline-offset: `).concat(n("treeselect.focus.ring.offset"),`;
}

.p-treeselect.p-variant-filled {
    background: `).concat(n("treeselect.filled.background"),`;
}

.p-treeselect.p-variant-filled:not(.p-disabled):hover {
    background: `).concat(n("treeselect.filled.hover.background"),`;
}

.p-treeselect.p-variant-filled.p-focus {
    background: `).concat(n("treeselect.filled.focus.background"),`;
}

.p-treeselect.p-invalid {
    border-color: `).concat(n("treeselect.invalid.border.color"),`;
}

.p-treeselect.p-disabled {
    opacity: 1;
    background: `).concat(n("treeselect.disabled.background"),`;
}

.p-treeselect-clear-icon {
    position: absolute;
    top: 50%;
    margin-top: -0.5rem;
    color: `).concat(n("treeselect.clear.icon.color"),`;
    inset-inline-end: `).concat(n("treeselect.dropdown.width"),`;
}

.p-treeselect-dropdown {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: transparent;
    color: `).concat(n("treeselect.dropdown.color"),`;
    width: `).concat(n("treeselect.dropdown.width"),`;
    border-start-end-radius: `).concat(n("border.radius.md"),`;
    border-end-end-radius: `).concat(n("border.radius.md"),`;
}

.p-treeselect-label-container {
    overflow: hidden;
    flex: 1 1 auto;
    cursor: pointer;
}

.p-treeselect-label {
    display: flex;
    align-items: center;
    gap: calc(`).concat(n("treeselect.padding.y"),` / 2);
    white-space: nowrap;
    cursor: pointer;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: `).concat(n("treeselect.padding.y")," ").concat(n("treeselect.padding.x"),`;
    color: `).concat(n("treeselect.color"),`;
}

.p-treeselect-label.p-placeholder {
    color: `).concat(n("treeselect.placeholder.color"),`;
}

.p-treeselect.p-invalid .p-treeselect-label.p-placeholder {
    color: `).concat(n("treeselect.invalid.placeholder.color"),`;
}

.p-treeselect.p-disabled .p-treeselect-label {
    color: `).concat(n("treeselect.disabled.color"),`;
}

.p-treeselect-label-empty {
    overflow: hidden;
    visibility: hidden;
}

.p-treeselect .p-treeselect-overlay {
    min-width: 100%;
}

.p-treeselect-overlay {
    position: absolute;
    top: 0;
    left: 0;
    background: `).concat(n("treeselect.overlay.background"),`;
    color: `).concat(n("treeselect.overlay.color"),`;
    border: 1px solid `).concat(n("treeselect.overlay.border.color"),`;
    border-radius: `).concat(n("treeselect.overlay.border.radius"),`;
    box-shadow: `).concat(n("treeselect.overlay.shadow"),`;
    overflow: hidden;
}

.p-treeselect-tree-container {
    overflow: auto;
}

.p-treeselect-empty-message {
    padding: `).concat(n("treeselect.empty.message.padding"),`;
    background: transparent;
}

.p-treeselect-fluid {
    display: flex;
}

.p-treeselect-overlay .p-tree {
    padding: `).concat(n("treeselect.tree.padding"),`;
}

.p-treeselect-overlay .p-tree-loading {
    min-height: 3rem;
}

.p-treeselect-label .p-chip {
    padding-block-start: calc(`).concat(n("treeselect.padding.y"),` / 2);
    padding-block-end: calc(`).concat(n("treeselect.padding.y"),` / 2);
    border-radius: `).concat(n("treeselect.chip.border.radius"),`;
}

.p-treeselect-label:has(.p-chip) {
    padding: calc(`).concat(n("treeselect.padding.y")," / 2) calc(").concat(n("treeselect.padding.x"),` / 2);
}

.p-treeselect-sm .p-treeselect-label {
    font-size: `).concat(n("treeselect.sm.font.size"),`;
    padding-block: `).concat(n("treeselect.sm.padding.y"),`;
    padding-inline: `).concat(n("treeselect.sm.padding.x"),`;
}

.p-treeselect-sm .p-treeselect-dropdown .p-icon {
    font-size: `).concat(n("treeselect.sm.font.size"),`;
    width: `).concat(n("treeselect.sm.font.size"),`;
    height: `).concat(n("treeselect.sm.font.size"),`;
}

.p-treeselect-lg .p-treeselect-label {
    font-size: `).concat(n("treeselect.lg.font.size"),`;
    padding-block: `).concat(n("treeselect.lg.padding.y"),`;
    padding-inline: `).concat(n("treeselect.lg.padding.x"),`;
}

.p-treeselect-lg .p-treeselect-dropdown .p-icon {
    font-size: `).concat(n("treeselect.lg.font.size"),`;
    width: `).concat(n("treeselect.lg.font.size"),`;
    height: `).concat(n("treeselect.lg.font.size"),`;
}
`)},pt={root:function(t){var n=t.props;return{position:n.appendTo==="self"?"relative":void 0}}},ft={root:function(t){var n=t.instance,l=t.props;return["p-treeselect p-component p-inputwrapper",{"p-treeselect-display-chip":l.display==="chip","p-disabled":l.disabled,"p-invalid":n.$invalid,"p-focus":n.focused,"p-variant-filled":n.$variant==="filled","p-inputwrapper-filled":n.$filled,"p-inputwrapper-focus":n.focused||n.overlayVisible,"p-treeselect-open":n.overlayVisible,"p-treeselect-fluid":n.$fluid,"p-treeselect-sm p-inputfield-sm":l.size==="small","p-treeselect-lg p-inputfield-lg":l.size==="large"}]},labelContainer:"p-treeselect-label-container",label:function(t){var n=t.instance,l=t.props;return["p-treeselect-label",{"p-placeholder":n.label===l.placeholder,"p-treeselect-label-empty":!l.placeholder&&n.emptyValue}]},clearIcon:"p-treeselect-clear-icon",chip:"p-treeselect-chip-item",pcChip:"p-treeselect-chip",dropdown:"p-treeselect-dropdown",dropdownIcon:"p-treeselect-dropdown-icon",panel:"p-treeselect-overlay p-component",treeContainer:"p-treeselect-tree-container",emptyMessage:"p-treeselect-empty-message"},bt=he.extend({name:"treeselect",theme:ut,classes:ft,inlineStyles:pt}),mt={name:"BaseTreeSelect",extends:wn,props:{options:Array,scrollHeight:{type:String,default:"20rem"},placeholder:{type:String,default:null},tabindex:{type:Number,default:null},selectionMode:{type:String,default:"single"},selectedItemsLabel:{type:String,default:null},maxSelectedLabels:{type:Number,default:null},appendTo:{type:[String,Object],default:"body"},emptyMessage:{type:String,default:null},display:{type:String,default:"comma"},metaKeySelection:{type:Boolean,default:!1},loading:{type:Boolean,default:!1},loadingIcon:{type:String,default:void 0},loadingMode:{type:String,default:"mask"},showClear:{type:Boolean,default:!1},clearIcon:{type:String,default:void 0},filter:{type:Boolean,default:!1},filterBy:{type:[String,Function],default:"label"},filterMode:{type:String,default:"lenient"},filterPlaceholder:{type:String,default:null},filterLocale:{type:String,default:void 0},inputId:{type:String,default:null},inputClass:{type:[String,Object],default:null},inputStyle:{type:Object,default:null},inputProps:{type:null,default:null},panelClass:{type:[String,Object],default:null},panelProps:{type:null,default:null},ariaLabelledby:{type:String,default:null},ariaLabel:{type:String,default:null},expandedKeys:{type:null,default:null}},style:bt,provide:function(){return{$pcTreeSelect:this,$parentInstance:this}}};function Q(e){"@babel/helpers - typeof";return Q=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},Q(e)}function Ke(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var l=Object.getOwnPropertySymbols(e);t&&(l=l.filter(function(c){return Object.getOwnPropertyDescriptor(e,c).enumerable})),n.push.apply(n,l)}return n}function de(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?arguments[t]:{};t%2?Ke(Object(n),!0).forEach(function(l){ht(e,l,n[l])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Ke(Object(n)).forEach(function(l){Object.defineProperty(e,l,Object.getOwnPropertyDescriptor(n,l))})}return e}function ht(e,t,n){return(t=qt(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function qt(e){var t=yt(e,"string");return Q(t)=="symbol"?t:t+""}function yt(e,t){if(Q(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var l=n.call(e,t);if(Q(l)!="object")return l;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}function B(e,t){var n=typeof Symbol<"u"&&e[Symbol.iterator]||e["@@iterator"];if(!n){if(Array.isArray(e)||(n=_e(e))||t){n&&(e=n);var l=0,c=function(){};return{s:c,n:function(){return l>=e.length?{done:!0}:{done:!1,value:e[l++]}},e:function(p){throw p},f:c}}throw new TypeError(`Invalid attempt to iterate non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}var a,m=!0,h=!1;return{s:function(){n=n.call(e)},n:function(){var p=n.next();return m=p.done,p},e:function(p){h=!0,a=p},f:function(){try{m||n.return==null||n.return()}finally{if(h)throw a}}}}function gt(e){return wt(e)||kt(e)||_e(e)||vt()}function vt(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function _e(e,t){if(e){if(typeof e=="string")return be(e,t);var n={}.toString.call(e).slice(8,-1);return n==="Object"&&e.constructor&&(n=e.constructor.name),n==="Map"||n==="Set"?Array.from(e):n==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?be(e,t):void 0}}function kt(e){if(typeof Symbol<"u"&&e[Symbol.iterator]!=null||e["@@iterator"]!=null)return Array.from(e)}function wt(e){if(Array.isArray(e))return be(e)}function be(e,t){(t==null||t>e.length)&&(t=e.length);for(var n=0,l=Array(t);n<t;n++)l[n]=e[n];return l}var Ue={name:"TreeSelect",extends:mt,inheritAttrs:!1,emits:["before-show","before-hide","change","show","hide","node-select","node-unselect","node-expand","node-collapse","focus","blur","update:expandedKeys"],inject:{$pcFluid:{default:null}},data:function(){return{id:this.$attrs.id,focused:!1,overlayVisible:!1,d_expandedKeys:this.expandedKeys||{}}},watch:{"$attrs.id":function(t){this.id=t||xe()},modelValue:{handler:function(){this.selfChange||this.updateTreeState(),this.selfChange=!1},immediate:!0},options:function(){this.updateTreeState()},expandedKeys:function(t){this.d_expandedKeys=t}},outsideClickListener:null,resizeListener:null,scrollHandler:null,overlay:null,selfChange:!1,selfClick:!1,beforeUnmount:function(){this.unbindOutsideClickListener(),this.unbindResizeListener(),this.scrollHandler&&(this.scrollHandler.destroy(),this.scrollHandler=null),this.overlay&&(ae.clear(this.overlay),this.overlay=null)},mounted:function(){this.id=this.id||xe(),this.updateTreeState()},methods:{show:function(){this.$emit("before-show"),this.overlayVisible=!0},hide:function(){this.$emit("before-hide"),this.overlayVisible=!1,this.$refs.focusInput.focus()},onFocus:function(t){this.focused=!0,this.$emit("focus",t)},onBlur:function(t){var n,l;this.focused=!1,this.$emit("blur",t),(n=(l=this.formField).onBlur)===null||n===void 0||n.call(l)},onClick:function(t){this.disabled||t.target.tagName==="INPUT"||t.target.getAttribute("data-pc-section")==="clearicon"||t.target.closest('[data-pc-section="clearicon"]')||(!this.overlay||!this.overlay.contains(t.target))&&(this.overlayVisible?this.hide():this.show(),F(this.$refs.focusInput))},onClearClick:function(){this.onSelectionChange(null)},onSelectionChange:function(t){this.selfChange=!0,this.writeValue(t),this.$emit("change",t)},onNodeSelect:function(t){this.$emit("node-select",t),this.selectionMode==="single"&&this.hide()},onNodeUnselect:function(t){this.$emit("node-unselect",t)},onNodeToggle:function(t){this.d_expandedKeys=t,this.$emit("update:expandedKeys",this.d_expandedKeys)},getSelectedItemsLabel:function(){var t=/{(.*?)}/,n=this.selectedItemsLabel||this.$primevue.config.locale.selectionMessage;return t.test(n)?n.replace(n.match(t)[0],Object.keys(this.d_value).length+""):n},onFirstHiddenFocus:function(t){var n=t.relatedTarget===this.$refs.focusInput?fn(this.overlay,':not([data-p-hidden-focusable="true"])'):this.$refs.focusInput;F(n)},onLastHiddenFocus:function(t){var n=t.relatedTarget===this.$refs.focusInput?pn(this.overlay,':not([data-p-hidden-focusable="true"])'):this.$refs.focusInput;F(n)},onKeyDown:function(t){switch(t.code){case"ArrowDown":this.onArrowDownKey(t);break;case"Space":case"Enter":case"NumpadEnter":this.onEnterKey(t);break;case"Escape":this.onEscapeKey(t);break;case"Tab":this.onTabKey(t);break}},onArrowDownKey:function(t){var n=this;this.overlayVisible||(this.show(),this.$nextTick(function(){var l=ue(n.$refs.tree.$el,'[data-pc-section="treeitem"]'),c=gt(l).find(function(a){return a.getAttribute("tabindex")==="0"});F(c)}),t.preventDefault())},onEnterKey:function(t){this.overlayVisible?this.hide():this.onArrowDownKey(t),t.preventDefault()},onEscapeKey:function(t){this.overlayVisible&&(this.hide(),t.preventDefault())},onTabKey:function(t){var n=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1;n||this.overlayVisible&&this.hasFocusableElements()&&(F(this.$refs.firstHiddenFocusableElementOnOverlay),t.preventDefault())},hasFocusableElements:function(){return ke(this.overlay,':not([data-p-hidden-focusable="true"])').length>0},onOverlayEnter:function(t){ae.set("overlay",t,this.$primevue.config.zIndex.overlay),un(t,{position:"absolute",top:"0",left:"0"}),this.alignOverlay(),this.focus()},onOverlayAfterEnter:function(){this.bindOutsideClickListener(),this.bindScrollListener(),this.bindResizeListener(),this.scrollValueInView(),this.$emit("show")},onOverlayLeave:function(){this.unbindOutsideClickListener(),this.unbindScrollListener(),this.unbindResizeListener(),this.$emit("hide"),this.overlay=null},onOverlayAfterLeave:function(t){ae.clear(t)},focus:function(){var t=ke(this.overlay);t&&t.length>0&&t[0].focus()},alignOverlay:function(){this.appendTo==="self"?sn(this.overlay,this.$el):(this.overlay.style.minWidth=dn(this.$el)+"px",cn(this.overlay,this.$el))},bindOutsideClickListener:function(){var t=this;this.outsideClickListener||(this.outsideClickListener=function(n){t.overlayVisible&&!t.selfClick&&t.isOutsideClicked(n)&&t.hide(),t.selfClick=!1},document.addEventListener("click",this.outsideClickListener))},unbindOutsideClickListener:function(){this.outsideClickListener&&(document.removeEventListener("click",this.outsideClickListener),this.outsideClickListener=null)},bindScrollListener:function(){var t=this;this.scrollHandler||(this.scrollHandler=new In(this.$refs.container,function(){t.overlayVisible&&t.hide()})),this.scrollHandler.bindScrollListener()},unbindScrollListener:function(){this.scrollHandler&&this.scrollHandler.unbindScrollListener()},bindResizeListener:function(){var t=this;this.resizeListener||(this.resizeListener=function(){t.overlayVisible&&!an()&&t.hide()},window.addEventListener("resize",this.resizeListener))},unbindResizeListener:function(){this.resizeListener&&(window.removeEventListener("resize",this.resizeListener),this.resizeListener=null)},isOutsideClicked:function(t){return!(this.$el.isSameNode(t.target)||this.$el.contains(t.target)||this.overlay&&this.overlay.contains(t.target))},overlayRef:function(t){this.overlay=t},onOverlayClick:function(t){Tn.emit("overlay-click",{originalEvent:t,target:this.$el}),this.selfClick=!0},onOverlayKeydown:function(t){t.code==="Escape"&&this.hide()},findSelectedNodes:function(t,n,l){if(t){if(this.isSelected(t,n)&&(l.push(t),delete n[t.key]),Object.keys(n).length&&t.children){var c=B(t.children),a;try{for(c.s();!(a=c.n()).done;){var m=a.value;this.findSelectedNodes(m,n,l)}}catch(x){c.e(x)}finally{c.f()}}}else{var h=B(this.options),r;try{for(h.s();!(r=h.n()).done;){var p=r.value;this.findSelectedNodes(p,n,l)}}catch(x){h.e(x)}finally{h.f()}}},isSelected:function(t,n){return this.selectionMode==="checkbox"?n[t.key]&&n[t.key].checked:n[t.key]},updateTreeState:function(){var t=de({},this.d_value);t&&this.options&&this.updateTreeBranchState(null,null,t)},updateTreeBranchState:function(t,n,l){if(t){if(this.isSelected(t,l)&&(this.expandPath(n),delete l[t.key]),Object.keys(l).length&&t.children){var c=B(t.children),a;try{for(c.s();!(a=c.n()).done;){var m=a.value;n.push(t.key),this.updateTreeBranchState(m,n,l)}}catch(x){c.e(x)}finally{c.f()}}}else{var h=B(this.options),r;try{for(h.s();!(r=h.n()).done;){var p=r.value;this.updateTreeBranchState(p,[],l)}}catch(x){h.e(x)}finally{h.f()}}},expandPath:function(t){if(t.length>0){var n=B(t),l;try{for(n.s();!(l=n.n()).done;){var c=l.value;this.d_expandedKeys[c]=!0}}catch(a){n.e(a)}finally{n.f()}this.d_expandedKeys=de({},this.d_expandedKeys),this.$emit("update:expandedKeys",this.d_expandedKeys)}},scrollValueInView:function(){if(this.overlay){var t=ce(this.overlay,'[data-p-selected="true"]');t&&t.scrollIntoView({block:"nearest",inline:"start"})}}},computed:{selectedNodes:function(){var t=[];if(this.d_value&&this.options){var n=de({},this.d_value);this.findSelectedNodes(null,n,t)}return t},label:function(){var t=this.selectedNodes,n;return t.length?le(this.maxSelectedLabels)&&t.length>this.maxSelectedLabels?n=this.getSelectedItemsLabel():n=t.map(function(l){return l.label}).join(", "):n=this.placeholder,n},chipSelectedItems:function(){return le(this.maxSelectedLabels)&&this.d_value&&Object.keys(this.d_value).length>this.maxSelectedLabels},emptyMessageText:function(){return this.emptyMessage||this.$primevue.config.locale.emptyMessage},emptyValue:function(){return!this.$filled},emptyOptions:function(){return!this.options||this.options.length===0},listId:function(){return this.id+"_list"},hasFluid:function(){return rn(this.fluid)?!!this.$pcFluid:this.fluid},isClearIconVisible:function(){return this.showClear&&this.d_value!=null&&le(this.options)}},components:{TSTree:De,Chip:Pn,Portal:Vn,ChevronDownIcon:je,TimesIcon:Kn},directives:{ripple:Le}};function G(e){"@babel/helpers - typeof";return G=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},G(e)}function Pe(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var l=Object.getOwnPropertySymbols(e);t&&(l=l.filter(function(c){return Object.getOwnPropertyDescriptor(e,c).enumerable})),n.push.apply(n,l)}return n}function Y(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?arguments[t]:{};t%2?Pe(Object(n),!0).forEach(function(l){xt(e,l,n[l])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Pe(Object(n)).forEach(function(l){Object.defineProperty(e,l,Object.getOwnPropertyDescriptor(n,l))})}return e}function xt(e,t,n){return(t=St(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function St(e){var t=Ct(e,"string");return G(t)=="symbol"?t:t+""}function Ct(e,t){if(G(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var l=n.call(e,t);if(G(l)!="object")return l;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var Ot=["id","disabled","tabindex","aria-labelledby","aria-label","aria-expanded","aria-controls"],Vt={key:0},Tt=["aria-expanded"];function It(e,t,n,l,c,a){var m=T("Chip"),h=T("TSTree"),r=T("Portal");return b(),q("div",f({ref:"container",class:e.cx("root"),style:e.sx("root"),onClick:t[10]||(t[10]=function(){return a.onClick&&a.onClick.apply(a,arguments)})},e.ptmi("root")),[i("div",f({class:"p-hidden-accessible"},e.ptm("hiddenInputContainer"),{"data-p-hidden-accessible":!0}),[i("input",f({ref:"focusInput",id:e.inputId,type:"text",role:"combobox",class:e.inputClass,style:e.inputStyle,readonly:"",disabled:e.disabled,tabindex:e.disabled?-1:e.tabindex,"aria-labelledby":e.ariaLabelledby,"aria-label":e.ariaLabel,"aria-haspopup":"tree","aria-expanded":c.overlayVisible,"aria-controls":a.listId,onFocus:t[0]||(t[0]=function(p){return a.onFocus(p)}),onBlur:t[1]||(t[1]=function(p){return a.onBlur(p)}),onKeydown:t[2]||(t[2]=function(p){return a.onKeyDown(p)})},Y(Y({},e.inputProps),e.ptm("hiddenInput"))),null,16,Ot)],16),i("div",f({class:e.cx("labelContainer")},e.ptm("labelContainer")),[i("div",f({class:e.cx("label")},e.ptm("label")),[O(e.$slots,"value",{value:a.selectedNodes,placeholder:e.placeholder},function(){return[e.display==="comma"?(b(),q(V,{key:0},[Z(S(a.label||"empty"),1)],64)):e.display==="chip"?(b(),q(V,{key:1},[a.chipSelectedItems?(b(),q("span",Vt,S(a.label),1)):(b(),q(V,{key:1},[(b(!0),q(V,null,$(a.selectedNodes,function(p){return b(),q("div",f({key:p.key,class:e.cx("chipItem"),ref_for:!0},e.ptm("chipItem")),[y(m,{class:w(e.cx("pcChip")),label:p.label,unstyled:e.unstyled,pt:e.ptm("pcChip")},null,8,["class","label","unstyled","pt"])],16)}),128)),a.emptyValue?(b(),q(V,{key:0},[Z(S(e.placeholder||"empty"),1)],64)):k("",!0)],64))],64)):k("",!0)]})],16)],16),a.isClearIconVisible?O(e.$slots,"clearicon",{key:0,class:w(e.cx("clearIcon")),clearCallback:a.onClearClick},function(){return[(b(),C(M(e.clearIcon?"i":"TimesIcon"),f({ref:"clearIcon",class:[e.cx("clearIcon"),e.clearIcon],onClick:a.onClearClick},e.ptm("clearIcon"),{"data-pc-section":"clearicon"}),null,16,["class","onClick"]))]}):k("",!0),i("div",f({class:e.cx("dropdown"),role:"button","aria-haspopup":"tree","aria-expanded":c.overlayVisible},e.ptm("dropdown")),[O(e.$slots,e.$slots.dropdownicon?"dropdownicon":"triggericon",{class:w(e.cx("dropdownIcon"))},function(){return[(b(),C(M("ChevronDownIcon"),f({class:e.cx("dropdownIcon")},e.ptm("dropdownIcon")),null,16,["class"]))]})],16,Tt),y(r,{appendTo:e.appendTo},{default:j(function(){return[y(bn,f({name:"p-connected-overlay",onEnter:a.onOverlayEnter,onAfterEnter:a.onOverlayAfterEnter,onLeave:a.onOverlayLeave,onAfterLeave:a.onOverlayAfterLeave},e.ptm("transition")),{default:j(function(){return[c.overlayVisible?(b(),q("div",f({key:0,ref:a.overlayRef,onClick:t[8]||(t[8]=function(){return a.onOverlayClick&&a.onOverlayClick.apply(a,arguments)}),class:[e.cx("panel"),e.panelClass],onKeydown:t[9]||(t[9]=function(){return a.onOverlayKeydown&&a.onOverlayKeydown.apply(a,arguments)})},Y(Y({},e.panelProps),e.ptm("panel"))),[i("span",f({ref:"firstHiddenFocusableElementOnOverlay",role:"presentation",class:"p-hidden-accessible p-hidden-focusable",tabindex:0,onFocus:t[3]||(t[3]=function(){return a.onFirstHiddenFocus&&a.onFirstHiddenFocus.apply(a,arguments)})},e.ptm("hiddenFirstFocusableEl"),{"data-p-hidden-accessible":!0,"data-p-hidden-focusable":!0}),null,16),O(e.$slots,"header",{value:e.d_value,options:e.options}),i("div",f({class:e.cx("treeContainer"),style:{"max-height":e.scrollHeight}},e.ptm("treeContainer")),[y(h,{ref:"tree",id:a.listId,value:e.options,selectionMode:e.selectionMode,loading:e.loading,loadingIcon:e.loadingIcon,loadingMode:e.loadingMode,filter:e.filter,filterBy:e.filterBy,filterMode:e.filterMode,filterPlaceholder:e.filterPlaceholder,filterLocale:e.filterLocale,"onUpdate:selectionKeys":a.onSelectionChange,selectionKeys:e.d_value,expandedKeys:c.d_expandedKeys,"onUpdate:expandedKeys":a.onNodeToggle,metaKeySelection:e.metaKeySelection,onNodeExpand:t[4]||(t[4]=function(p){return e.$emit("node-expand",p)}),onNodeCollapse:t[5]||(t[5]=function(p){return e.$emit("node-collapse",p)}),onNodeSelect:a.onNodeSelect,onNodeUnselect:a.onNodeUnselect,onClick:t[6]||(t[6]=qe(function(){},["stop"])),level:0,unstyled:e.unstyled,pt:e.ptm("pcTree")},mn({_:2},[e.$slots.option?{name:"default",fn:j(function(p){return[O(e.$slots,"option",{node:p.node,expanded:p.expanded,selected:p.selected})]}),key:"0"}:void 0,e.$slots.itemtoggleicon?{name:"toggleicon",fn:j(function(p){return[O(e.$slots,"itemtoggleicon",{node:p.node,expanded:p.expanded,class:w(p.class)})]}),key:"1"}:e.$slots.itemtogglericon?{name:"togglericon",fn:j(function(p){return[O(e.$slots,"itemtogglericon",{node:p.node,expanded:p.expanded,class:w(p.class)})]}),key:"2"}:void 0,e.$slots.itemcheckboxicon?{name:"checkboxicon",fn:j(function(p){return[O(e.$slots,"itemcheckboxicon",{checked:p.checked,partialChecked:p.partialChecked,class:w(p.class)})]}),key:"3"}:void 0]),1032,["id","value","selectionMode","loading","loadingIcon","loadingMode","filter","filterBy","filterMode","filterPlaceholder","filterLocale","onUpdate:selectionKeys","selectionKeys","expandedKeys","onUpdate:expandedKeys","metaKeySelection","onNodeSelect","onNodeUnselect","unstyled","pt"]),a.emptyOptions&&!e.loading?(b(),q("div",f({key:0,class:e.cx("emptyMessage")},e.ptm("emptyMessage")),[O(e.$slots,"empty",{},function(){return[Z(S(a.emptyMessageText),1)]})],16)):k("",!0)],16),O(e.$slots,"footer",{value:e.d_value,options:e.options}),i("span",f({ref:"lastHiddenFocusableElementOnOverlay",role:"presentation",class:"p-hidden-accessible p-hidden-focusable",tabindex:0,onFocus:t[7]||(t[7]=function(){return a.onLastHiddenFocus&&a.onLastHiddenFocus.apply(a,arguments)})},e.ptm("hiddenLastFocusableEl"),{"data-p-hidden-accessible":!0,"data-p-hidden-focusable":!0}),null,16)],16)):k("",!0)]}),_:3},16,["onEnter","onAfterEnter","onLeave","onAfterLeave"])]}),_:3},8,["appendTo"])],16)}Ue.render=It;var Kt=function(t){var n=t.dt;return`
/*!
* Quill Editor v1.3.3
* https://quilljs.com/
* Copyright (c) 2014, Jason Chen
* Copyright (c) 2013, salesforce.com
*/
.ql-container {
    box-sizing: border-box;
    font-family: Helvetica, Arial, sans-serif;
    font-size: 13px;
    height: 100%;
    margin: 0;
    position: relative;
}
.ql-container.ql-disabled .ql-tooltip {
    visibility: hidden;
}
.ql-container.ql-disabled .ql-editor ul[data-checked] > li::before {
    pointer-events: none;
}
.ql-clipboard {
    inset-inline-start: -100000px;
    height: 1px;
    overflow-y: hidden;
    position: absolute;
    top: 50%;
}
.ql-clipboard p {
    margin: 0;
    padding: 0;
}
.ql-editor {
    box-sizing: border-box;
    line-height: 1.42;
    height: 100%;
    outline: none;
    overflow-y: auto;
    padding: 12px 15px;
    tab-size: 4;
    -moz-tab-size: 4;
    text-align: left;
    white-space: pre-wrap;
    word-wrap: break-word;
}
.ql-editor > * {
    cursor: text;
}
.ql-editor p,
.ql-editor ol,
.ql-editor ul,
.ql-editor pre,
.ql-editor blockquote,
.ql-editor h1,
.ql-editor h2,
.ql-editor h3,
.ql-editor h4,
.ql-editor h5,
.ql-editor h6 {
    margin: 0;
    padding: 0;
    counter-reset: list-1 list-2 list-3 list-4 list-5 list-6 list-7 list-8 list-9;
}
.ql-editor ol,
.ql-editor ul {
    padding-inline-start: 1.5rem;
}
.ql-editor ol > li,
.ql-editor ul > li {
    list-style-type: none;
}
.ql-editor ul > li::before {
    content: '\\2022';
}
.ql-editor ul[data-checked='true'],
.ql-editor ul[data-checked='false'] {
    pointer-events: none;
}
.ql-editor ul[data-checked='true'] > li *,
.ql-editor ul[data-checked='false'] > li * {
    pointer-events: all;
}
.ql-editor ul[data-checked='true'] > li::before,
.ql-editor ul[data-checked='false'] > li::before {
    color: #777;
    cursor: pointer;
    pointer-events: all;
}
.ql-editor ul[data-checked='true'] > li::before {
    content: '\\2611';
}
.ql-editor ul[data-checked='false'] > li::before {
    content: '\\2610';
}
.ql-editor li::before {
    display: inline-block;
    white-space: nowrap;
    width: 1.2rem;
}
.ql-editor li:not(.ql-direction-rtl)::before {
    margin-inline-start: -1.5rem;
    margin-inline-end: 0.3rem;
    text-align: right;
}
.ql-editor li.ql-direction-rtl::before {
    margin-inline-start: 0.3rem;
    margin-inline-end: -1.5rem;
}
.ql-editor ol li:not(.ql-direction-rtl),
.ql-editor ul li:not(.ql-direction-rtl) {
    padding-inline-start: 1.5rem;
}
.ql-editor ol li.ql-direction-rtl,
.ql-editor ul li.ql-direction-rtl {
    padding-inline-end: 1.5rem;
}
.ql-editor ol li {
    counter-reset: list-1 list-2 list-3 list-4 list-5 list-6 list-7 list-8 list-9;
    counter-increment: list-0;
}
.ql-editor ol li:before {
    content: counter(list-0, decimal) '. ';
}
.ql-editor ol li.ql-indent-1 {
    counter-increment: list-1;
}
.ql-editor ol li.ql-indent-1:before {
    content: counter(list-1, lower-alpha) '. ';
}
.ql-editor ol li.ql-indent-1 {
    counter-reset: list-2 list-3 list-4 list-5 list-6 list-7 list-8 list-9;
}
.ql-editor ol li.ql-indent-2 {
    counter-increment: list-2;
}
.ql-editor ol li.ql-indent-2:before {
    content: counter(list-2, lower-roman) '. ';
}
.ql-editor ol li.ql-indent-2 {
    counter-reset: list-3 list-4 list-5 list-6 list-7 list-8 list-9;
}
.ql-editor ol li.ql-indent-3 {
    counter-increment: list-3;
}
.ql-editor ol li.ql-indent-3:before {
    content: counter(list-3, decimal) '. ';
}
.ql-editor ol li.ql-indent-3 {
    counter-reset: list-4 list-5 list-6 list-7 list-8 list-9;
}
.ql-editor ol li.ql-indent-4 {
    counter-increment: list-4;
}
.ql-editor ol li.ql-indent-4:before {
    content: counter(list-4, lower-alpha) '. ';
}
.ql-editor ol li.ql-indent-4 {
    counter-reset: list-5 list-6 list-7 list-8 list-9;
}
.ql-editor ol li.ql-indent-5 {
    counter-increment: list-5;
}
.ql-editor ol li.ql-indent-5:before {
    content: counter(list-5, lower-roman) '. ';
}
.ql-editor ol li.ql-indent-5 {
    counter-reset: list-6 list-7 list-8 list-9;
}
.ql-editor ol li.ql-indent-6 {
    counter-increment: list-6;
}
.ql-editor ol li.ql-indent-6:before {
    content: counter(list-6, decimal) '. ';
}
.ql-editor ol li.ql-indent-6 {
    counter-reset: list-7 list-8 list-9;
}
.ql-editor ol li.ql-indent-7 {
    counter-increment: list-7;
}
.ql-editor ol li.ql-indent-7:before {
    content: counter(list-7, lower-alpha) '. ';
}
.ql-editor ol li.ql-indent-7 {
    counter-reset: list-8 list-9;
}
.ql-editor ol li.ql-indent-8 {
    counter-increment: list-8;
}
.ql-editor ol li.ql-indent-8:before {
    content: counter(list-8, lower-roman) '. ';
}
.ql-editor ol li.ql-indent-8 {
    counter-reset: list-9;
}
.ql-editor ol li.ql-indent-9 {
    counter-increment: list-9;
}
.ql-editor ol li.ql-indent-9:before {
    content: counter(list-9, decimal) '. ';
}
.ql-editor .ql-video {
    display: block;
    max-width: 100%;
}
.ql-editor .ql-video.ql-align-center {
    margin: 0 auto;
}
.ql-editor .ql-video.ql-align-right {
    margin: 0 0 0 auto;
}
.ql-editor .ql-bg-black {
    background: #000;
}
.ql-editor .ql-bg-red {
    background: #e60000;
}
.ql-editor .ql-bg-orange {
    background: #f90;
}
.ql-editor .ql-bg-yellow {
    background: #ff0;
}
.ql-editor .ql-bg-green {
    background: #008a00;
}
.ql-editor .ql-bg-blue {
    background: #06c;
}
.ql-editor .ql-bg-purple {
    background: #93f;
}
.ql-editor .ql-color-white {
    color: #fff;
}
.ql-editor .ql-color-red {
    color: #e60000;
}
.ql-editor .ql-color-orange {
    color: #f90;
}
.ql-editor .ql-color-yellow {
    color: #ff0;
}
.ql-editor .ql-color-green {
    color: #008a00;
}
.ql-editor .ql-color-blue {
    color: #06c;
}
.ql-editor .ql-color-purple {
    color: #93f;
}
.ql-editor .ql-font-serif {
    font-family: Georgia, Times New Roman, serif;
}
.ql-editor .ql-font-monospace {
    font-family: Monaco, Courier New, monospace;
}
.ql-editor .ql-size-small {
    font-size: 0.75rem;
}
.ql-editor .ql-size-large {
    font-size: 1.5rem;
}
.ql-editor .ql-size-huge {
    font-size: 2.5rem;
}
.ql-editor .ql-direction-rtl {
    direction: rtl;
    text-align: inherit;
}
.ql-editor .ql-align-center {
    text-align: center;
}
.ql-editor .ql-align-justify {
    text-align: justify;
}
.ql-editor .ql-align-right {
    text-align: right;
}
.ql-editor.ql-blank::before {
    color: rgba(0, 0, 0, 0.6);
    content: attr(data-placeholder);
    font-style: italic;
    inset-inline-start: 15px;
    pointer-events: none;
    position: absolute;
    inset-inline-end: 15px;
}
.ql-snow.ql-toolbar:after,
.ql-snow .ql-toolbar:after {
    clear: both;
    content: '';
    display: table;
}
.ql-snow.ql-toolbar button,
.ql-snow .ql-toolbar button {
    background: none;
    border: none;
    cursor: pointer;
    display: inline-block;
    float: left;
    height: 24px;
    padding-block: 3px;
    padding-inline: 5px;
    width: 28px;
}
.ql-snow.ql-toolbar button svg,
.ql-snow .ql-toolbar button svg {
    float: left;
    height: 100%;
}
.ql-snow.ql-toolbar button:active:hover,
.ql-snow .ql-toolbar button:active:hover {
    outline: none;
}
.ql-snow.ql-toolbar input.ql-image[type='file'],
.ql-snow .ql-toolbar input.ql-image[type='file'] {
    display: none;
}
.ql-snow.ql-toolbar button:hover,
.ql-snow .ql-toolbar button:hover,
.ql-snow.ql-toolbar button:focus,
.ql-snow .ql-toolbar button:focus,
.ql-snow.ql-toolbar button.ql-active,
.ql-snow .ql-toolbar button.ql-active,
.ql-snow.ql-toolbar .ql-picker-label:hover,
.ql-snow .ql-toolbar .ql-picker-label:hover,
.ql-snow.ql-toolbar .ql-picker-label.ql-active,
.ql-snow .ql-toolbar .ql-picker-label.ql-active,
.ql-snow.ql-toolbar .ql-picker-item:hover,
.ql-snow .ql-toolbar .ql-picker-item:hover,
.ql-snow.ql-toolbar .ql-picker-item.ql-selected,
.ql-snow .ql-toolbar .ql-picker-item.ql-selected {
    color: #06c;
}
.ql-snow.ql-toolbar button:hover .ql-fill,
.ql-snow .ql-toolbar button:hover .ql-fill,
.ql-snow.ql-toolbar button:focus .ql-fill,
.ql-snow .ql-toolbar button:focus .ql-fill,
.ql-snow.ql-toolbar button.ql-active .ql-fill,
.ql-snow .ql-toolbar button.ql-active .ql-fill,
.ql-snow.ql-toolbar .ql-picker-label:hover .ql-fill,
.ql-snow .ql-toolbar .ql-picker-label:hover .ql-fill,
.ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-fill,
.ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-fill,
.ql-snow.ql-toolbar .ql-picker-item:hover .ql-fill,
.ql-snow .ql-toolbar .ql-picker-item:hover .ql-fill,
.ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-fill,
.ql-snow .ql-toolbar .ql-picker-item.ql-selected .ql-fill,
.ql-snow.ql-toolbar button:hover .ql-stroke.ql-fill,
.ql-snow .ql-toolbar button:hover .ql-stroke.ql-fill,
.ql-snow.ql-toolbar button:focus .ql-stroke.ql-fill,
.ql-snow .ql-toolbar button:focus .ql-stroke.ql-fill,
.ql-snow.ql-toolbar button.ql-active .ql-stroke.ql-fill,
.ql-snow .ql-toolbar button.ql-active .ql-stroke.ql-fill,
.ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke.ql-fill,
.ql-snow .ql-toolbar .ql-picker-label:hover .ql-stroke.ql-fill,
.ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-stroke.ql-fill,
.ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-stroke.ql-fill,
.ql-snow.ql-toolbar .ql-picker-item:hover .ql-stroke.ql-fill,
.ql-snow .ql-toolbar .ql-picker-item:hover .ql-stroke.ql-fill,
.ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-stroke.ql-fill,
.ql-snow .ql-toolbar .ql-picker-item.ql-selected .ql-stroke.ql-fill {
    fill: #06c;
}
.ql-snow.ql-toolbar button:hover .ql-stroke,
.ql-snow .ql-toolbar button:hover .ql-stroke,
.ql-snow.ql-toolbar button:focus .ql-stroke,
.ql-snow .ql-toolbar button:focus .ql-stroke,
.ql-snow.ql-toolbar button.ql-active .ql-stroke,
.ql-snow .ql-toolbar button.ql-active .ql-stroke,
.ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke,
.ql-snow .ql-toolbar .ql-picker-label:hover .ql-stroke,
.ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-stroke,
.ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-stroke,
.ql-snow.ql-toolbar .ql-picker-item:hover .ql-stroke,
.ql-snow .ql-toolbar .ql-picker-item:hover .ql-stroke,
.ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-stroke,
.ql-snow .ql-toolbar .ql-picker-item.ql-selected .ql-stroke,
.ql-snow.ql-toolbar button:hover .ql-stroke-miter,
.ql-snow .ql-toolbar button:hover .ql-stroke-miter,
.ql-snow.ql-toolbar button:focus .ql-stroke-miter,
.ql-snow .ql-toolbar button:focus .ql-stroke-miter,
.ql-snow.ql-toolbar button.ql-active .ql-stroke-miter,
.ql-snow.ql-toolbar button.ql-active .ql-stroke-miter,
.ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke-miter,
.ql-snow .ql-toolbar .ql-picker-label:hover .ql-stroke-miter,
.ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-stroke-miter,
.ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-stroke-miter,
.ql-snow.ql-toolbar .ql-picker-item:hover .ql-stroke-miter,
.ql-snow .ql-toolbar .ql-picker-item:hover .ql-stroke-miter,
.ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-stroke-miter,
.ql-snow .ql-toolbar .ql-picker-item.ql-selected .ql-stroke-miter {
    stroke: #06c;
}
@media (pointer: coarse) {
    .ql-snow.ql-toolbar button:hover:not(.ql-active),
    .ql-snow .ql-toolbar button:hover:not(.ql-active) {
        color: #444;
    }
    .ql-snow.ql-toolbar button:hover:not(.ql-active) .ql-fill,
    .ql-snow .ql-toolbar button:hover:not(.ql-active) .ql-fill,
    .ql-snow.ql-toolbar button:hover:not(.ql-active) .ql-stroke.ql-fill,
    .ql-snow .ql-toolbar button:hover:not(.ql-active) .ql-stroke.ql-fill {
        fill: #444;
    }
    .ql-snow.ql-toolbar button:hover:not(.ql-active) .ql-stroke,
    .ql-snow .ql-toolbar button:hover:not(.ql-active) .ql-stroke,
    .ql-snow.ql-toolbar button:hover:not(.ql-active) .ql-stroke-miter,
    .ql-snow .ql-toolbar button:hover:not(.ql-active) .ql-stroke-miter {
        stroke: #444;
    }
}
.ql-snow {
    box-sizing: border-box;
}
.ql-snow * {
    box-sizing: border-box;
}
.ql-snow .ql-hidden {
    display: none;
}
.ql-snow .ql-out-bottom,
.ql-snow .ql-out-top {
    visibility: hidden;
}
.ql-snow .ql-tooltip {
    position: absolute;
    transform: translateY(10px);
}
.ql-snow .ql-tooltip a {
    cursor: pointer;
    text-decoration: none;
}
.ql-snow .ql-tooltip.ql-flip {
    transform: translateY(-10px);
}
.ql-snow .ql-formats {
    display: inline-block;
    vertical-align: middle;
}
.ql-snow .ql-formats:after {
    clear: both;
    content: '';
    display: table;
}
.ql-snow .ql-stroke {
    fill: none;
    stroke: #444;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2;
}
.ql-snow .ql-stroke-miter {
    fill: none;
    stroke: #444;
    stroke-miterlimit: 10;
    stroke-width: 2;
}
.ql-snow .ql-fill,
.ql-snow .ql-stroke.ql-fill {
    fill: #444;
}
.ql-snow .ql-empty {
    fill: none;
}
.ql-snow .ql-even {
    fill-rule: evenodd;
}
.ql-snow .ql-thin,
.ql-snow .ql-stroke.ql-thin {
    stroke-width: 1;
}
.ql-snow .ql-transparent {
    opacity: 0.4;
}
.ql-snow .ql-direction svg:last-child {
    display: none;
}
.ql-snow .ql-direction.ql-active svg:last-child {
    display: inline;
}
.ql-snow .ql-direction.ql-active svg:first-child {
    display: none;
}
.ql-snow .ql-editor h1 {
    font-size: 2rem;
}
.ql-snow .ql-editor h2 {
    font-size: 1.5rem;
}
.ql-snow .ql-editor h3 {
    font-size: 1.17rem;
}
.ql-snow .ql-editor h4 {
    font-size: 1rem;
}
.ql-snow .ql-editor h5 {
    font-size: 0.83rem;
}
.ql-snow .ql-editor h6 {
    font-size: 0.67rem;
}
.ql-snow .ql-editor a {
    text-decoration: underline;
}
.ql-snow .ql-editor blockquote {
    border-inline-start: 4px solid #ccc;
    margin-block-end: 5px;
    margin-block-start: 5px;
    padding-inline-start: 16px;
}
.ql-snow .ql-editor code,
.ql-snow .ql-editor pre {
    background: #f0f0f0;
    border-radius: 3px;
}
.ql-snow .ql-editor pre {
    white-space: pre-wrap;
    margin-block-end: 5px;
    margin-block-start: 5px;
    padding: 5px 10px;
}
.ql-snow .ql-editor code {
    font-size: 85%;
    padding: 2px 4px;
}
.ql-snow .ql-editor pre.ql-syntax {
    background: #23241f;
    color: #f8f8f2;
    overflow: visible;
}
.ql-snow .ql-editor img {
    max-width: 100%;
}
.ql-snow .ql-picker {
    color: #444;
    display: inline-block;
    float: left;
    inset-inline-start: 0;
    font-size: 14px;
    font-weight: 500;
    height: 24px;
    position: relative;
    vertical-align: middle;
}
.ql-snow .ql-picker-label {
    cursor: pointer;
    display: inline-block;
    height: 100%;
    padding-inline-start: 8px;
    padding-inline-end: 2px;
    position: relative;
    width: 100%;
}
.ql-snow .ql-picker-label::before {
    display: inline-block;
    line-height: 22px;
}
.ql-snow .ql-picker-options {
    background: #fff;
    display: none;
    min-width: 100%;
    padding: 4px 8px;
    position: absolute;
    white-space: nowrap;
}
.ql-snow .ql-picker-options .ql-picker-item {
    cursor: pointer;
    display: block;
    padding-block-end: 5px;
    padding-block-start: 5px;
}
.ql-snow .ql-picker.ql-expanded .ql-picker-label {
    color: #ccc;
    z-index: 2;
}
.ql-snow .ql-picker.ql-expanded .ql-picker-label .ql-fill {
    fill: #ccc;
}
.ql-snow .ql-picker.ql-expanded .ql-picker-label .ql-stroke {
    stroke: #ccc;
}
.ql-snow .ql-picker.ql-expanded .ql-picker-options {
    display: block;
    margin-block-start: -1px;
    top: 100%;
    z-index: 1;
}
.ql-snow .ql-color-picker,
.ql-snow .ql-icon-picker {
    width: 28px;
}
.ql-snow .ql-color-picker .ql-picker-label,
.ql-snow .ql-icon-picker .ql-picker-label {
    padding: 2px 4px;
}
.ql-snow .ql-color-picker .ql-picker-label svg,
.ql-snow .ql-icon-picker .ql-picker-label svg {
    inset-inline-end: 4px;
}
.ql-snow .ql-icon-picker .ql-picker-options {
    padding: 4px 0;
}
.ql-snow .ql-icon-picker .ql-picker-item {
    height: 24px;
    width: 24px;
    padding: 2px 4px;
}
.ql-snow .ql-color-picker .ql-picker-options {
    padding: 3px 5px;
    width: 152px;
}
.ql-snow .ql-color-picker .ql-picker-item {
    border: 1px solid transparent;
    float: left;
    height: 16px;
    margin: 2px;
    padding: 0;
    width: 16px;
}
.ql-snow .ql-picker:not(.ql-color-picker):not(.ql-icon-picker) svg {
    position: absolute;
    margin-block-start: -9px;
    inset-inline-end: 0;
    top: 50%;
    width: 18px;
}
.ql-snow .ql-picker.ql-header .ql-picker-label[data-label]:not([data-label=''])::before,
.ql-snow .ql-picker.ql-font .ql-picker-label[data-label]:not([data-label=''])::before,
.ql-snow .ql-picker.ql-size .ql-picker-label[data-label]:not([data-label=''])::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-label]:not([data-label=''])::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-label]:not([data-label=''])::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-label]:not([data-label=''])::before {
    content: attr(data-label);
}
.ql-snow .ql-picker.ql-header {
    width: 98px;
}
.ql-snow .ql-picker.ql-header .ql-picker-label::before,
.ql-snow .ql-picker.ql-header .ql-picker-item::before {
    content: 'Normal';
}
.ql-snow .ql-picker.ql-header .ql-picker-label[data-value='1']::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value='1']::before {
    content: 'Heading 1';
}
.ql-snow .ql-picker.ql-header .ql-picker-label[data-value='2']::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value='2']::before {
    content: 'Heading 2';
}
.ql-snow .ql-picker.ql-header .ql-picker-label[data-value='3']::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value='3']::before {
    content: 'Heading 3';
}
.ql-snow .ql-picker.ql-header .ql-picker-label[data-value='4']::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value='4']::before {
    content: 'Heading 4';
}
.ql-snow .ql-picker.ql-header .ql-picker-label[data-value='5']::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value='5']::before {
    content: 'Heading 5';
}
.ql-snow .ql-picker.ql-header .ql-picker-label[data-value='6']::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value='6']::before {
    content: 'Heading 6';
}
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value='1']::before {
    font-size: 2rem;
}
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value='2']::before {
    font-size: 1.5rem;
}
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value='3']::before {
    font-size: 1.17rem;
}
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value='4']::before {
    font-size: 1rem;
}
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value='5']::before {
    font-size: 0.83rem;
}
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value='6']::before {
    font-size: 0.67rem;
}
.ql-snow .ql-picker.ql-font {
    width: 108px;
}
.ql-snow .ql-picker.ql-font .ql-picker-label::before,
.ql-snow .ql-picker.ql-font .ql-picker-item::before {
    content: 'Sans Serif';
}
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value='serif']::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value='serif']::before {
    content: 'Serif';
}
.ql-snow .ql-picker.ql-font .ql-picker-label[data-value='monospace']::before,
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value='monospace']::before {
    content: 'Monospace';
}
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value='serif']::before {
    font-family: Georgia, Times New Roman, serif;
}
.ql-snow .ql-picker.ql-font .ql-picker-item[data-value='monospace']::before {
    font-family: Monaco, Courier New, monospace;
}
.ql-snow .ql-picker.ql-size {
    width: 98px;
}
.ql-snow .ql-picker.ql-size .ql-picker-label::before,
.ql-snow .ql-picker.ql-size .ql-picker-item::before {
    content: 'Normal';
}
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value='small']::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value='small']::before {
    content: 'Small';
}
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value='large']::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value='large']::before {
    content: 'Large';
}
.ql-snow .ql-picker.ql-size .ql-picker-label[data-value='huge']::before,
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value='huge']::before {
    content: 'Huge';
}
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value='small']::before {
    font-size: 10px;
}
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value='large']::before {
    font-size: 18px;
}
.ql-snow .ql-picker.ql-size .ql-picker-item[data-value='huge']::before {
    font-size: 32px;
}
.ql-snow .ql-color-picker.ql-background .ql-picker-item {
    background: #fff;
}
.ql-snow .ql-color-picker.ql-color .ql-picker-item {
    background: #000;
}
.ql-toolbar.ql-snow {
    border: 1px solid #ccc;
    box-sizing: border-box;
    font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
    padding: 8px;
}
.ql-toolbar.ql-snow .ql-formats {
    margin-inline-end: 15px;
}
.ql-toolbar.ql-snow .ql-picker-label {
    border: 1px solid transparent;
}
.ql-toolbar.ql-snow .ql-picker-options {
    border: 1px solid transparent;
    box-shadow: rgba(0, 0, 0, 0.2) 0 2px 8px;
}
.ql-toolbar.ql-snow .ql-picker.ql-expanded .ql-picker-label {
    border-color: #ccc;
}
.ql-toolbar.ql-snow .ql-picker.ql-expanded .ql-picker-options {
    border-color: #ccc;
}
.ql-toolbar.ql-snow .ql-color-picker .ql-picker-item.ql-selected,
.ql-toolbar.ql-snow .ql-color-picker .ql-picker-item:hover {
    border-color: #000;
}
.ql-toolbar.ql-snow + .ql-container.ql-snow {
    border-block-start: 0;
}
.ql-snow .ql-tooltip {
    background: #fff;
    border: 1px solid #ccc;
    box-shadow: 0 0 5px #ddd;
    color: #444;
    padding: 5px 12px;
    white-space: nowrap;
}
.ql-snow .ql-tooltip::before {
    content: 'Visit URL:';
    line-height: 26px;
    margin-inline-end: 8px;
}
.ql-snow .ql-tooltip input[type='text'] {
    display: none;
    border: 1px solid #ccc;
    font-size: 13px;
    height: 26px;
    margin: 0;
    padding: 3px 5px;
    width: 170px;
}
.ql-snow .ql-tooltip a.ql-preview {
    display: inline-block;
    max-width: 200px;
    overflow-x: hidden;
    text-overflow: ellipsis;
    vertical-align: top;
}
.ql-snow .ql-tooltip a.ql-action::after {
    border-inline-end: 1px solid #ccc;
    content: 'Edit';
    margin-inline-start: 16px;
    padding-inline-end: 8px;
}
.ql-snow .ql-tooltip a.ql-remove::before {
    content: 'Remove';
    margin-inline-start: 8px;
}
.ql-snow .ql-tooltip a {
    line-height: 26px;
}
.ql-snow .ql-tooltip.ql-editing a.ql-preview,
.ql-snow .ql-tooltip.ql-editing a.ql-remove {
    display: none;
}
.ql-snow .ql-tooltip.ql-editing input[type='text'] {
    display: inline-block;
}
.ql-snow .ql-tooltip.ql-editing a.ql-action::after {
    border-inline-end: 0;
    content: 'Save';
    padding-inline-end: 0;
}
.ql-snow .ql-tooltip[data-mode='link']::before {
    content: 'Enter link:';
}
.ql-snow .ql-tooltip[data-mode='formula']::before {
    content: 'Enter formula:';
}
.ql-snow .ql-tooltip[data-mode='video']::before {
    content: 'Enter video:';
}
.ql-snow a {
    color: #06c;
}
.ql-container.ql-snow {
    border: 1px solid #ccc;
}

.p-editor .p-editor-toolbar {
    background: `.concat(n("editor.toolbar.background"),`;
    border-start-end-radius: `).concat(n("editor.toolbar.border.radius"),`;
    border-start-start-radius: `).concat(n("editor.toolbar.border.radius"),`;
}

.p-editor .p-editor-toolbar.ql-snow {
    border: 1px solid `).concat(n("editor.toolbar.border.color"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-stroke {
    stroke: `).concat(n("editor.toolbar.item.color"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-fill {
    fill: `).concat(n("editor.toolbar.item.color"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-picker .ql-picker-label {
    border: 0 none;
    color: `).concat(n("editor.toolbar.item.color"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-picker .ql-picker-label:hover {
    color: `).concat(n("editor.toolbar.item.hover.color"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-picker .ql-picker-label:hover .ql-stroke {
    stroke: `).concat(n("editor.toolbar.item.hover.color"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-picker .ql-picker-label:hover .ql-fill {
    fill: `).concat(n("editor.toolbar.item.hover.color"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-picker.ql-expanded .ql-picker-label {
    color: `).concat(n("editor.toolbar.item.active.color"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-picker.ql-expanded .ql-picker-label .ql-stroke {
    stroke: `).concat(n("editor.toolbar.item.active.color"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-picker.ql-expanded .ql-picker-label .ql-fill {
    fill: `).concat(n("editor.toolbar.item.active.color"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-picker.ql-expanded .ql-picker-options {
    background: `).concat(n("editor.overlay.background"),`;
    border: 1px solid `).concat(n("editor.overlay.border.color"),`;
    box-shadow: `).concat(n("editor.overlay.shadow"),`;
    border-radius: `).concat(n("editor.overlay.border.radius"),`;
    padding: `).concat(n("editor.overlay.padding"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-picker.ql-expanded .ql-picker-options .ql-picker-item {
    color: `).concat(n("editor.overlay.option.color"),`;
    border-radius: `).concat(n("editor.overlay.option.border.radius"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-picker.ql-expanded .ql-picker-options .ql-picker-item:hover {
    background: `).concat(n("editor.overlay.option.focus.background"),`;
    color: `).concat(n("editor.overlay.option.focus.color"),`;
}

.p-editor .p-editor-toolbar.ql-snow .ql-picker.ql-expanded:not(.ql-icon-picker) .ql-picker-item {
    padding: `).concat(n("editor.overlay.option.padding"),`;
}

.p-editor .p-editor-content {
    border-end-end-radius: `).concat(n("editor.content.border.radius"),`;
    border-end-start-radius: `).concat(n("editor.content.border.radius"),`;
}

.p-editor .p-editor-content.ql-snow {
    border: 1px solid `).concat(n("editor.content.border.color"),`;
}

.p-editor .p-editor-content .ql-editor {
    background: `).concat(n("editor.content.background"),`;
    color: `).concat(n("editor.content.color"),`;
    border-end-end-radius: `).concat(n("editor.content.border.radius"),`;
    border-end-start-radius: `).concat(n("editor.content.border.radius"),`;
}

.p-editor .ql-snow.ql-toolbar button:hover,
.p-editor .ql-snow.ql-toolbar button:focus {
    color: `).concat(n("editor.toolbar.item.hover.color"),`;
}

.p-editor .ql-snow.ql-toolbar button:hover .ql-stroke,
.p-editor .ql-snow.ql-toolbar button:focus .ql-stroke {
    stroke: `).concat(n("editor.toolbar.item.hover.color"),`;
}

.p-editor .ql-snow.ql-toolbar button:hover .ql-fill,
.p-editor .ql-snow.ql-toolbar button:focus .ql-fill {
    fill: `).concat(n("editor.toolbar.item.hover.color"),`;
}

.p-editor .ql-snow.ql-toolbar button.ql-active,
.p-editor .ql-snow.ql-toolbar .ql-picker-label.ql-active,
.p-editor .ql-snow.ql-toolbar .ql-picker-item.ql-selected {
    color: `).concat(n("editor.toolbar.item.active.color"),`;
}

.p-editor .ql-snow.ql-toolbar button.ql-active .ql-stroke,
.p-editor .ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-stroke,
.p-editor .ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-stroke {
    stroke: `).concat(n("editor.toolbar.item.active.color"),`;
}

.p-editor .ql-snow.ql-toolbar button.ql-active .ql-fill,
.p-editor .ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-fill,
.p-editor .ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-fill {
    fill: `).concat(n("editor.toolbar.item.active.color"),`;
}

.p-editor .ql-snow.ql-toolbar button.ql-active .ql-picker-label,
.p-editor .ql-snow.ql-toolbar .ql-picker-label.ql-active .ql-picker-label,
.p-editor .ql-snow.ql-toolbar .ql-picker-item.ql-selected .ql-picker-label {
    color: `).concat(n("editor.toolbar.item.active.color"),`;
}
`)},Pt={root:function(t){var n=t.instance;return["p-editor",{"p-invalid":n.$invalid}]},toolbar:"p-editor-toolbar",content:"p-editor-content"},Et=he.extend({name:"editor",theme:Kt,classes:Pt}),Mt={name:"BaseEditor",extends:xn,props:{placeholder:String,readonly:Boolean,formats:Array,editorStyle:null,modules:null},style:Et,provide:function(){return{$pcEditor:this,$parentInstance:this}}};function J(e){"@babel/helpers - typeof";return J=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},J(e)}function Ee(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var l=Object.getOwnPropertySymbols(e);t&&(l=l.filter(function(c){return Object.getOwnPropertyDescriptor(e,c).enumerable})),n.push.apply(n,l)}return n}function jt(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?arguments[t]:{};t%2?Ee(Object(n),!0).forEach(function(l){Nt(e,l,n[l])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Ee(Object(n)).forEach(function(l){Object.defineProperty(e,l,Object.getOwnPropertyDescriptor(n,l))})}return e}function Nt(e,t,n){return(t=Lt(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function Lt(e){var t=At(e,"string");return J(t)=="symbol"?t:t+""}function At(e,t){if(J(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var l=n.call(e,t);if(J(l)!="object")return l;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var Me=function(){try{return window.Quill}catch{return null}}(),me={name:"Editor",extends:Mt,inheritAttrs:!1,emits:["text-change","selection-change","load"],data:function(){return{reRenderColorKey:0}},quill:null,watch:{modelValue:function(t,n){t!==n&&this.quill&&!this.quill.hasFocus()&&(this.reRenderColorKey++,this.renderValue(t))},readonly:function(){this.handleReadOnlyChange()}},mounted:function(){var t=this,n={modules:jt({toolbar:this.$refs.toolbarElement},this.modules),readOnly:this.readonly,theme:"snow",formats:this.formats,placeholder:this.placeholder};Me?(this.quill=new Me(this.$refs.editorElement,n),this.initQuill(),this.handleLoad()):hn(()=>import("./quill-Bn6O9OcI.js"),__vite__mapDeps([0,1,2])).then(function(l){l&&qn(t.$refs.editorElement)&&(l.default?t.quill=new l.default(t.$refs.editorElement,n):t.quill=new l(t.$refs.editorElement,n),t.initQuill())}).then(function(){t.handleLoad()})},beforeUnmount:function(){this.quill=null},methods:{renderValue:function(t){this.quill&&(t?this.quill.clipboard.dangerouslyPasteHTML(t):this.quill.setText(""))},initQuill:function(){var t=this;this.renderValue(this.d_value),this.quill.on("text-change",function(n,l,c){if(c==="user"){var a=t.$refs.editorElement.children[0].innerHTML,m=t.quill.getText().trim();a==="<p><br></p>"&&(a=""),t.writeValue(a),t.$emit("text-change",{htmlValue:a,textValue:m,delta:n,source:c,instance:t.quill})}}),this.quill.on("selection-change",function(n,l,c){var a=t.$refs.editorElement.children[0].innerHTML,m=t.quill.getText().trim();t.$emit("selection-change",{htmlValue:a,textValue:m,range:n,oldRange:l,source:c,instance:t.quill})})},handleLoad:function(){this.quill&&this.quill.getModule("toolbar")&&this.$emit("load",{instance:this.quill})},handleReadOnlyChange:function(){this.quill&&this.quill.enable(!this.readonly)}}};function $t(e,t,n,l,c,a){return b(),q("div",f({class:e.cx("root")},e.ptmi("root")),[i("div",f({ref:"toolbarElement",class:e.cx("toolbar")},e.ptm("toolbar")),[O(e.$slots,"toolbar",{},function(){return[i("span",f({class:"ql-formats"},e.ptm("formats")),[i("select",f({class:"ql-header",defaultValue:"0"},e.ptm("header")),[i("option",f({value:"1"},e.ptm("option")),"Heading",16),i("option",f({value:"2"},e.ptm("option")),"Subheading",16),i("option",f({value:"0"},e.ptm("option")),"Normal",16)],16),i("select",f({class:"ql-font"},e.ptm("font")),[i("option",yn(gn(e.ptm("option"))),null,16),i("option",f({value:"serif"},e.ptm("option")),null,16),i("option",f({value:"monospace"},e.ptm("option")),null,16)],16)],16),i("span",f({class:"ql-formats"},e.ptm("formats")),[i("button",f({class:"ql-bold",type:"button"},e.ptm("bold")),null,16),i("button",f({class:"ql-italic",type:"button"},e.ptm("italic")),null,16),i("button",f({class:"ql-underline",type:"button"},e.ptm("underline")),null,16)],16),(b(),q("span",f({key:c.reRenderColorKey,class:"ql-formats"},e.ptm("formats")),[i("select",f({class:"ql-color"},e.ptm("color")),null,16),i("select",f({class:"ql-background"},e.ptm("background")),null,16)],16)),i("span",f({class:"ql-formats"},e.ptm("formats")),[i("button",f({class:"ql-list",value:"ordered",type:"button"},e.ptm("list")),null,16),i("button",f({class:"ql-list",value:"bullet",type:"button"},e.ptm("list")),null,16),i("select",f({class:"ql-align"},e.ptm("select")),[i("option",f({defaultValue:""},e.ptm("option")),null,16),i("option",f({value:"center"},e.ptm("option")),null,16),i("option",f({value:"right"},e.ptm("option")),null,16),i("option",f({value:"justify"},e.ptm("option")),null,16)],16)],16),i("span",f({class:"ql-formats"},e.ptm("formats")),[i("button",f({class:"ql-link",type:"button"},e.ptm("link")),null,16),i("button",f({class:"ql-image",type:"button"},e.ptm("image")),null,16),i("button",f({class:"ql-code-block",type:"button"},e.ptm("codeBlock")),null,16)],16),i("span",f({class:"ql-formats"},e.ptm("formats")),[i("button",f({class:"ql-clean",type:"button"},e.ptm("clean")),null,16)],16)]})],16),i("div",f({ref:"editorElement",class:e.cx("content"),style:e.editorStyle},e.ptm("content")),null,16)],16)}me.render=$t;const zt={class:"grid grid-cols-1 md:grid-cols-3 gap-4"},Ft={class:"field col-12 sm:col-6 mb-4 pr-md-2"},Dt={key:0,class:"p-error"},_t={class:"field col-12 sm:col-6 mb-4 pl-md-2"},Ut={key:0,class:"p-error"},Bt={class:"field col-12 md:col-4 mb-4 pr-md-2"},Ht={key:0,class:"p-error"},Rt={class:"field col-12 md:col-4 mb-4 px-md-2"},Wt={key:0,class:"p-error"},Qt={class:"field col-12 md:col-4 mb-4 pl-md-2"},Gt={key:0,class:"p-error"},Jt={class:"field col-12 md:col-4 mb-4 pr-md-2"},Yt={key:0,class:"p-error"},Zt={class:"field col-12 md:col-4 mb-4 pl-md-2"},Xt={class:"pt-2 flex items-center"},el={class:"ml-2"},nl={class:"grid grid-cols-1 md:grid-cols-3 gap-4"},tl={class:"field col-12 sm:col-6 mb-4 pr-md-2"},ll={key:0,class:"p-error"},ol={class:"field col-12 sm:col-6 mb-4 pl-md-2"},rl={key:0,class:"p-error"},il={key:0,class:"col-span-full"},al={class:"overflow-x-auto border rounded"},sl={class:"min-w-full text-sm"},dl={class:"p-2"},cl={class:"p-2"},ul={class:"p-2"},pl={key:0,class:"p-error"},fl={class:"grid grid-cols-1 md:grid-cols-3 gap-4"},bl={class:"field col-12 sm:col-6 mb-4 pr-md-2"},ml={key:0,class:"p-error"},hl={class:"field col-12 sm:col-6 mb-4 pr-md-2"},ql={class:"field col-12 sm:col-6 mb-4 pl-md-2"},yl={class:"field col-12 mb-4 col-span-full"},gl={class:"grid grid-cols-1 md:grid-cols-3 gap-4"},vl={class:"field col-12 md:col-4 mb-4 pr-md-2"},kl={class:"field col-span-2 mb-4 px-md-2"},wl={class:"grid grid-cols-3 gap-2"},xl={class:"field col-12 md:col-4 mb-4 pl-md-2"},Sl={class:"grid grid-cols-1 md:grid-cols-3 gap-4"},Cl={class:"field col-12 mb-4"},Ol={class:"flex items-center gap-4"},Vl={key:0,class:"thumbnail-preview mb-3"},Tl=["src"],Il={key:0},Kl={class:"mb-4 border rounded p-3"},Pl={class:"grid grid-cols-1 md:grid-cols-3 gap-4"},El={class:"block font-bold mb-2"},Ml={class:"flex gap-2 mt-3"},jl={class:"mb-3"},Nl={key:0,class:"p-error ml-3"},Ll={key:0,class:"w-full overflow-x-auto"},Al={class:"min-w-[1100px] w-full text-sm table-fixed"},$l={class:"p-2"},zl={class:"p-2"},Fl={class:"grid grid-cols-4"},Dl={class:"p-2"},_l={class:"p-2"},Ul={class:"max-w-[260px]"},Bl={class:"p-2"},Hl={class:"space-y-2"},Rl={class:"col-span-5 text-xs truncate"},Wl={class:"col-span-4"},Ql={class:"p-2"},Gl={class:"p-2 text-right"},Jl={key:1,class:"p-error"},Yl={class:"grid grid-cols-1 md:grid-cols-1 gap-4"},Zl={class:"field col-12 mb-4"},Xl={class:"field col-12 mb-4"},eo={class:"grid grid-cols-1 md:grid-cols-2 gap-4"},no={class:"field col-12 sm:col-6 mb-4 pr-md-2"},to={class:"field col-12 sm:col-6 mb-4 pl-md-2"},lo={class:"field col-12 mb-4 col-span-full"},oo={class:"flex justify-end gap-2 pt-2"},ro={__name:"ProductForm",props:{product:{type:Object,default:null},isEditing:{type:Boolean,default:!1},categories:{type:Array,default:()=>[]},brands:{type:Array,default:()=>[]},taxes:{type:Array,default:()=>[]},tags:{type:Array,default:()=>[]},attributes:{type:Array,default:()=>[]},warehouses:{type:Array,default:()=>[]}},emits:["cancel","saved"],setup(e,{emit:t}){const n=e,l=t,c=[{label:"Simple Product",value:"simple"},{label:"Variable Product",value:"variable"}],a={category_id:null,brand_id:null,tax_id:null,name:"",slug:"",sku:"",barcode:"",code:"",base_price:null,base_discount_price:null,type:"simple",stocks:[],thumbnail:null,images:[],weight:null,dimensions:{length:null,width:null,height:null},materials:[],description:"",additional_info:"",is_active:!0,meta_title:"",meta_description:"",meta_keywords:"",tag_ids:[],variations:[]},m=u=>{if(!u)return{...a};const o={...a,category_id:u.category_id??null,brand_id:u.brand_id??null,tax_id:u.tax_id??null,name:u.name??"",slug:u.slug??"",sku:u.sku??"",barcode:u.barcode??"",code:u.code??"",base_price:u.base_price??null,base_discount_price:u.base_discount_price??null,type:u.type??"simple",thumbnail:null,images:u.images??[],weight:u.weight??null,dimensions:u.dimensions??{length:null,width:null,height:null},materials:u.materials??[],description:u.description??"",additional_info:u.additional_info??"",is_active:u.is_active??!0,meta_title:u.meta_title??"",meta_description:u.meta_description??"",meta_keywords:u.meta_keywords??"",tag_ids:u.tag_ids?u.tag_ids:(u.tags||[]).map(s=>s.id),stocks:(u.stocks||[]).filter(s=>!s.variation_id).map(s=>({warehouse_id:s.warehouse_id,quantity:Number(s.quantity??0),alert_quantity:s.alert_quantity??null})),variations:(u.variations||[]).map(s=>({id:s.id,sku:s.sku??"",price:s.price??null,discount_price:s.discount_price??null,image:s.image??null,attribute_value_ids:(s.attribute_values||s.attribute_value_ids||[]).map(g=>typeof g=="object"?g.id:g),stocks:(s.stocks||[]).map(g=>({warehouse_id:g.warehouse_id,quantity:Number(g.quantity??0),alert_quantity:g.alert_quantity??null}))}))};return o.type==="simple"&&(!o.stocks||!o.stocks.length)&&(o.stocks=n.warehouses.map(s=>({warehouse_id:s.id,quantity:0,alert_quantity:null}))),o},h=D(!1),r=vn(m(n.product)),p=D(null),x=oe(()=>{const u=o=>({key:String(o.id),label:o.name,data:o,children:(o.children||[]).map(u)});return n.categories.map(u)});_(p,u=>{if(!u)return r.category_id=null;if(typeof u=="string"||typeof u=="number"){r.category_id=Number(u);return}if(typeof u=="object"&&u.key){r.category_id=Number(u.key);return}if(typeof u=="object"){const o=Object.keys(u);r.category_id=o.length?Number(o[0]):null;return}r.category_id=null},{immediate:!0});const N=D("");_(N,u=>{r.materials=u?u.split(",").map(o=>o.trim()).filter(Boolean):[]}),_(()=>r.name,u=>{r.slug||(r.slug=(u||"").toLowerCase().replace(/\s+/g,"-").replace(/[^\w\-]+/g,"").replace(/\-\-+/g,"-").replace(/^-+/,"").replace(/-+$/,""))});const P=D(null),X=u=>{var g;const o=(g=u.files)==null?void 0:g[0];if(!o)return;r.thumbnail=o;const s=new FileReader;s.onload=v=>P.value=v.target.result,s.readAsDataURL(o)},Be=oe(()=>{const u=[];return n.attributes.forEach(o=>{(o.values||[]).forEach(s=>{u.push({id:s.id,label:`${o.display_name||o.name}: ${s.display_value||s.value}`})})}),u}),E=D({}),He=u=>u.reduce((o,s)=>o.flatMap(g=>s.map(v=>[...g,v])),[[]]),ye=u=>u.slice().sort((o,s)=>o-s).join("-"),Re=oe(()=>{const u=new Map;return n.attributes.forEach(o=>{(o.values||[]).forEach(s=>u.set(s.id,String(s.display_value||s.value||"")))}),u}),We=u=>{const o=u.map(s=>Re.value.get(s)).filter(Boolean);return`${r.slug||r.name||"product"}-${o.join("-")}`.toUpperCase().replace(/\s+/g,"")},Qe=u=>{const o=new Map(r.variations.map(s=>[ye(s.attribute_value_ids||[]),s]));return u.map(s=>{var v;const g=o.get(ye(s.attribute_value_ids));return g?{...s,id:g.id,sku:g.sku||s.sku,price:g.price??s.price,discount_price:g.discount_price??s.discount_price,image:g.image||s.image,stocks:(v=g.stocks)!=null&&v.length?g.stocks:s.stocks}:s})},ee=()=>n.warehouses.map(u=>({warehouse_id:u.id,quantity:0,alert_quantity:null})),Ge=()=>{r.variations.push({sku:"",price:r.base_price||null,discount_price:null,image:null,attribute_value_ids:[],stocks:ee()})},Je=u=>r.variations.splice(u,1),Ye=()=>{const u=Object.values(E.value).filter(v=>Array.isArray(v)&&v.length);if(!u.length)return r.variations=[];const o=He(u),s=r.base_price||null,g=o.map(v=>({sku:We(v),price:s,discount_price:null,image:null,attribute_value_ids:v,stocks:ee()}));r.variations=Qe(g)},Ze=()=>{r.variations=[],Object.keys(E.value).forEach(u=>E.value[u]=[])};_(()=>r.type,u=>{var o;u==="simple"?((o=r.stocks)!=null&&o.length||(r.stocks=ee()),r.variations=[]):u==="variable"&&(r.stocks=[],E.value={},n.attributes.forEach(s=>E.value[s.id]=[]))},{immediate:!0});const ge=()=>{var o;const u=m(n.product);r.defaults(u),r.reset(u),r.clearErrors(),N.value=Array.isArray(u.materials)?u.materials.join(", "):"",p.value=u.category_id?String(u.category_id):null,P.value=(o=n.product)!=null&&o.thumbnail?we(n.product.thumbnail):null,E.value={},n.attributes.forEach(s=>E.value[s.id]=[])};kn(ge),_(()=>n.product,ge);const Xe=()=>{h.value=!0;const u=n.isEditing?route("products.update",n.product.id):route("products.store"),o={preserveScroll:!0,forceFormData:!0,onSuccess:()=>l("saved"),onFinish:()=>h.value=!1};n.isEditing?r.transform(s=>({...s,_method:"put"})).post(u,o):r.post(u,o)},en=()=>l("cancel");return(u,o)=>(b(),q("form",{onSubmit:qe(Xe,["prevent"]),class:"space-y-6"},[i("div",null,[o[30]||(o[30]=i("h3",{class:"text-xl font-semibold uppercase mb-3"}," Basic Information ",-1)),i("div",zt,[i("div",Ft,[o[23]||(o[23]=i("label",{for:"name",class:"block font-bold mb-2"},"Name *",-1)),y(d(K),{id:"name",modelValue:d(r).name,"onUpdate:modelValue":o[0]||(o[0]=s=>d(r).name=s),modelModifiers:{trim:!0},required:"",class:w(["w-full",{"p-invalid":h.value&&!d(r).name}])},null,8,["modelValue","class"]),d(r).errors.name?(b(),q("small",Dt,S(d(r).errors.name),1)):k("",!0)]),i("div",_t,[o[24]||(o[24]=i("label",{for:"slug",class:"block font-bold mb-2"},"Slug *",-1)),y(d(K),{id:"slug",modelValue:d(r).slug,"onUpdate:modelValue":o[1]||(o[1]=s=>d(r).slug=s),modelModifiers:{trim:!0},required:"",class:w(["w-full",{"p-invalid":h.value&&!d(r).slug}])},null,8,["modelValue","class"]),d(r).errors.slug?(b(),q("small",Ut,S(d(r).errors.slug),1)):k("",!0)]),i("div",Bt,[o[25]||(o[25]=i("label",{for:"sku",class:"block font-bold mb-2"},"SKU",-1)),y(d(K),{id:"sku",modelValue:d(r).sku,"onUpdate:modelValue":o[2]||(o[2]=s=>d(r).sku=s),modelModifiers:{trim:!0},class:"w-full"},null,8,["modelValue"]),d(r).errors.sku?(b(),q("small",Ht,S(d(r).errors.sku),1)):k("",!0)]),i("div",Rt,[o[26]||(o[26]=i("label",{for:"barcode",class:"block font-bold mb-2"},"Barcode",-1)),y(d(K),{id:"barcode",modelValue:d(r).barcode,"onUpdate:modelValue":o[3]||(o[3]=s=>d(r).barcode=s),modelModifiers:{trim:!0},class:"w-full"},null,8,["modelValue"]),d(r).errors.barcode?(b(),q("small",Wt,S(d(r).errors.barcode),1)):k("",!0)]),i("div",Qt,[o[27]||(o[27]=i("label",{for:"code",class:"block font-bold mb-2"},"Product Code",-1)),y(d(K),{id:"code",modelValue:d(r).code,"onUpdate:modelValue":o[4]||(o[4]=s=>d(r).code=s),modelModifiers:{trim:!0},class:"w-full"},null,8,["modelValue"]),d(r).errors.code?(b(),q("small",Gt,S(d(r).errors.code),1)):k("",!0)]),i("div",Jt,[o[28]||(o[28]=i("label",{for:"type",class:"block font-bold mb-2"},"Product Type *",-1)),y(d(re),{id:"type",modelValue:d(r).type,"onUpdate:modelValue":o[5]||(o[5]=s=>d(r).type=s),options:c,optionLabel:"label",optionValue:"value",placeholder:"Select Type",class:w(["w-full",{"p-invalid":d(r).errors.type}])},null,8,["modelValue","class"]),d(r).errors.type?(b(),q("small",Yt,S(d(r).errors.type),1)):k("",!0)]),i("div",Zt,[o[29]||(o[29]=i("label",{for:"status",class:"block font-bold mb-2"},"Status",-1)),i("div",Xt,[y(d(On),{modelValue:d(r).is_active,"onUpdate:modelValue":o[6]||(o[6]=s=>d(r).is_active=s)},null,8,["modelValue"]),i("span",el,S(d(r).is_active?"Active":"Inactive"),1)])])])]),i("div",null,[o[35]||(o[35]=i("h3",{class:"text-xl font-semibold uppercase mb-3"}," Pricing & Inventory ",-1)),i("div",nl,[i("div",tl,[o[31]||(o[31]=i("label",{for:"base_price",class:"block font-bold mb-2"},"Base Price *",-1)),y(d(I),{id:"base_price",modelValue:d(r).base_price,"onUpdate:modelValue":o[7]||(o[7]=s=>d(r).base_price=s),modelModifiers:{number:!0},class:w(["w-full",{"p-invalid":d(r).errors.base_price}]),min:0},null,8,["modelValue","class"]),d(r).errors.base_price?(b(),q("small",ll,S(d(r).errors.base_price),1)):k("",!0)]),i("div",ol,[o[32]||(o[32]=i("label",{for:"base_discount_price",class:"block font-bold mb-2"},"Discount Price",-1)),y(d(I),{id:"base_discount_price",modelValue:d(r).base_discount_price,"onUpdate:modelValue":o[8]||(o[8]=s=>d(r).base_discount_price=s),modelModifiers:{number:!0},class:"w-full",min:0,max:d(r).base_price||null},null,8,["modelValue","max"]),d(r).errors.base_discount_price?(b(),q("small",rl,S(d(r).errors.base_discount_price),1)):k("",!0)]),d(r).type==="simple"?(b(),q("div",il,[o[34]||(o[34]=i("h4",{class:"font-semibold mb-2"},"Warehouse Stock (Simple)",-1)),i("div",al,[i("table",sl,[o[33]||(o[33]=i("thead",null,[i("tr",{class:"border-b"},[i("th",{class:"p-2 text-left"},"Warehouse"),i("th",{class:"p-2 text-left"},"Quantity"),i("th",{class:"p-2 text-left"},"Alert Qty")])],-1)),i("tbody",null,[(b(!0),q(V,null,$(d(r).stocks,(s,g)=>{var v;return b(),q("tr",{key:s.warehouse_id,class:"border-b"},[i("td",dl,S(((v=e.warehouses.find(L=>L.id===s.warehouse_id))==null?void 0:v.name)||"Warehouse"),1),i("td",cl,[y(d(I),{modelValue:s.quantity,"onUpdate:modelValue":L=>s.quantity=L,modelModifiers:{number:!0},class:"w-full",min:0},null,8,["modelValue","onUpdate:modelValue"])]),i("td",ul,[y(d(I),{modelValue:s.alert_quantity,"onUpdate:modelValue":L=>s.alert_quantity=L,modelModifiers:{number:!0},class:"w-full",min:0},null,8,["modelValue","onUpdate:modelValue"])])])}),128))])])]),d(r).errors.stocks?(b(),q("small",pl,S(d(r).errors.stocks),1)):k("",!0)])):k("",!0)])]),i("div",null,[o[40]||(o[40]=i("h3",{class:"text-xl font-semibold uppercase mb-3"}," Categories & Tags ",-1)),i("div",fl,[i("div",bl,[o[36]||(o[36]=i("label",{for:"category_id",class:"block font-bold mb-2"},"Category *",-1)),y(d(Ue),{id:"category_id",modelValue:p.value,"onUpdate:modelValue":o[9]||(o[9]=s=>p.value=s),options:x.value,placeholder:"Select Category",class:w(["w-full",{"p-invalid":h.value&&!d(r).category_id}]),selectionMode:"single"},null,8,["modelValue","options","class"]),h.value&&!d(r).category_id?(b(),q("small",ml," Category is required. ")):k("",!0)]),i("div",hl,[o[37]||(o[37]=i("label",{for:"brand_id",class:"block font-bold mb-2"},"Brand",-1)),y(d(re),{id:"brand_id",modelValue:d(r).brand_id,"onUpdate:modelValue":o[10]||(o[10]=s=>d(r).brand_id=s),options:e.brands,optionLabel:"name",optionValue:"id",placeholder:"Select Brand",class:"w-full"},null,8,["modelValue","options"])]),i("div",ql,[o[38]||(o[38]=i("label",{for:"tax_id",class:"block font-bold mb-2"},"Tax",-1)),y(d(re),{id:"tax_id",modelValue:d(r).tax_id,"onUpdate:modelValue":o[11]||(o[11]=s=>d(r).tax_id=s),options:e.taxes,optionLabel:"name",optionValue:"id",placeholder:"Select Tax",class:"w-full"},null,8,["modelValue","options"])]),i("div",yl,[o[39]||(o[39]=i("label",{for:"tag_ids",class:"block font-bold mb-2"},"Tags",-1)),y(d(ie),{id:"tag_ids",modelValue:d(r).tag_ids,"onUpdate:modelValue":o[12]||(o[12]=s=>d(r).tag_ids=s),options:e.tags,optionLabel:"name",optionValue:"id",display:"chip",placeholder:"Select Tags",class:"w-full"},null,8,["modelValue","options"])])])]),i("div",null,[o[46]||(o[46]=i("h3",{class:"text-xl font-semibold uppercase mb-3"}," Physical Properties ",-1)),i("div",gl,[i("div",vl,[o[41]||(o[41]=i("label",{for:"weight",class:"block font-bold mb-2"},"Weight (kg)",-1)),y(d(I),{id:"weight",modelValue:d(r).weight,"onUpdate:modelValue":o[13]||(o[13]=s=>d(r).weight=s),modelModifiers:{number:!0},class:"w-full",min:0,placeholder:"e.g. 1.5"},null,8,["modelValue"])]),i("div",kl,[o[42]||(o[42]=i("label",{class:"block font-bold mb-2"},"Dimensions (cm)",-1)),i("div",wl,[y(d(I),{modelValue:d(r).dimensions.length,"onUpdate:modelValue":o[14]||(o[14]=s=>d(r).dimensions.length=s),modelModifiers:{number:!0},min:0,placeholder:"L"},null,8,["modelValue"]),y(d(I),{modelValue:d(r).dimensions.width,"onUpdate:modelValue":o[15]||(o[15]=s=>d(r).dimensions.width=s),modelModifiers:{number:!0},min:0,placeholder:"W"},null,8,["modelValue"]),y(d(I),{modelValue:d(r).dimensions.height,"onUpdate:modelValue":o[16]||(o[16]=s=>d(r).dimensions.height=s),modelModifiers:{number:!0},min:0,placeholder:"H"},null,8,["modelValue"])]),o[43]||(o[43]=i("small",{class:"text-gray-500"},"Stored as JSON: { length, width, height }",-1))]),i("div",xl,[o[44]||(o[44]=i("label",{for:"materials",class:"block font-bold mb-2"},"Materials",-1)),y(d(K),{id:"materials",modelValue:N.value,"onUpdate:modelValue":o[17]||(o[17]=s=>N.value=s),modelModifiers:{trim:!0},class:"w-full",placeholder:"e.g. Cotton, Polyester"},null,8,["modelValue"]),o[45]||(o[45]=i("small",{class:"text-gray-500"},"Comma-separated, stored as array.",-1))])])]),i("div",null,[o[49]||(o[49]=i("h3",{class:"text-xl font-semibold uppercase mb-3"},"Product Image",-1)),i("div",Sl,[i("div",Cl,[o[47]||(o[47]=i("label",{for:"thumbnail",class:"block font-bold mb-2"},"Thumbnail",-1)),i("div",Ol,[P.value||d(r).thumbnail?(b(),q("div",Vl,[i("img",{src:P.value?P.value:d(we)(d(r).thumbnail),alt:"Product Thumbnail",class:"w-20 h-20 object-cover rounded"},null,8,Tl)])):k("",!0),y(d(Sn),{mode:"basic",name:"thumbnail",accept:"image/*",maxFileSize:2e6,onSelect:X,auto:!0,chooseLabel:"Browse"})]),o[48]||(o[48]=i("small",{class:"text-gray-500"},"Max size: 2MB. Accepted formats: JPEG, PNG, JPG, GIF",-1))])])]),d(r).type==="variable"?(b(),q("div",Il,[o[52]||(o[52]=i("h3",{class:"text-xl font-semibold uppercase mb-3"},"Variations",-1)),i("div",Kl,[o[50]||(o[50]=i("h4",{class:"font-semibold mb-3"},"Variant Attributes",-1)),i("div",Pl,[(b(!0),q(V,null,$(e.attributes,s=>(b(),q("div",{key:s.id,class:"mb-3"},[i("label",El,S(s.display_name||s.name),1),y(d(ie),{modelValue:E.value[s.id],"onUpdate:modelValue":g=>E.value[s.id]=g,options:(s.values||[]).map(g=>({id:g.id,label:g.display_value||g.value})),optionLabel:"label",optionValue:"id",display:"chip",placeholder:"Select values",class:"w-full"},null,8,["modelValue","onUpdate:modelValue","options"])]))),128))]),i("div",Ml,[y(d(z),{label:"Generate Variations",icon:"pi pi-sitemap",class:"p-button-sm",onClick:Ye}),y(d(z),{label:"Clear",icon:"pi pi-times",class:"p-button-sm p-button-secondary",onClick:Ze})])]),i("div",jl,[y(d(z),{label:"Add Variation",icon:"pi pi-plus",class:"p-button-sm",onClick:Ge}),h.value&&d(r).type==="variable"&&!d(r).variations.length?(b(),q("small",Nl," At least one variation is required for variable products. ")):k("",!0)]),d(r).variations.length?(b(),q("div",Ll,[i("table",Al,[o[51]||(o[51]=i("thead",{class:"sticky top-0 bg-white z-10"},[i("tr",{class:"border-b"},[i("th",{class:"p-2 text-left w-[180px]"},"SKU *"),i("th",{class:"p-2 text-left w-[120px]"},"Price *"),i("th",{class:"p-2 text-left w-[120px]"},"Discount"),i("th",{class:"p-2 text-left w-[280px]"}," Attribute Values * "),i("th",{class:"p-2 text-left w-[320px]"}," Warehouse Alert Qty * "),i("th",{class:"p-2 text-left w-[220px]"},"Image Path"),i("th",{class:"p-2 text-left w-[70px]"})])],-1)),i("tbody",null,[(b(!0),q(V,null,$(d(r).variations,(s,g)=>(b(),q("tr",{key:g,class:"border-b align-top"},[i("td",$l,[y(d(K),{modelValue:s.sku,"onUpdate:modelValue":v=>s.sku=v,modelModifiers:{trim:!0},class:w(["w-full",{"p-invalid ":h.value&&!s.sku}]),placeholder:"Variation SKU"},null,8,["modelValue","onUpdate:modelValue","class"])]),i("td",zl,[i("div",Fl,[y(d(I),{modelValue:s.price,"onUpdate:modelValue":v=>s.price=v,modelModifiers:{number:!0},min:0,class:w({"p-invalid":h.value&&!s.price})},null,8,["modelValue","onUpdate:modelValue","class"])])]),i("td",Dl,[y(d(I),{modelValue:s.discount_price,"onUpdate:modelValue":v=>s.discount_price=v,modelModifiers:{number:!0},min:0},null,8,["modelValue","onUpdate:modelValue"])]),i("td",_l,[i("div",Ul,[y(d(ie),{modelValue:s.attribute_value_ids,"onUpdate:modelValue":v=>s.attribute_value_ids=v,options:Be.value,optionLabel:"label",optionValue:"id",display:"chip",placeholder:"Select attribute values",class:w(["w-full",{"p-invalid":h.value&&(!s.attribute_value_ids||!s.attribute_value_ids.length)}])},null,8,["modelValue","onUpdate:modelValue","options","class"])])]),i("td",Bl,[i("div",Hl,[(b(!0),q(V,null,$(s.stocks,(v,L)=>{var ve;return b(),q("div",{key:v.warehouse_id??L,class:"grid grid-cols-12 gap-2 items-center"},[i("span",Rl,S(((ve=e.warehouses.find(ne=>ne.id===v.warehouse_id))==null?void 0:ve.name)||"Warehouse"),1),i("div",Wl,[y(d(I),{modelValue:v.alert_quantity,"onUpdate:modelValue":ne=>v.alert_quantity=ne,modelModifiers:{number:!0},class:"w-full",min:0,placeholder:"Alert"},null,8,["modelValue","onUpdate:modelValue"])])])}),128))])]),i("td",Ql,[y(d(K),{modelValue:s.image,"onUpdate:modelValue":v=>s.image=v,modelModifiers:{trim:!0},class:"w-full",placeholder:"e.g. products/hoodie/red-s.jpg"},null,8,["modelValue","onUpdate:modelValue"])]),i("td",Gl,[y(d(z),{icon:"pi pi-trash",class:"p-button-rounded p-button-text p-button-danger p-button-sm",onClick:v=>Je(g)},null,8,["onClick"])])]))),128))])])])):k("",!0),d(r).errors.variations?(b(),q("small",Jl,S(d(r).errors.variations),1)):k("",!0)])):k("",!0),i("div",null,[o[55]||(o[55]=i("h3",{class:"text-xl font-semibold uppercase mb-3"}," Description & Details ",-1)),i("div",Yl,[i("div",Zl,[o[53]||(o[53]=i("label",{for:"description",class:"block font-bold mb-2"},"Description",-1)),y(d(me),{modelValue:d(r).description,"onUpdate:modelValue":o[18]||(o[18]=s=>d(r).description=s),editorStyle:"height: 250px"},null,8,["modelValue"])]),i("div",Xl,[o[54]||(o[54]=i("label",{for:"additional_info",class:"block font-bold mb-2"},"Additional Information",-1)),y(d(me),{modelValue:d(r).additional_info,"onUpdate:modelValue":o[19]||(o[19]=s=>d(r).additional_info=s),editorStyle:"height: 150px"},null,8,["modelValue"])])])]),i("div",null,[o[59]||(o[59]=i("h3",{class:"text-xl font-semibold uppercase mb-3"}," SEO Information ",-1)),i("div",eo,[i("div",no,[o[56]||(o[56]=i("label",{for:"meta_title",class:"block font-bold mb-2"},"Meta Title",-1)),y(d(K),{id:"meta_title",modelValue:d(r).meta_title,"onUpdate:modelValue":o[20]||(o[20]=s=>d(r).meta_title=s),modelModifiers:{trim:!0},class:"w-full"},null,8,["modelValue"])]),i("div",to,[o[57]||(o[57]=i("label",{for:"meta_keywords",class:"block font-bold mb-2"},"Meta Keywords",-1)),y(d(K),{id:"meta_keywords",modelValue:d(r).meta_keywords,"onUpdate:modelValue":o[21]||(o[21]=s=>d(r).meta_keywords=s),modelModifiers:{trim:!0},class:"w-full"},null,8,["modelValue"])]),i("div",lo,[o[58]||(o[58]=i("label",{for:"meta_description",class:"block font-bold mb-2"},"Meta Description",-1)),y(d(Cn),{id:"meta_description",modelValue:d(r).meta_description,"onUpdate:modelValue":o[22]||(o[22]=s=>d(r).meta_description=s),rows:"4",class:"w-full"},null,8,["modelValue"])])])]),i("div",oo,[y(d(z),{type:"button",label:"Cancel",icon:"pi pi-times",class:"p-button-text",onClick:en}),y(d(z),{type:"submit",label:e.isEditing?"Update Product":"Create Product",icon:"pi pi-check"},null,8,["label"])])],32))}},Eo=zn(ro,[["__scopeId","data-v-631d0afc"]]);export{Eo as default};
