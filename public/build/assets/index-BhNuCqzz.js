import{i as r,B as d,m as a,c as u,o as c}from"./app-Cnptsgld.js";import{s as p}from"./index-B-TGdPZ3.js";var s={name:"BaseEditableHolder",extends:p,emits:["update:modelValue","value-change"],props:{modelValue:{type:null,default:void 0},defaultValue:{type:null,default:void 0},name:{type:String,default:void 0},invalid:{type:Boolean,default:void 0},disabled:{type:Boolean,default:!1},formControl:{type:Object,default:void 0}},inject:{$parentInstance:{default:void 0},$pcForm:{default:void 0},$pcFormField:{default:void 0}},data:function(){return{d_value:this.defaultValue||this.modelValue}},watch:{modelValue:function(n){this.d_value=n},defaultValue:function(n){this.d_value=n},$formName:{immediate:!0,handler:function(n){var t,e;this.formField=((t=this.$pcForm)===null||t===void 0||(e=t.register)===null||e===void 0?void 0:e.call(t,n,this.$formControl))||{}}},$formControl:{immediate:!0,handler:function(n){var t,e;this.formField=((t=this.$pcForm)===null||t===void 0||(e=t.register)===null||e===void 0?void 0:e.call(t,this.$formName,n))||{}}},$formDefaultValue:{immediate:!0,handler:function(n){this.d_value!==n&&(this.d_value=n)}}},formField:{},methods:{writeValue:function(n,t){var e,o;this.controlled&&(this.d_value=n,this.$emit("update:modelValue",n)),this.$emit("value-change",n),(e=(o=this.formField).onChange)===null||e===void 0||e.call(o,{originalEvent:t,value:n})}},computed:{$filled:function(){return r(this.d_value)},$invalid:function(){var n,t,e,o;return(n=(t=this.invalid)!==null&&t!==void 0?t:(e=this.$pcFormField)===null||e===void 0||(e=e.$field)===null||e===void 0?void 0:e.invalid)!==null&&n!==void 0?n:(o=this.$pcForm)===null||o===void 0||(o=o.states)===null||o===void 0||(o=o[this.$formName])===null||o===void 0?void 0:o.invalid},$formName:function(){var n;return this.name||((n=this.$formControl)===null||n===void 0?void 0:n.name)},$formControl:function(){var n;return this.formControl||((n=this.$pcFormField)===null||n===void 0?void 0:n.formControl)},$formDefaultValue:function(){var n,t,e,o;return(n=(t=this.d_value)!==null&&t!==void 0?t:(e=this.$pcFormField)===null||e===void 0?void 0:e.initialValue)!==null&&n!==void 0?n:(o=this.$pcForm)===null||o===void 0||(o=o.initialValues)===null||o===void 0?void 0:o[this.$formName]},controlled:function(){return this.$inProps.hasOwnProperty("modelValue")||!this.$inProps.hasOwnProperty("modelValue")&&!this.$inProps.hasOwnProperty("defaultValue")},filled:function(){return this.$filled}}},f={name:"BaseInput",extends:s,props:{size:{type:String,default:null},fluid:{type:Boolean,default:null},variant:{type:String,default:null}},inject:{$parentInstance:{default:void 0},$pcFluid:{default:void 0}},computed:{$variant:function(){var n;return(n=this.variant)!==null&&n!==void 0?n:this.$primevue.config.inputStyle||this.$primevue.config.inputVariant},$fluid:function(){var n;return(n=this.fluid)!==null&&n!==void 0?n:!!this.$pcFluid},hasFluid:function(){return this.$fluid}}},m=function(n){var t=n.dt;return`
.p-inputtext {
    font-family: inherit;
    font-feature-settings: inherit;
    font-size: 1rem;
    color: `.concat(t("inputtext.color"),`;
    background: `).concat(t("inputtext.background"),`;
    padding-block: `).concat(t("inputtext.padding.y"),`;
    padding-inline: `).concat(t("inputtext.padding.x"),`;
    border: 1px solid `).concat(t("inputtext.border.color"),`;
    transition: background `).concat(t("inputtext.transition.duration"),", color ").concat(t("inputtext.transition.duration"),", border-color ").concat(t("inputtext.transition.duration"),", outline-color ").concat(t("inputtext.transition.duration"),", box-shadow ").concat(t("inputtext.transition.duration"),`;
    appearance: none;
    border-radius: `).concat(t("inputtext.border.radius"),`;
    outline-color: transparent;
    box-shadow: `).concat(t("inputtext.shadow"),`;
}

.p-inputtext:enabled:hover {
    border-color: `).concat(t("inputtext.hover.border.color"),`;
}

.p-inputtext:enabled:focus {
    border-color: `).concat(t("inputtext.focus.border.color"),`;
    box-shadow: `).concat(t("inputtext.focus.ring.shadow"),`;
    outline: `).concat(t("inputtext.focus.ring.width")," ").concat(t("inputtext.focus.ring.style")," ").concat(t("inputtext.focus.ring.color"),`;
    outline-offset: `).concat(t("inputtext.focus.ring.offset"),`;
}

.p-inputtext.p-invalid {
    border-color: `).concat(t("inputtext.invalid.border.color"),`;
}

.p-inputtext.p-variant-filled {
    background: `).concat(t("inputtext.filled.background"),`;
}

.p-inputtext.p-variant-filled:enabled:hover {
    background: `).concat(t("inputtext.filled.hover.background"),`;
}

.p-inputtext.p-variant-filled:enabled:focus {
    background: `).concat(t("inputtext.filled.focus.background"),`;
}

.p-inputtext:disabled {
    opacity: 1;
    background: `).concat(t("inputtext.disabled.background"),`;
    color: `).concat(t("inputtext.disabled.color"),`;
}

.p-inputtext::placeholder {
    color: `).concat(t("inputtext.placeholder.color"),`;
}

.p-inputtext.p-invalid::placeholder {
    color: `).concat(t("inputtext.invalid.placeholder.color"),`;
}

.p-inputtext-sm {
    font-size: `).concat(t("inputtext.sm.font.size"),`;
    padding-block: `).concat(t("inputtext.sm.padding.y"),`;
    padding-inline: `).concat(t("inputtext.sm.padding.x"),`;
}

.p-inputtext-lg {
    font-size: `).concat(t("inputtext.lg.font.size"),`;
    padding-block: `).concat(t("inputtext.lg.padding.y"),`;
    padding-inline: `).concat(t("inputtext.lg.padding.x"),`;
}

.p-inputtext-fluid {
    width: 100%;
}
`)},h={root:function(n){var t=n.instance,e=n.props;return["p-inputtext p-component",{"p-filled":t.$filled,"p-inputtext-sm p-inputfield-sm":e.size==="small","p-inputtext-lg p-inputfield-lg":e.size==="large","p-invalid":t.$invalid,"p-variant-filled":t.$variant==="filled","p-inputtext-fluid":t.$fluid}]}},v=d.extend({name:"inputtext",theme:m,classes:h}),$={name:"BaseInputText",extends:f,style:v,provide:function(){return{$pcInputText:this,$parentInstance:this}}},x={name:"InputText",extends:$,inheritAttrs:!1,methods:{onInput:function(n){this.writeValue(n.target.value,n)}},computed:{attrs:function(){return a(this.ptmi("root",{context:{filled:this.$filled,disabled:this.disabled}}),this.formField)}}},g=["value","disabled","aria-invalid"];function b(i,n,t,e,o,l){return c(),u("input",a({type:"text",class:i.cx("root"),value:i.d_value,disabled:i.disabled,"aria-invalid":i.$invalid||void 0,onInput:n[0]||(n[0]=function(){return l.onInput&&l.onInput.apply(l,arguments)})},l.attrs),null,16,g)}x.render=b;export{f as a,s as b,x as s};
