import{u as b,r as d,c as y,a as s,n as h,b as a,w as v,v as _,d as g,i as x,e as r,f as k,o as T,$ as c}from"./app-48782f7c.js";import{u as A,_ as z}from"./SectionsView-e3eb4601.js";const I="/build/assets/huberman-71b17ca2.jpg",L={class:"prose daisy-prose mx-auto flex flex-col max-w-full"},R={class:"flex justify-between items-end"},U=s("div",null,[s("h1",null,"A podcast summarizer powered by AI ⚡"),s("p",null,[r("As a proof of concept, the app will "),s("i",null,"only"),r(" process "),s("a",{class:"daisy-link daisy-link-primary",href:"https://hubermanlab.com/",target:"_blank"},"Huberman Lab"),r(" episodes.")])],-1),S=["src"],V=s("i",{class:"fa fa-shuffle"},null,-1),j=["disabled"],E={__name:"Page",setup(B){const m=A(),u=b();let o=d(""),p=d(""),l=d(!1),n=()=>{if(l.value)return;let i=o.value.match(/.*?\?v\=([a-zA-Z0-9_-]+?)[^a-zA-Z0-9_-]?$/);if(i!=null)p.value=i[1];else{u.info("Invalid URL 🥥");return}c.ajax({url:"/api/summarize",method:"POST",data:{videoId:p.value},beforeSend:()=>{l.value=!0},success:e=>{var t;((t=e.errors)==null?void 0:t.length)>0?e.errors.forEach(w=>{u.error(w)}):(m.title=e.data.title,m.sections=e.data.sections,setTimeout(()=>{c("html, body").animate({scrollTop:c("#video-title-heading").offset().top-100},500)},1e3))},error:()=>{u.error("Something went wrong 🥥")},complete:()=>{setTimeout(()=>{l.value=!1},800)}})},f=()=>{let i=["https://www.youtube.com/watch?v=LG53Vxum0as","https://www.youtube.com/watch?v=iw97uvIge7c","https://www.youtube.com/watch?v=gXvuJu1kt48","https://www.youtube.com/watch?v=DkS1pkKpILY","https://www.youtube.com/watch?v=ntfcfJ28eiU"];o.value=i[Math.floor(Math.random()*i.length)]};return(i,e)=>(T(),y("div",L,[s("div",R,[U,s("img",{class:h([{"animate-bounce":a(l)},"-translate-y-1/4 relative top-4 w-[70px] h-[70px] daisy-mask daisy-mask-hexagon mr-8"]),src:a(I),alt:"Face shot of Dr. Andrew Huberman"},null,10,S)]),v(s("input",{onKeyup:e[0]||(e[0]=g((...t)=>a(n)&&a(n)(...t),["enter"])),"onUpdate:modelValue":e[1]||(e[1]=t=>x(o)?o.value=t:o=t),type:"text",placeholder:"Enter a YouTube URL",class:"w-full daisy-input daisy-input-bordered",autofocus:""},null,544),[[_,a(o)]]),s("button",{onClick:e[2]||(e[2]=(...t)=>a(f)&&a(f)(...t)),class:"daisy-btn daisy-btn-ghost mt-4 daisy-btn-xs gap-2 self-end"},[V,r("Use a random link")]),s("button",{class:h(["daisy-btn daisy-btn-primary mt-6 w-full",{"daisy-loading":a(l)}]),disabled:a(l),onClick:e[3]||(e[3]=(...t)=>a(n)&&a(n)(...t))},"Summarize",10,j),k(z)]))}};export{E as default};
