import fs from 'node:fs/promises';
import { Presentation, PresentationFile } from '@oai/artifact-tool';
const W=1280,H=720;
const C={navy:'#14263D',blue:'#42617F',slate:'#586879',paper:'#FFFFFF',soft:'#F3F5F7',line:'#D7DEE6',gold:'#C8A56A',pale:'#B8C7D6',dark:'#1E344D'};
const FONT='Aptos';
const deck=Presentation.create({slideSize:{width:W,height:H}});
function rect(s,x,y,w,h,fill='none',stroke='none',sw=0){return s.shapes.add({geometry:'rect',position:{left:x,top:y,width:w,height:h},fill,line:{style:'solid',fill:stroke,width:sw}})}
function text(s,v,x,y,w,h,size,color,bold=false){const q=rect(s,x,y,w,h);q.text=v;q.text.style={fontFamily:FONT,fontSize:size,color,bold,verticalAlignment:'middle',wrap:true};return q}
function line(s,x,y,w,color=C.line,h=2){rect(s,x,y,w,h,color)}
function footer(s,n,dark=false){const c=dark?C.pale:C.slate;text(s,'CHARITH KALHARA',64,674,360,16,12,c,true);text(s,`${String(n).padStart(2,'0')}  /  04`,1120,674,100,16,12,c,true)}
function notes(s,v){s.speakerNotes.text=v;s.speakerNotes.setVisible(true)}
async function imgBytes(p){const b=await fs.readFile(p);return b.buffer.slice(b.byteOffset,b.byteOffset+b.byteLength)}
// Slide 1: photographic editorial cover
{
const s=deck.slides.add();s.background.fill=C.navy;
const hero=await imgBytes('C:/Nadu_System/nadu_system/tmp/self-branding-build-v4/cloud-infrastructure-hero.png');
s.images.add({blob:hero,contentType:'image/png',alt:'Illustration of connected cloud servers and infrastructure nodes',fit:'cover',position:{left:0,top:0,width:W,height:H}});
// Subtle darkened text field on the image's natural negative-space side.
rect(s,0,0,622,H,{color:C.navy,transparency:10});
text(s,'COMPUTER SCIENCE  /  SRI LANKA',72,74,550,24,14,'#E1C99F',true);
text(s,'Charith\nKalhara',68,172,600,160,64,C.paper,true);
line(s,72,370,74,C.gold,5);
text(s,'Software  ·  Cloud  ·  DevOps',72,406,545,36,22,C.paper,true);
text(s,'I build practical software and deploy it\nas reliable cloud services.',72,470,575,78,25,'#E5EAF0',false);
footer(s,1,true);
notes(s,`Hello, I’m Charith Kalhara. I’m a Computer Science student at IIT Sri Lanka, affiliated with the University of Westminster. I’m building my professional identity around software development and cloud engineering. I enjoy creating practical applications and understanding what it takes to deploy them reliably. The idea behind my personal brand is simple: I build useful software and work to make it dependable in the cloud.\n\nIn this presentation, I’ll share the technical areas I’m developing, one project that brings them together, and the direction I want to take next.\n\nSource: https://lk.linkedin.com/in/charithkalhara`)
}
// Slide 2: skills as an editable three-stage diagram.
{
const s=deck.slides.add();s.background.fill=C.paper;
text(s,'SKILLS & STRENGTHS',64,52,470,22,14,C.blue,true);
text(s,'From application to operations',64,98,1080,56,40,C.navy,true);
text(s,'I connect what I build with how it runs.',66,166,880,32,21,C.slate,false);
line(s,90,292,1064,C.line,2);
const stages=[
 {x:100,n:'01',head:'BUILD',sub:'Laravel · PHP · Filament\nMySQL'},
 {x:490,n:'02',head:'DEPLOY',sub:'Docker · GitHub Actions\nOracle Cloud'},
 {x:880,n:'03',head:'OPERATE',sub:'Linux · Kubernetes lab\nTerraform · Ansible'}
];
for(const a of stages){
 rect(s,a.x,273,40,40,C.navy,'none',0);text(s,a.n,a.x,279,40,24,13,C.paper,true);
 text(s,a.head,a.x,344,300,28,16,C.blue,true);
 text(s,a.sub,a.x,389,330,65,23,C.navy,true);
}
line(s,64,526,1152,C.line,2);
text(s,'CORE STRENGTH',72,554,210,20,13,C.blue,true);
text(s,'Connect application work with deployment and operations.',280,543,850,42,22,C.navy,true);
footer(s,2);
notes(s,`My technical experience covers application development and cloud delivery. I’ve worked with Laravel, PHP, Filament and MySQL to build web applications. For deployment, I’ve used Oracle Cloud Infrastructure, Docker, GitHub Actions and Linux. I’m also expanding my skills through a cloud-native lab that uses Kubernetes, Terraform and Ansible.\n\nA strength I bring to my technical work is connecting the application to the systems that support it. I’m interested in how software is built, how it is deployed, and how it can continue to run reliably. I’m still developing in each area, and I’m using practical projects to build stronger engineering fundamentals.\n\nSource: https://lk.linkedin.com/in/charithkalhara`)
}
// Slide 3: project architecture and outcome.
{
const s=deck.slides.add();s.background.fill=C.soft;
text(s,'PROJECT EXPERIENCE',64,52,470,22,14,C.blue,true);
text(s,'Court case management system',64,100,1080,60,40,C.navy,true);
text(s,'Production web application for debt-recovery case records',66,169,1040,30,20,C.slate,false);
text(s,'HOW IT WORKS',72,250,250,22,13,C.blue,true);
const boxes=[
 {x:72,w:260,title:'CASE RECORDS',sub:'Court workflow'},
 {x:388,w:250,title:'APPLICATION',sub:'Laravel · Filament'},
 {x:698,w:250,title:'PRIVATE CLOUD',sub:'App + database'},
 {x:1008,w:200,title:'REPORTS',sub:'Sinhala PDF'}
];
for(const b of boxes){rect(s,b.x,302,b.w,114,C.paper,C.line,1);text(s,b.title,b.x+16,322,b.w-32,22,14,C.blue,true);text(s,b.sub,b.x+16,356,b.w-32,34,18,C.navy,true)}
for(const x of [346,656,966]){text(s,'→',x,338,32,36,25,C.blue,true)}
line(s,64,477,1152,C.line,2);
text(s,'DEPLOYMENT',72,507,200,20,13,C.blue,true);
text(s,'Docker  ·  GitHub Actions  ·  Oracle Cloud',262,496,890,42,22,C.navy,true);
footer(s,3);
notes(s,`One project that brings these skills together is a cloud-based court case management system for debt recovery. It is a production web application designed to organize case records. I developed the application with Laravel, PHP and Filament. It runs on Oracle Cloud Infrastructure, with the application and database on separate virtual machines connected through a private network. Docker and GitHub Actions support deployment. The system also generates Sinhala-language PDF reports.\n\nWorking on this project helped me think beyond whether a feature works locally. Access, infrastructure, deployment and the needs of users all affect whether a system is useful. It gave me experience connecting application development with cloud operations, and it strengthened my interest in building reliable services.\n\nSource: https://lk.linkedin.com/in/charithkalhara`)
}
// Slide 4: career direction and values with an editable roadmap.
{
const s=deck.slides.add();s.background.fill=C.navy;
text(s,'CAREER DIRECTION',64,54,420,22,14,C.pale,true);
text(s,'Cloud & DevOps engineering',64,106,1100,64,44,C.paper,true);
text(s,'The engineer I am working toward becoming',66,176,900,30,20,C.pale,false);
line(s,86,326,1080,'#53657A',2);
const focuses=[
 {x:92,n:'01',t:'Cloud\ninfrastructure'},
 {x:478,n:'02',t:'Deployment\nautomation'},
 {x:864,n:'03',t:'Reliable\nservices'}
];
for(const f of focuses){rect(s,f.x,305,42,42,C.gold);text(s,f.n,f.x,313,42,24,13,C.navy,true);text(s,f.t,f.x,376,280,72,24,C.paper,true)}
line(s,64,516,1152,'#53657A',2);
text(s,'VALUES',72,546,140,22,13,C.pale,true);
text(s,'Security  ·  Responsibility  ·  Continuous learning',220,536,930,42,22,C.paper,true);
footer(s,4,true);
notes(s,`Looking ahead, I want to grow toward cloud and DevOps engineering. I plan to deepen my understanding of cloud infrastructure, deployment automation and service reliability through hands-on work. My cloud-native lab is one way I’m building experience in these areas.\n\nThe values I want to bring to this work are security, responsibility and continuous learning. In the case management project, security meant considering access and keeping the database on a private network. I’ve also contributed to writing about the ethical and governance questions around predictive AI in healthcare. That experience reinforced that technical work should be matched by care for the people affected by it.\n\nMy goal is to build cloud systems that are useful, secure and dependable. Thank you.\n\nSource: https://lk.linkedin.com/in/charithkalhara`)
}
const out='C:/Nadu_System/nadu_system/tmp/self-branding-build-v4';
for(let i=0;i<deck.slides.items.length;i++){const png=await deck.export({slide:deck.slides.items[i],format:'png',scale:1});await fs.writeFile(`${out}/slide-${i+1}.png`,new Uint8Array(await png.arrayBuffer()))}
await(await PresentationFile.exportPptx(deck)).save(`${out}/self-branding-draft.pptx`);
console.log(`Created ${deck.slides.items.length} slides`)
