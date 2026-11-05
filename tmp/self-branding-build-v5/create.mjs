import fs from 'node:fs/promises';
import {Presentation,PresentationFile} from '@oai/artifact-tool';
const W=1280,H=720, FONT='Aptos';
const C={navy:'#14263D',blue:'#42617F',slate:'#586879',paper:'#FFFFFF',soft:'#F3F5F7',line:'#D7DEE6',gold:'#C8A56A',pale:'#B8C7D6'};
const deck=Presentation.create({slideSize:{width:W,height:H}});
function rect(s,x,y,w,h,fill='none',stroke='none',sw=0){return s.shapes.add({geometry:'rect',position:{left:x,top:y,width:w,height:h},fill,line:{style:'solid',fill:stroke,width:sw}})}
function txt(s,v,x,y,w,h,size,color,bold=false){const q=rect(s,x,y,w,h);q.text=v;q.text.style={fontFamily:FONT,fontSize:size,color,bold,verticalAlignment:'middle',wrap:true};return q}
function line(s,x,y,w,color=C.line,h=2){rect(s,x,y,w,h,color)}
function footer(s,n,dark=false){const c=dark?C.pale:C.slate;txt(s,'CHARITH KALHARA',64,674,360,16,12,c,true);txt(s,`${String(n).padStart(2,'0')}  /  04`,1120,674,100,16,12,c,true)}
function notes(s,v){s.speakerNotes.text=v;s.speakerNotes.setVisible(true)}
async function imageBytes(path){const b=await fs.readFile(path);return b.buffer.slice(b.byteOffset,b.byteOffset+b.byteLength)}
// 1: Brand identity
{
 const s=deck.slides.add();s.background.fill=C.navy;
 s.images.add({blob:await imageBytes('C:/Nadu_System/nadu_system/tmp/self-branding-build-v5/cloud-infrastructure-hero.png'),contentType:'image/png',alt:'Cloud infrastructure illustration with connected servers',fit:'cover',position:{left:0,top:0,width:W,height:H}});
 rect(s,0,0,622,H,{color:C.navy,transparency:10});
 txt(s,'MY PROFESSIONAL BRAND',72,74,540,24,14,'#E1C99F',true);
 txt(s,'Charith\nKalhara',68,172,600,160,64,C.paper,true);
 line(s,72,370,74,C.gold,5);
 txt(s,'Computer Science student',72,402,550,34,22,C.paper,true);
 txt(s,'IIT Sri Lanka  ·  University of Westminster',72,446,545,28,17,'#E5EAF0',false);
 txt(s,'I connect software development\nwith reliable cloud services.',72,510,570,66,24,C.paper,true);
 footer(s,1,true);
 notes(s,`Hello, I’m Charith Kalhara, a Computer Science student at IIT Sri Lanka, affiliated with the University of Westminster. My professional brand is rooted in software development and cloud engineering. I’m interested in how digital solutions are designed, built, and made dependable for the people who use them.\n\nI want to be known as someone who connects software development with reliable cloud services. This combination reflects the technical areas I’m studying and the direction I’m choosing for my professional growth. In the next few minutes, I’ll outline the strengths I’m building, the values I want to bring to work, and my career direction.\n\nSource: https://lk.linkedin.com/in/charithkalhara`)
}
// 2: Strengths, expressed as professional capabilities rather than project evidence.
{
 const s=deck.slides.add();s.background.fill=C.paper;
 txt(s,'MY STRENGTHS',64,52,470,22,14,C.blue,true);
 txt(s,'From software to cloud operations',64,98,1100,56,40,C.navy,true);
 txt(s,'A broad technical foundation with an end-to-end perspective',66,166,990,32,21,C.slate,false);
 line(s,90,292,1064,C.line,2);
 const cols=[
  {x:100,n:'01',h:'DEVELOP',detail:'Laravel · PHP\nFilament · MySQL'},
  {x:490,n:'02',h:'DELIVER',detail:'Docker · GitHub Actions\nAWS · Oracle Cloud'},
  {x:880,n:'03',h:'OPERATE',detail:'Linux · Kubernetes\nTerraform · Ansible'}
 ];
 for(const c of cols){rect(s,c.x,273,40,40,C.navy);txt(s,c.n,c.x,279,40,24,13,C.paper,true);txt(s,c.h,c.x,344,300,28,16,C.blue,true);txt(s,c.detail,c.x,389,330,65,22,C.navy,true)}
 line(s,64,526,1152,C.line,2);
 txt(s,'WHAT I BRING',72,554,200,20,13,C.blue,true);
 txt(s,'I connect application development with deployment and operations.',280,543,875,42,21,C.navy,true);
 footer(s,2);
 notes(s,`My technical foundation spans application development, cloud platforms, delivery automation and operations. I’m building applications with technologies such as Laravel, PHP, Filament and MySQL. I’ve also worked with AWS and Oracle Cloud, Docker, GitHub Actions and Linux. Alongside that, I’m learning cloud-native tools including Kubernetes, Terraform and Ansible.\n\nThe strength I want to make distinctive is an end-to-end view. I’m interested in how an application is developed, how it reaches users, and what helps it run reliably. That perspective helps me connect the different parts of software delivery instead of treating them as separate skills. I’m continuing to develop these capabilities through my studies and technical practice.\n\nSource: https://lk.linkedin.com/in/charithkalhara`)
}
// 3: Values and personal brand behaviors.
{
 const s=deck.slides.add();s.background.fill=C.soft;
 txt(s,'MY PROFESSIONAL VALUES',64,52,520,22,14,C.blue,true);
 txt(s,'How I want to work',64,104,1000,58,42,C.navy,true);
 txt(s,'The principles behind the professional I am becoming',66,174,1000,32,21,C.slate,false);
 const vals=[
  {y:270,n:'01',word:'SECURITY',desc:'Treat access and data with care.'},
  {y:370,n:'02',word:'RESPONSIBILITY',desc:'Consider how technology affects people.'},
  {y:470,n:'03',word:'CONTINUOUS LEARNING',desc:'Keep improving my technical foundation.'}
 ];
 for(const v of vals){
  rect(s,72,v.y,42,42,C.navy);txt(s,v.n,72,v.y+8,42,24,13,C.paper,true);
  txt(s,v.word,148,v.y-1,360,26,16,C.blue,true);
  txt(s,v.desc,505,v.y-7,650,40,23,C.navy,true);
  if(v.n!=='03')line(s,148,v.y+62,1007,C.line,1);
 }
 footer(s,3);
 notes(s,`The professional identity I’m building is also shaped by how I want to work. Security matters to me because software often handles information people expect to be protected. I want to keep learning how to design systems with appropriate access and careful data handling.\n\nResponsibility means considering the people affected by technology, not only whether a system works. My interest in responsible technology has also led me to explore questions of ethics and governance around predictive AI in healthcare. Continuous learning is essential in a field that changes quickly, so I aim to keep strengthening my fundamentals as I learn new tools.\n\nThese values help define the professional brand I want to present: technically curious, careful with responsibility, and focused on building useful systems people can trust.\n\nSource: https://lk.linkedin.com/in/charithkalhara`)
}
// 4: Career direction and vision.
{
 const s=deck.slides.add();s.background.fill=C.navy;
 txt(s,'CAREER DIRECTION',64,54,420,22,14,C.pale,true);
 txt(s,'Cloud & DevOps engineering',64,106,1100,64,44,C.paper,true);
 txt(s,'My next steps',66,177,420,30,20,C.pale,false);
 line(s,86,326,1080,'#53657A',2);
 const steps=[
  {x:92,n:'01',t:'Deepen cloud\ninfrastructure skills'},
  {x:478,n:'02',t:'Build deployment\nautomation skills'},
  {x:864,n:'03',t:'Learn to improve\nservice reliability'}
 ];
 for(const a of steps){rect(s,a.x,305,42,42,C.gold);txt(s,a.n,a.x,313,42,24,13,C.navy,true);txt(s,a.t,a.x,376,300,72,22,C.paper,true)}
 line(s,64,516,1152,'#53657A',2);
 txt(s,'MY VISION',72,546,140,22,13,C.pale,true);
 txt(s,'Build cloud services that are useful, secure and dependable.',220,536,930,42,21,C.paper,true);
 footer(s,4,true);
 notes(s,`My career direction is cloud and DevOps engineering. I want to deepen my understanding of cloud infrastructure, strengthen my ability to automate deployment, and learn how to improve the reliability of services. I plan to do this through continued study, hands-on technical practice and collaboration with other developers.\n\nMy longer-term vision is to help build cloud services that are useful, secure and dependable. I want my work to combine sound software engineering with thoughtful operations, while keeping the people who rely on the technology in mind.\n\nTo sum up, my professional brand brings together software development, cloud engineering, responsibility and continuous learning. I’m Charith Kalhara, and I’m working toward becoming an engineer who builds systems people can trust. Thank you.\n\nSource: https://lk.linkedin.com/in/charithkalhara`)
}
const out='C:/Nadu_System/nadu_system/tmp/self-branding-build-v5';
for(let i=0;i<deck.slides.items.length;i++){const png=await deck.export({slide:deck.slides.items[i],format:'png',scale:1});await fs.writeFile(`${out}/slide-${i+1}.png`,new Uint8Array(await png.arrayBuffer()))}
await(await PresentationFile.exportPptx(deck)).save(`${out}/self-branding-draft.pptx`);
console.log(`Created ${deck.slides.items.length} slides`)
