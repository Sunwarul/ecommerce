import{B as L,n as w,ae as v,ad as M,p as S,s as C,z as B,c as p,o as h,d as f,a as d,m as c,w as g,T as V,b as u,N as D}from"./app-DsieonZu.js";import{s as x,O,C as R,Z as m}from"./index-DQNAyf7j.js";import{b as E,s as I}from"./index-ctHPnxG6.js";import{s as U}from"./index-lraT3xMD.js";import"./index-Cs22S3sc.js";import"./index-6gGX_01L.js";import"./index-CvW0F4Ik.js";import"./index-C1VHlqZY.js";import"./index-BCjHW8ac.js";import"./index-B1YzyBAC.js";import"./index-CfjDihnE.js";var P=function(e){var n=e.dt;return`
.p-colorpicker {
    display: inline-block;
    position: relative;
}

.p-colorpicker-dragging {
    cursor: pointer;
}

.p-colorpicker-preview {
    width: `.concat(n("colorpicker.preview.width"),`;
    height: `).concat(n("colorpicker.preview.height"),`;
    padding: 0;
    border: 0 none;
    border-radius: `).concat(n("colorpicker.preview.border.radius"),`;
    transition: background `).concat(n("colorpicker.transition.duration"),", color ").concat(n("colorpicker.transition.duration"),", border-color ").concat(n("colorpicker.transition.duration"),", outline-color ").concat(n("colorpicker.transition.duration"),", box-shadow ").concat(n("colorpicker.transition.duration"),`;
    outline-color: transparent;
    cursor: pointer;
}

.p-colorpicker-preview:enabled:focus-visible {
    border-color: `).concat(n("colorpicker.preview.focus.border.color"),`;
    box-shadow: `).concat(n("colorpicker.preview.focus.ring.shadow"),`;
    outline: `).concat(n("colorpicker.preview.focus.ring.width")," ").concat(n("colorpicker.preview.focus.ring.style")," ").concat(n("colorpicker.preview.focus.ring.color"),`;
    outline-offset: `).concat(n("colorpicker.preview.focus.ring.offset"),`;
}

.p-colorpicker-panel {
    background: `).concat(n("colorpicker.panel.background"),`;
    border: 1px solid `).concat(n("colorpicker.panel.border.color"),`;
    border-radius: `).concat(n("colorpicker.panel.border.radius"),`;
    box-shadow: `).concat(n("colorpicker.panel.shadow"),`;
    width: 193px;
    height: 166px;
    position: absolute;
    top: 0;
    left: 0;
}

.p-colorpicker-panel-inline {
    box-shadow: none;
    position: static;
}

.p-colorpicker-content {
    position: relative;
}

.p-colorpicker-color-selector {
    width: 150px;
    height: 150px;
    inset-block-start: 8px;
    inset-inline-start: 8px;
    position: absolute;
}

.p-colorpicker-color-background {
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, #000 0%, rgba(0, 0, 0, 0) 100%), linear-gradient(to right, #fff 0%, rgba(255, 255, 255, 0) 100%);
}

.p-colorpicker-color-handle {
    position: absolute;
    inset-block-start: 0px;
    inset-inline-start: 150px;
    border-radius: 100%;
    width: 10px;
    height: 10px;
    border-width: 1px;
    border-style: solid;
    margin: -5px 0 0 -5px;
    cursor: pointer;
    opacity: 0.85;
    border-color: `).concat(n("colorpicker.handle.color"),`;
}

.p-colorpicker-hue {
    width: 17px;
    height: 150px;
    inset-block-start: 8px;
    inset-inline-start: 167px;
    position: absolute;
    opacity: 0.85;
    background: linear-gradient(0deg,
        red 0,
        #ff0 17%,
        #0f0 33%,
        #0ff 50%,
        #00f 67%,
        #f0f 83%,
        red);
}

.p-colorpicker-hue-handle {
    position: absolute;
    inset-block-start: 150px;
    inset-inline-start: 0px;
    width: 21px;
    margin-inline-start: -2px;
    margin-block-start: -5px;
    height: 10px;
    border-width: 2px;
    border-style: solid;
    opacity: 0.85;
    cursor: pointer;
    border-color: `).concat(n("colorpicker.handle.color"),`;
}
`)},T={root:"p-colorpicker p-component",preview:function(e){var n=e.props;return["p-colorpicker-preview",{"p-disabled":n.disabled}]},panel:function(e){var n=e.instance,i=e.props;return["p-colorpicker-panel",{"p-colorpicker-panel-inline":i.inline,"p-disabled":i.disabled,"p-invalid":n.$invalid}]},colorSelector:"p-colorpicker-color-selector",colorBackground:"p-colorpicker-color-background",colorHandle:"p-colorpicker-color-handle",hue:"p-colorpicker-hue",hueHandle:"p-colorpicker-hue-handle"},j=L.extend({name:"colorpicker",theme:P,classes:T}),X={name:"BaseColorPicker",extends:E,props:{defaultColor:{type:null,default:"ff0000"},inline:{type:Boolean,default:!1},format:{type:String,default:"hex"},tabindex:{type:String,default:null},autoZIndex:{type:Boolean,default:!0},baseZIndex:{type:Number,default:0},appendTo:{type:[String,Object],default:"body"},inputId:{type:String,default:null},panelClass:null,overlayClass:null},style:j,provide:function(){return{$pcColorPicker:this,$parentInstance:this}}},H={name:"ColorPicker",extends:X,inheritAttrs:!1,emits:["change","show","hide"],data:function(){return{overlayVisible:!1}},hsbValue:null,outsideClickListener:null,documentMouseMoveListener:null,documentMouseUpListener:null,scrollHandler:null,resizeListener:null,hueDragging:null,colorDragging:null,selfUpdate:null,picker:null,colorSelector:null,colorHandle:null,hueView:null,hueHandle:null,watch:{modelValue:{immediate:!0,handler:function(e){this.hsbValue=this.toHSB(e),this.selfUpdate?this.selfUpdate=!1:this.updateUI()}}},beforeUnmount:function(){this.unbindOutsideClickListener(),this.unbindDragListeners(),this.unbindResizeListener(),this.scrollHandler&&(this.scrollHandler.destroy(),this.scrollHandler=null),this.picker&&this.autoZIndex&&m.clear(this.picker),this.clearRefs()},mounted:function(){this.updateUI()},methods:{pickColor:function(e){var n=this.colorSelector.getBoundingClientRect(),i=n.top+(window.pageYOffset||document.documentElement.scrollTop||document.body.scrollTop||0),r=n.left+document.body.scrollLeft,o=Math.floor(100*Math.max(0,Math.min(150,(e.pageX||e.changedTouches[0].pageX)-r))/150),s=Math.floor(100*(150-Math.max(0,Math.min(150,(e.pageY||e.changedTouches[0].pageY)-i)))/150);this.hsbValue=this.validateHSB({h:this.hsbValue.h,s:o,b:s}),this.selfUpdate=!0,this.updateColorHandle(),this.updateInput(),this.updateModel(e)},pickHue:function(e){var n=this.hueView.getBoundingClientRect().top+(window.pageYOffset||document.documentElement.scrollTop||document.body.scrollTop||0);this.hsbValue=this.validateHSB({h:Math.floor(360*(150-Math.max(0,Math.min(150,(e.pageY||e.changedTouches[0].pageY)-n)))/150),s:100,b:100}),this.selfUpdate=!0,this.updateColorSelector(),this.updateHue(),this.updateModel(e),this.updateInput()},updateModel:function(e){var n=this.d_value;switch(this.format){case"hex":n=this.HSBtoHEX(this.hsbValue);break;case"rgb":n=this.HSBtoRGB(this.hsbValue);break;case"hsb":n=this.hsbValue;break}this.writeValue(n,e),this.$emit("change",{event:e,value:n})},updateColorSelector:function(){if(this.colorSelector){var e=this.validateHSB({h:this.hsbValue.h,s:100,b:100});this.colorSelector.style.backgroundColor="#"+this.HSBtoHEX(e)}},updateColorHandle:function(){this.colorHandle&&(this.colorHandle.style.left=Math.floor(150*this.hsbValue.s/100)+"px",this.colorHandle.style.top=Math.floor(150*(100-this.hsbValue.b)/100)+"px")},updateHue:function(){this.hueHandle&&(this.hueHandle.style.top=Math.floor(150-150*this.hsbValue.h/360)+"px")},updateInput:function(){this.$refs.input&&(this.$refs.input.style.backgroundColor="#"+this.HSBtoHEX(this.hsbValue))},updateUI:function(){this.updateHue(),this.updateColorHandle(),this.updateInput(),this.updateColorSelector()},validateHSB:function(e){return{h:Math.min(360,Math.max(0,e.h)),s:Math.min(100,Math.max(0,e.s)),b:Math.min(100,Math.max(0,e.b))}},validateRGB:function(e){return{r:Math.min(255,Math.max(0,e.r)),g:Math.min(255,Math.max(0,e.g)),b:Math.min(255,Math.max(0,e.b))}},validateHEX:function(e){var n=6-e.length;if(n>0){for(var i=[],r=0;r<n;r++)i.push("0");i.push(e),e=i.join("")}return e},HEXtoRGB:function(e){var n=parseInt(e.indexOf("#")>-1?e.substring(1):e,16);return{r:n>>16,g:(n&65280)>>8,b:n&255}},HEXtoHSB:function(e){return this.RGBtoHSB(this.HEXtoRGB(e))},RGBtoHSB:function(e){var n={h:0,s:0,b:0},i=Math.min(e.r,e.g,e.b),r=Math.max(e.r,e.g,e.b),o=r-i;return n.b=r,n.s=r!==0?255*o/r:0,n.s!==0?e.r===r?n.h=(e.g-e.b)/o:e.g===r?n.h=2+(e.b-e.r)/o:n.h=4+(e.r-e.g)/o:n.h=-1,n.h*=60,n.h<0&&(n.h+=360),n.s*=100/255,n.b*=100/255,n},HSBtoRGB:function(e){var n={r:null,g:null,b:null},i=Math.round(e.h),r=Math.round(e.s*255/100),o=Math.round(e.b*255/100);if(r===0)n={r:o,g:o,b:o};else{var s=o,l=(255-r)*o/255,a=(s-l)*(i%60)/60;i===360&&(i=0),i<60?(n.r=s,n.b=l,n.g=l+a):i<120?(n.g=s,n.b=l,n.r=s-a):i<180?(n.g=s,n.r=l,n.b=l+a):i<240?(n.b=s,n.r=l,n.g=s-a):i<300?(n.b=s,n.g=l,n.r=l+a):i<360?(n.r=s,n.g=l,n.b=s-a):(n.r=0,n.g=0,n.b=0)}return{r:Math.round(n.r),g:Math.round(n.g),b:Math.round(n.b)}},RGBtoHEX:function(e){var n=[e.r.toString(16),e.g.toString(16),e.b.toString(16)];for(var i in n)n[i].length===1&&(n[i]="0"+n[i]);return n.join("")},HSBtoHEX:function(e){return this.RGBtoHEX(this.HSBtoRGB(e))},toHSB:function(e){var n;if(e)switch(this.format){case"hex":n=this.HEXtoHSB(e);break;case"rgb":n=this.RGBtoHSB(e);break;case"hsb":n=e;break}else n=this.HEXtoHSB(this.defaultColor);return n},onOverlayEnter:function(e){this.updateUI(),this.alignOverlay(),this.bindOutsideClickListener(),this.bindScrollListener(),this.bindResizeListener(),this.autoZIndex&&m.set("overlay",e,this.baseZIndex,this.$primevue.config.zIndex.overlay),this.$emit("show")},onOverlayLeave:function(){this.unbindOutsideClickListener(),this.unbindScrollListener(),this.unbindResizeListener(),this.clearRefs(),this.$emit("hide")},onOverlayAfterLeave:function(e){this.autoZIndex&&m.clear(e)},alignOverlay:function(){this.appendTo==="self"?S(this.picker,this.$refs.input):C(this.picker,this.$refs.input)},onInputClick:function(){this.disabled||(this.overlayVisible=!this.overlayVisible)},onInputKeydown:function(e){switch(e.code){case"Space":this.overlayVisible=!this.overlayVisible,e.preventDefault();break;case"Escape":case"Tab":this.overlayVisible=!1;break}},onInputBlur:function(e){var n,i;(n=(i=this.formField).onBlur)===null||n===void 0||n.call(i)},onColorMousedown:function(e){this.disabled||(this.bindDragListeners(),this.onColorDragStart(e))},onColorDragStart:function(e){this.disabled||(this.colorDragging=!0,this.pickColor(e),this.$el.setAttribute("p-colorpicker-dragging","true"),!this.isUnstyled&&v(this.$el,"p-colorpicker-dragging"),e.preventDefault())},onDrag:function(e){this.colorDragging&&(this.pickColor(e),e.preventDefault()),this.hueDragging&&(this.pickHue(e),e.preventDefault())},onDragEnd:function(){this.colorDragging=!1,this.hueDragging=!1,this.$el.setAttribute("p-colorpicker-dragging","false"),!this.isUnstyled&&M(this.$el,"p-colorpicker-dragging"),this.unbindDragListeners()},onHueMousedown:function(e){this.disabled||(this.bindDragListeners(),this.onHueDragStart(e))},onHueDragStart:function(e){this.disabled||(this.hueDragging=!0,this.pickHue(e),!this.isUnstyled&&v(this.$el,"p-colorpicker-dragging"))},isInputClicked:function(e){return this.$refs.input&&this.$refs.input.isSameNode(e.target)},bindDragListeners:function(){this.bindDocumentMouseMoveListener(),this.bindDocumentMouseUpListener()},unbindDragListeners:function(){this.unbindDocumentMouseMoveListener(),this.unbindDocumentMouseUpListener()},bindOutsideClickListener:function(){var e=this;this.outsideClickListener||(this.outsideClickListener=function(n){e.overlayVisible&&e.picker&&!e.picker.contains(n.target)&&!e.isInputClicked(n)&&(e.overlayVisible=!1)},document.addEventListener("click",this.outsideClickListener))},unbindOutsideClickListener:function(){this.outsideClickListener&&(document.removeEventListener("click",this.outsideClickListener),this.outsideClickListener=null)},bindScrollListener:function(){var e=this;this.scrollHandler||(this.scrollHandler=new R(this.$refs.container,function(){e.overlayVisible&&(e.overlayVisible=!1)})),this.scrollHandler.bindScrollListener()},unbindScrollListener:function(){this.scrollHandler&&this.scrollHandler.unbindScrollListener()},bindResizeListener:function(){var e=this;this.resizeListener||(this.resizeListener=function(){e.overlayVisible&&!w()&&(e.overlayVisible=!1)},window.addEventListener("resize",this.resizeListener))},unbindResizeListener:function(){this.resizeListener&&(window.removeEventListener("resize",this.resizeListener),this.resizeListener=null)},bindDocumentMouseMoveListener:function(){this.documentMouseMoveListener||(this.documentMouseMoveListener=this.onDrag.bind(this),document.addEventListener("mousemove",this.documentMouseMoveListener))},unbindDocumentMouseMoveListener:function(){this.documentMouseMoveListener&&(document.removeEventListener("mousemove",this.documentMouseMoveListener),this.documentMouseMoveListener=null)},bindDocumentMouseUpListener:function(){this.documentMouseUpListener||(this.documentMouseUpListener=this.onDragEnd.bind(this),document.addEventListener("mouseup",this.documentMouseUpListener))},unbindDocumentMouseUpListener:function(){this.documentMouseUpListener&&(document.removeEventListener("mouseup",this.documentMouseUpListener),this.documentMouseUpListener=null)},pickerRef:function(e){this.picker=e},colorSelectorRef:function(e){this.colorSelector=e},colorHandleRef:function(e){this.colorHandle=e},hueViewRef:function(e){this.hueView=e},hueHandleRef:function(e){this.hueHandle=e},clearRefs:function(){this.picker=null,this.colorSelector=null,this.colorHandle=null,this.hueView=null,this.hueHandle=null},onOverlayClick:function(e){O.emit("overlay-click",{originalEvent:e,target:this.$el})}},components:{Portal:x}};function b(t){"@babel/helpers - typeof";return b=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(e){return typeof e}:function(e){return e&&typeof Symbol=="function"&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},b(t)}function k(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(t);e&&(i=i.filter(function(r){return Object.getOwnPropertyDescriptor(t,r).enumerable})),n.push.apply(n,i)}return n}function y(t){for(var e=1;e<arguments.length;e++){var n=arguments[e]!=null?arguments[e]:{};e%2?k(Object(n),!0).forEach(function(i){z(t,i,n[i])}):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):k(Object(n)).forEach(function(i){Object.defineProperty(t,i,Object.getOwnPropertyDescriptor(n,i))})}return t}function z(t,e,n){return(e=G(e))in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t}function G(t){var e=A(t,"string");return b(e)=="symbol"?e:e+""}function A(t,e){if(b(t)!="object"||!t)return t;var n=t[Symbol.toPrimitive];if(n!==void 0){var i=n.call(t,e);if(b(i)!="object")return i;throw new TypeError("@@toPrimitive must return a primitive value.")}return(e==="string"?String:Number)(t)}var Z=["id","tabindex","disabled"];function K(t,e,n,i,r,o){var s=B("Portal");return h(),p("div",c({ref:"container",class:t.cx("root")},t.ptmi("root")),[t.inline?f("",!0):(h(),p("input",c({key:0,ref:"input",id:t.inputId,type:"text",class:t.cx("preview"),readonly:"",tabindex:t.tabindex,disabled:t.disabled,onClick:e[0]||(e[0]=function(){return o.onInputClick&&o.onInputClick.apply(o,arguments)}),onKeydown:e[1]||(e[1]=function(){return o.onInputKeydown&&o.onInputKeydown.apply(o,arguments)}),onBlur:e[2]||(e[2]=function(){return o.onInputBlur&&o.onInputBlur.apply(o,arguments)})},t.ptm("preview")),null,16,Z)),d(s,{appendTo:t.appendTo,disabled:t.inline},{default:g(function(){return[d(V,c({name:"p-connected-overlay",onEnter:o.onOverlayEnter,onLeave:o.onOverlayLeave,onAfterLeave:o.onOverlayAfterLeave},t.ptm("transition")),{default:g(function(){return[t.inline||r.overlayVisible?(h(),p("div",c({key:0,ref:o.pickerRef,class:[t.cx("panel"),t.panelClass,t.overlayClass],onClick:e[11]||(e[11]=function(){return o.onOverlayClick&&o.onOverlayClick.apply(o,arguments)})},y(y({},t.ptm("panel")),t.ptm("overlay"))),[u("div",c({class:t.cx("content")},t.ptm("content")),[u("div",c({ref:o.colorSelectorRef,class:t.cx("colorSelector"),onMousedown:e[3]||(e[3]=function(l){return o.onColorMousedown(l)}),onTouchstart:e[4]||(e[4]=function(l){return o.onColorDragStart(l)}),onTouchmove:e[5]||(e[5]=function(l){return o.onDrag(l)}),onTouchend:e[6]||(e[6]=function(l){return o.onDragEnd()})},t.ptm("colorSelector")),[u("div",c({class:t.cx("colorBackground")},t.ptm("colorBackground")),[u("div",c({ref:o.colorHandleRef,class:t.cx("colorHandle")},t.ptm("colorHandle")),null,16)],16)],16),u("div",c({ref:o.hueViewRef,class:t.cx("hue"),onMousedown:e[7]||(e[7]=function(l){return o.onHueMousedown(l)}),onTouchstart:e[8]||(e[8]=function(l){return o.onHueDragStart(l)}),onTouchmove:e[9]||(e[9]=function(l){return o.onDrag(l)}),onTouchend:e[10]||(e[10]=function(l){return o.onDragEnd()})},t.ptm("hue")),[u("div",c({ref:o.hueHandleRef,class:t.cx("hueHandle")},t.ptm("hueHandle")),null,16)],16)],16)],16)):f("",!0)]}),_:1},16,["onEnter","onLeave","onAfterLeave"])]}),_:1},8,["appendTo","disabled"])],16)}H.render=K;const N={class:"flex flex-col gap-6"},F={key:0,class:"text-red-500"},Y={key:0,class:"text-red-500"},$={key:0},q={class:"flex gap-3 items-center"},se={__name:"Form",props:{form:{type:Object,required:!0},attributes:{type:Array,required:!0},submitted:{type:Boolean,default:!1}},setup(t){const e=t,n=D(()=>{const i=e.attributes.find(r=>r.id===e.form.attribute_id);return(i==null?void 0:i.type)==="color"});return(i,r)=>{const o=U,s=I,l=H;return h(),p("div",N,[u("div",null,[r[5]||(r[5]=u("label",{class:"block font-bold mb-2"},"Attribute",-1)),d(o,{modelValue:t.form.attribute_id,"onUpdate:modelValue":r[0]||(r[0]=a=>t.form.attribute_id=a),options:t.attributes,optionLabel:"display_name",optionValue:"id",placeholder:"Select attribute",class:"w-full",invalid:t.submitted&&!t.form.attribute_id},null,8,["modelValue","options","invalid"]),t.submitted&&!t.form.attribute_id?(h(),p("small",F," Attribute is required. ")):f("",!0)]),u("div",null,[r[6]||(r[6]=u("label",{class:"block font-bold mb-2"},"Value",-1)),d(s,{modelValue:t.form.value,"onUpdate:modelValue":r[1]||(r[1]=a=>t.form.value=a),modelModifiers:{trim:!0},class:"w-full",invalid:t.submitted&&!t.form.value},null,8,["modelValue","invalid"]),t.submitted&&!t.form.value?(h(),p("small",Y," Value is required. ")):f("",!0)]),u("div",null,[r[7]||(r[7]=u("label",{class:"block font-bold mb-2"},"Display Value",-1)),d(s,{modelValue:t.form.display_value,"onUpdate:modelValue":r[2]||(r[2]=a=>t.form.display_value=a),class:"w-full"},null,8,["modelValue"]),r[8]||(r[8]=u("small",{class:"text-gray-500 text-sm"}," Optional (e.g. “Bright Red”) ",-1))]),n.value?(h(),p("div",$,[r[9]||(r[9]=u("label",{class:"block font-bold mb-2"},"Color",-1)),u("div",q,[d(l,{modelValue:t.form.color_code,"onUpdate:modelValue":r[3]||(r[3]=a=>t.form.color_code=a),format:"hex"},null,8,["modelValue"]),d(s,{modelValue:t.form.color_code,"onUpdate:modelValue":r[4]||(r[4]=a=>t.form.color_code=a),placeholder:"#FF0000",class:"w-full"},null,8,["modelValue"])])])):f("",!0)])}}};export{se as default};
