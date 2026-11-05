import fs from 'node:fs/promises';
import { Presentation, PresentationFile } from '@oai/artifact-tool';

const W=1280, H=720;
const C={ink:'#101B2D',ink2:'#18283D',paper:'#F4F1E8',white:'#FFFFFF',muted:'#AAB6C4',darkMuted:'#586574',lime:'#B8F35A',line:'#D8D4C9'};
const FONT='Aptos';
const deck=Presentation.create({slideSize:{width:W,height:H}});
function box(slide,x,y,w,h,fill='none',lineFill='none',lineWidth=0,geometry='rect'){
 return slide.shapes.add({geometry,position:{left:x,top:y,width:w,height:h},fill,line:{style:'solid',fill:lineFill,width:lineWidth}});
}
function txt(slide,text,x,y,w,h,size,color,bold=false,font=FONT){
 const s=box(slide,x,y,w,h); s.text=text; s.text.style={fontFamily:font,fontSize:size,color,bold,verticalAlignment:'middle',wrap:true}; return s;
}
function footer(slide,n,dark=false){
 const fg=dark?C.muted:C.darkMuted; txt(slide,'CHARITH KALHARA  /  SELF BRAND',64,670,430,20,12,fg,true); txt(slide,`${String(n).padStart(2,'0')} / 04`,1110,670,106,20,12,fg,true);
}
function notes(slide,text){slide.speakerNotes.text=text;slide.speakerNotes.setVisible(true);}

