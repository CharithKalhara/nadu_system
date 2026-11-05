import fs from 'node:fs/promises';
import { Presentation, PresentationFile } from '@oai/artifact-tool';
const W=1280,H=720;
const C={navy:'#14263D',blue:'#42617F',slate:'#566577',paper:'#FFFFFF',line:'#D8DEE5',soft:'#F3F5F7',pale:'#B8C7D6'};
const FONT='Aptos';
const deck=Presentation.create({slideSize:{width:W,height:H}});
function shape(slide,x,y,w,h,fill='none',stroke='none',strokeWidth=0){return slide.shapes.add({geometry:'rect',position:{left:x,top:y,width:w,height:h},fill,line:{style:'solid',fill:stroke,width:strokeWidth}})}
function text(slide,value,x,y,w,h,size,color,bold=false){const s=shape(slide,x,y,w,h);s.text=value;s.text.style={fontFamily:FONT,fontSize:size,color,bold,verticalAlignment:'middle',wrap:true};return s;}
function rule(slide,x,y,w,color=C.line,h=2){shape(slide,x,y,w,h,color)}
function footer(slide,n,dark=false){const col=dark?C.pale:C.slate;text(slide,'CHARITH KALHARA',64,672,350,18,12,col,true);text(slide,`${n} / 4`,1130,672,86,18,12,col,true)}
function notes(slide,body){slide.speakerNotes.text=body;slide.speakerNotes.setVisible(true)}
// 1 Introduction
{
const s=deck.slides.add();s.background.fill=C.paper;shape(s,0,0,16,H,C.navy);
text(s,'INTRODUCTION',72,72,400,24,14,C.blue,true);
text(s,'Charith Kalhara',68,166,1000,88,64,C.navy,true);
rule(s,72,282,84,C.blue,5);
text(s,'Computer Science student',72,320,780,42,28,C.slate,true);
text(s,'IIT Sri Lanka  ·  University of Westminster',72,369,810,32,20,C.slate);
text(s,'I build practical software and deploy it\nas reliable cloud services.',72,470,960,92,32,C.navy,true);
footer(s,1);
notes(s,`Hello, I’m Charith Kalhara. I’m a Computer Science student at IIT Sri Lanka, affiliated with the University of Westminster. I’m building my professional identity around software development and cloud engineering. I enjoy creating practical applications and understanding what it takes to deploy them reliably. The idea behind my personal brand is simple: I build useful software and work to make it dependable in the cloud.\n\nIn this presentation, I’ll share the technical areas I’m developing, one project that brings them together, and the direction I want to take next.\n\nSource: https://lk.linkedin.com/in/charithkalhara`)
}
// 2 Skills and strengths
{
const s=deck.slides.add();s.background.fill=C.paper;
text(s,'SKILLS & STRENGTHS',64,54,500,24,14,C.blue,true);
text(s,'Software and cloud delivery',64,106,1040,62,42,C.navy,true);
rule(s,64,196,1152);
text(s,'APPLICATION DEVELOPMENT',72,232,480,26,15,C.blue,true);
text(s,'Laravel · PHP · Filament · MySQL',72,278,495,66,24,C.navy,true);
text(s,'CLOUD & AUTOMATION',664,232,480,26,15,C.blue,true);
text(s,'Oracle Cloud · Docker\nGitHub Actions · Linux',664,278,490,68,24,C.navy,true);
rule(s,64,399,1152);
text(s,'CURRENT LEARNING',72,433,300,24,15,C.blue,true);
text(s,'Kubernetes · Terraform · Ansible',72,472,850,38,24,C.navy,true);
text(s,'Strength: connect application work with deployment and operations.',72,555,1070,42,22,C.slate,false);
footer(s,2);
notes(s,`My technical experience covers application development and cloud delivery. I’ve worked with Laravel, PHP, Filament and MySQL to build web applications. For deployment, I’ve used Oracle Cloud Infrastructure, Docker, GitHub Actions and Linux. I’m also expanding my skills through a cloud-native lab that uses Kubernetes, Terraform and Ansible.\n\nA strength I bring to my technical work is connecting the application to the systems that support it. I’m interested in how software is built, how it is deployed, and how it can continue to run reliably. I’m still developing in each area, and I’m using practical projects to build stronger engineering fundamentals.\n\nSource: https://lk.linkedin.com/in/charithkalhara`)
}
// 3 Project evidence
{
const s=deck.slides.add();s.background.fill=C.paper;
text(s,'PROJECT EXPERIENCE',64,54,500,24,14,C.blue,true);
text(s,'Court case management system',64,108,1100,62,42,C.navy,true);
text(s,'Production web application for debt-recovery case records',66,184,1090,34,21,C.slate,false);
rule(s,64,260,1152);
const cols=[72,470,856];
const labels=['APPLICATION','CLOUD','DELIVERY'];
const bodies=['Laravel · PHP\nFilament','Oracle Cloud\nPrivate network','Docker\nGitHub Actions'];
for(let i=0;i<3;i++){
 text(s,labels[i],cols[i],300,290,24,15,C.blue,true);
 text(s,bodies[i],cols[i],342,300,86,27,C.navy,true);
 if(i<2) shape(s,cols[i]+330,296,1,140,C.line);
}
rule(s,64,488,1152);
text(s,'RESULT',72,522,160,24,15,C.blue,true);
text(s,'Centralized case records and Sinhala PDF reports',250,512,870,45,24,C.navy,true);
footer(s,3);
notes(s,`One project that brings these skills together is a cloud-based court case management system for debt recovery. It is a production web application designed to organize case records. I developed the application with Laravel, PHP and Filament. It runs on Oracle Cloud Infrastructure, with the application and database on separate virtual machines connected through a private network. Docker and GitHub Actions support deployment. The system also generates Sinhala-language PDF reports.\n\nWorking on this project helped me think beyond whether a feature works locally. Access, infrastructure, deployment and the needs of users all affect whether a system is useful. It gave me experience connecting application development with cloud operations, and it strengthened my interest in building reliable services.\n\nSource: https://lk.linkedin.com/in/charithkalhara`)
}
// 4 Career aspiration and values
{
const s=deck.slides.add();s.background.fill=C.navy;
text(s,'CAREER DIRECTION',64,58,500,24,14,C.pale,true);
text(s,'Cloud & DevOps\nengineering',64,128,780,140,54,C.paper,true);
text(s,'FOCUS',68,330,180,24,14,C.pale,true);
rule(s,68,370,1140,'#42546A');
text(s,'Cloud infrastructure',68,402,350,38,23,C.paper,true);
text(s,'Deployment automation',466,402,370,38,23,C.paper,true);
text(s,'Reliable services',880,402,310,38,23,C.paper,true);
text(s,'VALUES',68,500,180,24,14,C.pale,true);
text(s,'Security  ·  Responsibility  ·  Continuous learning',68,538,1070,40,24,C.paper,true);
footer(s,4,true);
notes(s,`Looking ahead, I want to grow toward cloud and DevOps engineering. I plan to deepen my understanding of cloud infrastructure, deployment automation and service reliability through hands-on work. My cloud-native lab is one way I’m building experience in these areas.\n\nThe values I want to bring to this work are security, responsibility and continuous learning. In the case management project, security meant considering access and keeping the database on a private network. I’ve also contributed to writing about the ethical and governance questions around predictive AI in healthcare. That experience reinforced that technical work should be matched by care for the people affected by it.\n\nMy goal is to build cloud systems that are useful, secure and dependable. Thank you.\n\nSource: https://lk.linkedin.com/in/charithkalhara`)
}
const build='C:/Nadu_System/nadu_system/tmp/self-branding-build-v3';
for(let i=0;i<deck.slides.items.length;i++) {const png=await deck.export({slide:deck.slides.items[i],format:'png',scale:1});await fs.writeFile(`${build}/slide-${i+1}.png`,new Uint8Array(await png.arrayBuffer()))}
await (await PresentationFile.exportPptx(deck)).save(`${build}/self-branding-draft.pptx`);
console.log(`Created ${deck.slides.items.length} slides`)
