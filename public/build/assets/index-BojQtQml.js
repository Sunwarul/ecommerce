import{s as h}from"./index-Dz2Ab6O2.js";import{s as m}from"./index-BogZg6Yq.js";import{B as u,c as r,d as t,o as i,D as s,g as l,m as c,I as p,t as g}from"./app-CFBpZPLY.js";var v=function(o){var e=o.dt;return`
.p-chip {
    display: inline-flex;
    align-items: center;
    background: `.concat(e("chip.background"),`;
    color: `).concat(e("chip.color"),`;
    border-radius: `).concat(e("chip.border.radius"),`;
    padding-block: `).concat(e("chip.padding.y"),`;
    padding-inline: `).concat(e("chip.padding.x"),`;
    gap: `).concat(e("chip.gap"),`;
}

.p-chip-icon {
    color: `).concat(e("chip.icon.color"),`;
    font-size: `).concat(e("chip.icon.font.size"),`;
    width: `).concat(e("chip.icon.size"),`;
    height: `).concat(e("chip.icon.size"),`;
}

.p-chip-image {
    border-radius: 50%;
    width: `).concat(e("chip.image.width"),`;
    height: `).concat(e("chip.image.height"),`;
    margin-inline-start: calc(-1 * `).concat(e("chip.padding.y"),`);
}

.p-chip:has(.p-chip-remove-icon) {
    padding-inline-end: `).concat(e("chip.padding.y"),`;
}

.p-chip:has(.p-chip-image) {
    padding-block-start: calc(`).concat(e("chip.padding.y"),` / 2);
    padding-block-end: calc(`).concat(e("chip.padding.y"),` / 2);
}

.p-chip-remove-icon {
    cursor: pointer;
    font-size: `).concat(e("chip.remove.icon.size"),`;
    width: `).concat(e("chip.remove.icon.size"),`;
    height: `).concat(e("chip.remove.icon.size"),`;
    color: `).concat(e("chip.remove.icon.color"),`;
    border-radius: 50%;
    transition: outline-color `).concat(e("chip.transition.duration"),", box-shadow ").concat(e("chip.transition.duration"),`;
    outline-color: transparent;
}

.p-chip-remove-icon:focus-visible {
    box-shadow: `).concat(e("chip.remove.icon.focus.ring.shadow"),`;
    outline: `).concat(e("chip.remove.icon.focus.ring.width")," ").concat(e("chip.remove.icon.focus.ring.style")," ").concat(e("chip.remove.icon.focus.ring.color"),`;
    outline-offset: `).concat(e("chip.remove.icon.focus.ring.offset"),`;
}
`)},f={root:"p-chip p-component",image:"p-chip-image",icon:"p-chip-icon",label:"p-chip-label",removeIcon:"p-chip-remove-icon"},y=u.extend({name:"chip",theme:v,classes:f}),b={name:"BaseChip",extends:m,props:{label:{type:String,default:null},icon:{type:String,default:null},image:{type:String,default:null},removable:{type:Boolean,default:!1},removeIcon:{type:String,default:void 0}},style:y,provide:function(){return{$pcChip:this,$parentInstance:this}}},k={name:"Chip",extends:b,inheritAttrs:!1,emits:["remove"],data:function(){return{visible:!0}},methods:{onKeydown:function(o){(o.key==="Enter"||o.key==="Backspace")&&this.close(o)},close:function(o){this.visible=!1,this.$emit("remove",o)}},components:{TimesCircleIcon:h}},w=["aria-label"],C=["src"];function I(n,o,e,$,d,a){return d.visible?(i(),r("div",c({key:0,class:n.cx("root"),"aria-label":n.label},n.ptmi("root")),[s(n.$slots,"default",{},function(){return[n.image?(i(),r("img",c({key:0,src:n.image},n.ptm("image"),{class:n.cx("image")}),null,16,C)):n.$slots.icon?(i(),l(p(n.$slots.icon),c({key:1,class:n.cx("icon")},n.ptm("icon")),null,16,["class"])):n.icon?(i(),r("span",c({key:2,class:[n.cx("icon"),n.icon]},n.ptm("icon")),null,16)):t("",!0),n.label?(i(),r("div",c({key:3,class:n.cx("label")},n.ptm("label")),g(n.label),17)):t("",!0)]}),n.removable?s(n.$slots,"removeicon",{key:0,removeCallback:a.close,keydownCallback:a.onKeydown},function(){return[(i(),l(p(n.removeIcon?"span":"TimesCircleIcon"),c({class:[n.cx("removeIcon"),n.removeIcon],onClick:a.close,onKeydown:a.onKeydown},n.ptm("removeIcon")),null,16,["class","onClick","onKeydown"]))]}):t("",!0)],16,w)):t("",!0)}k.render=I;export{k as s};
