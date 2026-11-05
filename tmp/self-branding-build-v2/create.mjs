import fs from 'node:fs/promises';
import { Presentation, PresentationFile } from '@oai/artifact-tool';
const W=1280,H=720;
const C={navy:'#14263D',blue:'#42617F',slate:'#566577',pale:'#EDF1F4',paper:'#FFFFFF',line:'#D8DEE5',soft:'#F5F7F9'};
const FONT='Aptos';
const deck=Presentation.create({slideSize:{width:W,height:H}});
function shape(slide,x,y,w,h,fill='none',geometry='rect',stroke='none',strokeWidth=0){return slide.shapes.add({geometry,position:{left:x,top:y,width:w,height:h},fill,line:{style:'solid',fill:stroke,width:strokeWidth}})}
function text(slide,value,x,y,w,h,size,color,bold=false){const s=shape(slide,x,y,w,h);s.text=value;s.text.style={fontFamily:FONT,fontSize:size,color,bold,verticalAlignment:'middle',wrap:true};return s;}
function rule(slide,x,y,w,color=C.line,h=2){shape(slide,x,y,w,h,color);}
function footer(slide,n){text(slide,'CHARITH KALHARA',64,672,350,18,12,C.slate,true);text(slide,`${n} / 3`,1130,672,86,18,12,C.slate,true);}
function addNotes(slide,value){slide.speakerNotes.text=value;slide.speakerNotes.setVisible(true);}
// Slide 1: clear identity and one-line brand.
{
 const s=deck.slides.add();s.background.fill=C.paper;
 shape(s,0,0,16,H,C.navy);
 text(s,'SELF INTRODUCTION',72,74,420,24,14,C.blue,true);
 text(s,'Charith Kalhara',68,168,930,90,64,C.navy,true);
 rule(s,72,286,84,C.blue,5);
 text(s,'Computer Science student',72,326,760,42,28,C.slate,true);
 text(s,'IIT Sri Lanka  ·  University of Westminster',72,376,800,32,20,C.slate,false);
 text(s,'I build practical software and deploy it\nas reliable cloud services.',72,476,930,92,32,C.navy,true);
 footer(s,1);
 addNotes(s,`Hello, I’m Charith Kalhara. I’m a Computer Science student at IIT Sri Lanka, affiliated with the University of Westminster. I’m building my professional identity around software development and cloud engineering. I enjoy creating practical applications and understanding what it takes to deploy them reliably. That is the simple idea behind my personal brand: I build useful software and work to make it dependable in the cloud.\n\nIn this short introduction, I’ll share the technical areas I’m developing, one project that brings them together, and the direction I want to take next.\n\nSource: https://lk.linkedin.com/in/charithkalhara`);
}
// Slide 2: essential skills and one proof point.
{
 const s=deck.slides.add();s.background.fill=C.paper;
 text(s,'SKILLS & PROJECT',64,54,500,24,14,C.blue,true);
 text(s,'Software from build to deployment',64,104,1080,62,42,C.navy,true);
 rule(s,64,198,1152);
 text(s,'APPLICATIONS',72,236,420,26,15,C.blue,true);
 text(s,'Laravel · PHP · Filament · MySQL',72,278,500,62,23,C.navy,true);
 text(s,'CLOUD & DELIVERY',664,236,420,26,15,C.blue,true);
 text(s,'Oracle Cloud · Docker\nGitHub Actions · Linux',664,278,490,68,23,C.navy,true);
 rule(s,64,386,1152);
 text(s,'PROJECT',72,422,220,24,15,C.blue,true);
 text(s,'Court case management system',72,460,600,36,25,C.navy,true);
 text(s,'Production web app  ·  Private cloud network\nSinhala PDF reports',72,512,800,58,20,C.slate,false);
 shape(s,980,425,2,150,C.line);
 text(s,'CURRENT LAB',1010,426,200,24,14,C.blue,true);
 text(s,'Kubernetes\nTerraform · Ansible',1010,468,210,68,19,C.navy,true);
 footer(s,2);
 addNotes(s,`My experience spans both application development and cloud delivery. On the application side, I’ve worked with Laravel, PHP, Filament and MySQL. For deployment, I’ve used Oracle Cloud Infrastructure, Docker, GitHub Actions and Linux. I’m also building my cloud-native skills through a lab using Kubernetes, Terraform and Ansible.\n\nA project that brings these areas together is a cloud-based court case management system. It is a production web application designed to organize debt-recovery case records. The application and database run on separate cloud virtual machines connected through a private network. The system also produces Sinhala-language PDF reports, and Docker with GitHub Actions supports deployment.\n\nThe project helped me see that good software depends on more than the application itself. Deployment, access, infrastructure and the needs of users all matter. It is the combination of these areas that I want to keep strengthening.\n\nSource: https://lk.linkedin.com/in/charithkalhara`);
}
// Slide 3: direction, concise and direct.
{
 const s=deck.slides.add();s.background.fill=C.navy;
 text(s,'CAREER DIRECTION',64,58,500,24,14,'#B8C7D6',true);
 text(s,'Cloud & DevOps\nengineering',64,132,780,140,54,C.paper,true);
 text(s,'My focus',68,344,180,24,15,'#B8C7D6',true);
 rule(s,68,388,1140,'#42546A');
 text(s,'Cloud infrastructure',68,421,350,38,24,C.paper,true);
 text(s,'Deployment automation',466,421,360,38,24,C.paper,true);
 text(s,'Reliable services',880,421,300,38,24,C.paper,true);
 text(s,'Security and responsibility guide how I build.',68,530,1000,42,24,C.paper,false);
 footer(s,3);
 addNotes(s,`Looking ahead, I want to grow toward cloud and DevOps engineering. My next steps are to deepen my understanding of cloud infrastructure, deployment automation and service reliability through hands-on projects. The cloud-native lab is one way I’m developing those skills.\n\nSecurity and responsibility are important to the kind of engineer I want to become. In my case management project, that means thinking about secure access and keeping the database on a private network. I’ve also contributed to writing about responsible use of predictive AI in healthcare, which reinforced that technology should be developed with care for the people it affects.\n\nMy direction is to build cloud systems that are useful, secure and reliable. Thank you.\n\nSource: https://lk.linkedin.com/in/charithkalhara`);
}
const build='C:/Nadu_System/nadu_system/tmp/self-branding-build-v2';
for(let i=0;i<deck.slides.items.length;i++){
 const png=await deck.export({slide:deck.slides.items[i],format:'png',scale:1});
 await fs.writeFile(`${build}/slide-${String(i+1).padStart(2,'0')}.png`,new Uint8Array(await png.arrayBuffer()));
}
const pptx=await PresentationFile.exportPptx(deck);await pptx.save(`${build}/self-branding-draft.pptx`);
console.log(`Created ${deck.slides.items.length} slides`);
