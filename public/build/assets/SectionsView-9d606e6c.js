import{g as b,r as v,h as f,w as u,j as m,b as e,c,a as s,t as i,e as r,F as g,k as y,$ as S,o as l,n as x,i as w}from"./app-f34fc159.js";const j=b("apiResponse",{state:()=>({title:"",sections:[]})}),k={class:"w-full mb-[80px]"},O={class:"mt-12",id:"video-title-heading"},R={clas:"mt-4"},T=s("span",{class:"text-primary"},"#",-1),V={class:"text-primary"},B={class:"daisy-tabs-boxed mt-6"},C=["onClick"],z={class:"p-2"},D={class:"daisy-badge daisy-badge-primary ml-2"},E={__name:"SectionsView",setup(F){const t=j();let a=v(0),n=f(()=>t.sections[a.value]);return(N,$)=>{var d,_,p;return u((l(),c("div",k,[s("h2",O,i(e(t).title),1),s("h3",R,[T,r(" This podcast contains "),s("span",V,i(e(t).sections.length)+" sections",1),r(".")]),s("div",B,[(l(!0),c(g,null,y(e(t).sections,(h,o)=>(l(),c("a",{class:x(["daisy-tab daisy-tab-lg lifted transition-[background-color,color,border-radius] ease-linear",{"daisy-tab-active":o==e(a)}]),key:o,onClick:A=>w(a)?a.value=o:a=o},i(h.section_number+1),11,C))),128))]),s("div",z,[s("h3",null,[r(i((d=e(n))==null?void 0:d.title),1),u(s("div",D,"Often a general introduction",512),[[m,((_=e(n))==null?void 0:_.section_number)==0]])]),s("p",null,i((p=e(n))==null?void 0:p.text),1)])],512)),[[m,!e(S).isEmptyObject(e(t).sections)]])}}},M=Object.freeze(Object.defineProperty({__proto__:null,default:E},Symbol.toStringTag,{value:"Module"}));export{M as S,E as _,j as u};