// 1 — Opening
{
 const s=deck.slides.add(); s.background.fill=C.ink;
 box(s,0,0,14,H,C.lime);
 txt(s,'COMPUTER SCIENCE  /  SRI LANKA',72,60,670,28,15,C.lime,true);
 txt(s,'Charith\nKalhara',68,151,790,192,76,C.paper,true);
 txt(s,'I build practical software, then make it\nwork reliably in the cloud.',74,382,730,88,29,C.white,false);
 box(s,900,152,1,350,'#425064');
 txt(s,'MY LANE',946,174,230,24,14,C.muted,true);
 txt(s,'Software\n+ Cloud\n+ DevOps',944,220,270,180,34,C.white,true);
 txt(s,'Computer Science student\nIIT Sri Lanka | University of Westminster',74,558,730,50,18,C.muted,false);
 txt(s,'01',1114,558,100,94,62,C.lime,true);
 footer(s,1,true);
 notes(s,`Hello, I’m Charith Kalhara, a Computer Science student at IIT Sri Lanka, affiliated with the University of Westminster. The professional identity I’m building sits at the intersection of software development and cloud operations. I enjoy taking a real process, turning it into a useful application, and then thinking through how to deploy and maintain it reliably. My short version is simple: I build practical software, then make it work reliably in the cloud. That combination is the thread connecting my projects and the direction I want to grow in.\n\nSource: https://lk.linkedin.com/in/charithkalhara`);
}
// 2 — Skills as a practical stack
{
 const s=deck.slides.add(); s.background.fill=C.paper;
 txt(s,'02  /  SKILLS & STRENGTHS',64,54,700,25,14,C.ink,true);
 txt(s,'I work across the\nwhole delivery path',64,98,735,112,46,C.ink,true);
 txt(s,'From application logic to production operations',66,223,730,30,20,C.darkMuted,false);
 // Native diagram: three connected stages with concise technology evidence.
 const y=326; box(s,88,y+15,1050,2,C.line);
 const xs=[100,490,880]; const titles=['BUILD','DEPLOY','OPERATE'];
 const bodies=[
  'Laravel · PHP · Filament\nMySQL · Node.js',
  'Docker · GitHub Actions\nAWS · Oracle Cloud',
  'Linux · Kubernetes\nTerraform · Ansible'
 ];
 for(let i=0;i<3;i++){
   box(s,xs[i],y,34,34,C.lime,'none',0,'ellipse');
   txt(s,titles[i],xs[i],y+68,300,24,15,C.ink,true);
   txt(s,bodies[i],xs[i],y+108,320,60,22,C.ink,false);
 }
 box(s,64,548,1148,1,C.line);
 txt(s,'Strength I bring',66,574,220,26,15,C.darkMuted,true);
 txt(s,'Connect product features with the infrastructure that supports them.',300,564,850,44,22,C.ink,true);
 footer(s,2,false);
 notes(s,`My technical strengths cover more than one layer of a product. I build web applications with PHP, Laravel and Filament, work with databases, and have also deployed Node.js workloads. I use Docker and GitHub Actions to package and automate releases, and I’ve worked with AWS and Oracle Cloud Infrastructure. In my cloud-native lab, I’ve been developing experience with Kubernetes, Terraform and Ansible, alongside Linux operations.\n\nThe strength I’m trying to make distinctive is the ability to connect these layers: understanding how a feature is built, how it reaches users, and what it needs to run securely and reliably. I’m still growing these skills through hands-on projects, and I want to keep deepening the engineering fundamentals behind them.\n\nSource: https://lk.linkedin.com/in/charithkalhara`);
}
// 3 — Evidence
{
 const s=deck.slides.add(); s.background.fill=C.ink2;
 txt(s,'03  /  PROJECT EVIDENCE',64,54,700,25,14,C.lime,true);
 txt(s,'Built for real\noperational needs',64,105,660,104,46,C.paper,true);
 txt(s,'Cloud-based court case management',66,236,720,32,23,C.white,true);
 txt(s,'A production web app for organizing debt-recovery cases',66,278,730,30,18,C.muted,false);
 // Flat architecture flow
 const cy=396;
 txt(s,'CASE\nWORKFLOW',90,cy,190,58,18,C.paper,true);
 txt(s,'APPLICATION',405,cy,180,58,18,C.paper,true);
 txt(s,'PRIVATE CLOUD',726,cy,200,58,18,C.paper,true);
 txt(s,'AUTOMATED\nRELEASES',1030,cy,180,58,18,C.paper,true);
 box(s,275,cy+28,95,2,C.lime); box(s,586,cy+28,95,2,C.lime); box(s,926,cy+28,78,2,C.lime);
 txt(s,'Records → documents',90,cy+82,235,28,15,C.muted,false);
 txt(s,'Laravel · PHP · Filament',405,cy+82,270,28,15,C.muted,false);
 txt(s,'OCI · Linux · MySQL',726,cy+82,255,28,15,C.muted,false);
 txt(s,'Docker · GitHub Actions',1010,cy+82,240,28,15,C.muted,false);
 box(s,64,566,1148,1,'#435165');
 txt(s,'Design priorities',66,591,205,22,14,C.muted,true);
 txt(s,'Useful workflows   /   secure access   /   Sinhala PDF reports',300,582,850,42,20,C.white,true);
 footer(s,3,true);
 notes(s,`One project that brings these skills together is a cloud-based court case management system for debt recovery. It was designed to centralize case records and support the work around those cases. I developed the web application using Laravel, PHP and Filament, and worked on its cloud deployment using Oracle Cloud Infrastructure. The architecture keeps the application and database on separate virtual machines connected through a private network. Docker and GitHub Actions support repeatable deployment, and the system generates Sinhala-language PDF reports.\n\nThis project taught me to think beyond whether a feature works on my computer. Access control, deployment, database connectivity and document output all affect whether the system is useful in practice. A separate cloud-native lab is extending that learning through Kubernetes, infrastructure automation and monitoring.\n\nSource: https://lk.linkedin.com/in/charithkalhara`);
}
// 4 — Values and direction
{
 const s=deck.slides.add(); s.background.fill=C.paper;
 txt(s,'04  /  VALUES & DIRECTION',64,54,700,25,14,C.ink,true);
 txt(s,'Build systems people\ncan depend on',64,104,740,112,48,C.ink,true);
 txt(s,'The engineer I am working toward becoming',66,232,730,30,20,C.darkMuted,false);
 box(s,64,316,3,247,C.ink);
 txt(s,'NEXT',96,322,120,24,14,C.darkMuted,true);
 txt(s,'Cloud & DevOps engineering',96,359,470,38,28,C.ink,true);
 txt(s,'Deepen cloud-native design, automation and reliability through\nprojects that solve real operational problems.',96,408,650,64,19,C.darkMuted,false);
 txt(s,'GUIDING VALUES',792,322,250,24,14,C.darkMuted,true);
 txt(s,'Security\nResponsibility\nContinuous learning',792,360,400,118,27,C.ink,true);
 box(s,64,584,1148,1,C.line);
 txt(s,'My promise',66,607,190,25,15,C.darkMuted,true);
 txt(s,'Make technology useful, secure and dependable.',300,598,835,42,22,C.ink,true);
 footer(s,4,false);
 notes(s,`My next step is to grow toward cloud and DevOps engineering. I want to deepen my understanding of cloud-native architecture, infrastructure automation, deployment pipelines and service reliability, while continuing to build applications that address real operational needs.\n\nThe values I want people to associate with my work are responsibility, security and continuous learning. They show up in the way I think about protecting access, separating sensitive systems, and considering the people who depend on the software. I also co-authored a piece examining the ethical and governance questions around predictive AI in healthcare. That work reinforced for me that technical capability needs to be matched by careful judgment.\n\nMy professional brand is still developing, but the direction is clear: make technology useful, secure and dependable. Thank you.\n\nSource: https://lk.linkedin.com/in/charithkalhara`);
}

const build='C:/Nadu_System/nadu_system/tmp/self-branding-build';
const output='C:/Nadu_System/nadu_system/output/presentations';
await fs.mkdir(build,{recursive:true}); await fs.mkdir(output,{recursive:true});
for(let i=0;i<deck.slides.items.length;i++){
 const png=await deck.export({slide:deck.slides.items[i],format:'png',scale:1});
 await fs.writeFile(`${build}/slide-${String(i+1).padStart(2,'0')}.png`,new Uint8Array(await png.arrayBuffer()));
}
const pptx=await PresentationFile.exportPptx(deck); await pptx.save(`${build}/self-branding-draft.pptx`);
console.log('Created draft with',deck.slides.items.length,'slides');
