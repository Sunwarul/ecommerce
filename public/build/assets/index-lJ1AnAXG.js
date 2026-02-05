import{B as e,l as c,c as u,o as b,b as d,m as r}from"./app-DsieonZu.js";import{a as l}from"./index-ctHPnxG6.js";var s=function(n){var o=n.dt;return`
.p-radiobutton {
    position: relative;
    display: inline-flex;
    user-select: none;
    vertical-align: bottom;
    width: `.concat(o("radiobutton.width"),`;
    height: `).concat(o("radiobutton.height"),`;
}

.p-radiobutton-input {
    cursor: pointer;
    appearance: none;
    position: absolute;
    top: 0;
    inset-inline-start: 0;
    width: 100%;
    height: 100%;
    padding: 0;
    margin: 0;
    opacity: 0;
    z-index: 1;
    outline: 0 none;
    border: 1px solid transparent;
    border-radius: 50%;
}

.p-radiobutton-box {
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    border: 1px solid `).concat(o("radiobutton.border.color"),`;
    background: `).concat(o("radiobutton.background"),`;
    width: `).concat(o("radiobutton.width"),`;
    height: `).concat(o("radiobutton.height"),`;
    transition: background `).concat(o("radiobutton.transition.duration"),", color ").concat(o("radiobutton.transition.duration"),", border-color ").concat(o("radiobutton.transition.duration"),", box-shadow ").concat(o("radiobutton.transition.duration"),", outline-color ").concat(o("radiobutton.transition.duration"),`;
    outline-color: transparent;
    box-shadow: `).concat(o("radiobutton.shadow"),`;
}

.p-radiobutton-icon {
    transition-duration: `).concat(o("radiobutton.transition.duration"),`;
    background: transparent;
    font-size: `).concat(o("radiobutton.icon.size"),`;
    width: `).concat(o("radiobutton.icon.size"),`;
    height: `).concat(o("radiobutton.icon.size"),`;
    border-radius: 50%;
    backface-visibility: hidden;
    transform: translateZ(0) scale(0.1);
}

.p-radiobutton:not(.p-disabled):has(.p-radiobutton-input:hover) .p-radiobutton-box {
    border-color: `).concat(o("radiobutton.hover.border.color"),`;
}

.p-radiobutton-checked .p-radiobutton-box {
    border-color: `).concat(o("radiobutton.checked.border.color"),`;
    background: `).concat(o("radiobutton.checked.background"),`;
}

.p-radiobutton-checked .p-radiobutton-box .p-radiobutton-icon {
    background: `).concat(o("radiobutton.icon.checked.color"),`;
    transform: translateZ(0) scale(1, 1);
    visibility: visible;
}

.p-radiobutton-checked:not(.p-disabled):has(.p-radiobutton-input:hover) .p-radiobutton-box {
    border-color: `).concat(o("radiobutton.checked.hover.border.color"),`;
    background: `).concat(o("radiobutton.checked.hover.background"),`;
}

.p-radiobutton:not(.p-disabled):has(.p-radiobutton-input:hover).p-radiobutton-checked .p-radiobutton-box .p-radiobutton-icon {
    background: `).concat(o("radiobutton.icon.checked.hover.color"),`;
}

.p-radiobutton:not(.p-disabled):has(.p-radiobutton-input:focus-visible) .p-radiobutton-box {
    border-color: `).concat(o("radiobutton.focus.border.color"),`;
    box-shadow: `).concat(o("radiobutton.focus.ring.shadow"),`;
    outline: `).concat(o("radiobutton.focus.ring.width")," ").concat(o("radiobutton.focus.ring.style")," ").concat(o("radiobutton.focus.ring.color"),`;
    outline-offset: `).concat(o("radiobutton.focus.ring.offset"),`;
}

.p-radiobutton-checked:not(.p-disabled):has(.p-radiobutton-input:focus-visible) .p-radiobutton-box {
    border-color: `).concat(o("radiobutton.checked.focus.border.color"),`;
}

.p-radiobutton.p-invalid > .p-radiobutton-box {
    border-color: `).concat(o("radiobutton.invalid.border.color"),`;
}

.p-radiobutton.p-variant-filled .p-radiobutton-box {
    background: `).concat(o("radiobutton.filled.background"),`;
}

.p-radiobutton.p-variant-filled.p-radiobutton-checked .p-radiobutton-box {
    background: `).concat(o("radiobutton.checked.background"),`;
}

.p-radiobutton.p-variant-filled:not(.p-disabled):has(.p-radiobutton-input:hover).p-radiobutton-checked .p-radiobutton-box {
    background: `).concat(o("radiobutton.checked.hover.background"),`;
}

.p-radiobutton.p-disabled {
    opacity: 1;
}

.p-radiobutton.p-disabled .p-radiobutton-box {
    background: `).concat(o("radiobutton.disabled.background"),`;
    border-color: `).concat(o("radiobutton.checked.disabled.border.color"),`;
}

.p-radiobutton-checked.p-disabled .p-radiobutton-box .p-radiobutton-icon {
    background: `).concat(o("radiobutton.icon.disabled.color"),`;
}

.p-radiobutton-sm,
.p-radiobutton-sm .p-radiobutton-box {
    width: `).concat(o("radiobutton.sm.width"),`;
    height: `).concat(o("radiobutton.sm.height"),`;
}

.p-radiobutton-sm .p-radiobutton-icon {
    font-size: `).concat(o("radiobutton.icon.sm.size"),`;
    width: `).concat(o("radiobutton.icon.sm.size"),`;
    height: `).concat(o("radiobutton.icon.sm.size"),`;
}

.p-radiobutton-lg,
.p-radiobutton-lg .p-radiobutton-box {
    width: `).concat(o("radiobutton.lg.width"),`;
    height: `).concat(o("radiobutton.lg.height"),`;
}

.p-radiobutton-lg .p-radiobutton-icon {
    font-size: `).concat(o("radiobutton.icon.lg.size"),`;
    width: `).concat(o("radiobutton.icon.lg.size"),`;
    height: `).concat(o("radiobutton.icon.lg.size"),`;
}
`)},p={root:function(n){var o=n.instance,i=n.props;return["p-radiobutton p-component",{"p-radiobutton-checked":o.checked,"p-disabled":i.disabled,"p-invalid":o.$pcRadioButtonGroup?o.$pcRadioButtonGroup.$invalid:o.$invalid,"p-variant-filled":o.$variant==="filled","p-radiobutton-sm p-inputfield-sm":i.size==="small","p-radiobutton-lg p-inputfield-lg":i.size==="large"}]},box:"p-radiobutton-box",input:"p-radiobutton-input",icon:"p-radiobutton-icon"},h=e.extend({name:"radiobutton",theme:s,classes:p}),g={name:"BaseRadioButton",extends:l,props:{value:null,binary:Boolean,readonly:{type:Boolean,default:!1},tabindex:{type:Number,default:null},inputId:{type:String,default:null},inputClass:{type:[String,Object],default:null},inputStyle:{type:Object,default:null},ariaLabelledby:{type:String,default:null},ariaLabel:{type:String,default:null}},style:h,provide:function(){return{$pcRadioButton:this,$parentInstance:this}}},f={name:"RadioButton",extends:g,inheritAttrs:!1,emits:["change","focus","blur"],inject:{$pcRadioButtonGroup:{default:void 0}},methods:{getPTOptions:function(n){var o=n==="root"?this.ptmi:this.ptm;return o(n,{context:{checked:this.checked,disabled:this.disabled}})},onChange:function(n){if(!this.disabled&&!this.readonly){var o=this.binary?!this.checked:this.value;this.$pcRadioButtonGroup?this.$pcRadioButtonGroup.writeValue(o,n):this.writeValue(o,n),this.$emit("change",n)}},onFocus:function(n){this.$emit("focus",n)},onBlur:function(n){var o,i;this.$emit("blur",n),(o=(i=this.formField).onBlur)===null||o===void 0||o.call(i,n)}},computed:{groupName:function(){return this.$pcRadioButtonGroup?this.$pcRadioButtonGroup.groupName:this.$formName},checked:function(){var n=this.$pcRadioButtonGroup?this.$pcRadioButtonGroup.d_value:this.d_value;return n!=null&&(this.binary?!!n:c(n,this.value))}}},m=["data-p-checked","data-p-disabled"],v=["id","value","name","checked","tabindex","disabled","readonly","aria-labelledby","aria-label","aria-invalid"];function k(t,n,o,i,y,a){return b(),u("div",r({class:t.cx("root")},a.getPTOptions("root"),{"data-p-checked":a.checked,"data-p-disabled":t.disabled}),[d("input",r({id:t.inputId,type:"radio",class:[t.cx("input"),t.inputClass],style:t.inputStyle,value:t.value,name:a.groupName,checked:a.checked,tabindex:t.tabindex,disabled:t.disabled,readonly:t.readonly,"aria-labelledby":t.ariaLabelledby,"aria-label":t.ariaLabel,"aria-invalid":t.invalid||void 0,onFocus:n[0]||(n[0]=function(){return a.onFocus&&a.onFocus.apply(a,arguments)}),onBlur:n[1]||(n[1]=function(){return a.onBlur&&a.onBlur.apply(a,arguments)}),onChange:n[2]||(n[2]=function(){return a.onChange&&a.onChange.apply(a,arguments)})},a.getPTOptions("input")),null,16,v),d("div",r({class:t.cx("box")},a.getPTOptions("box")),[d("div",r({class:t.cx("icon")},a.getPTOptions("icon")),null,16)],16)],16,m)}f.render=k;export{f as s};
