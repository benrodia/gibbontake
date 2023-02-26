// HELPER FUNCTIONS

const rand = (arr,getInd) => {
    const ind = Math.floor(Math.random()*arr.length)
    return getInd ? ind : arr[ind]
}
const chance = (num = .5) => Math.random() < num


// DEFINE TERMS
let redmanCont, redman, textBox

const argDef = {
    active: false,
    stillActive: false,
    corner: 0,
    dismissed: 0,
    friendship: 0,
    started: Date.now(),
    last: Date.now(),
    quests: [],
}

// localStorage.removeItem('arg')

let arg = JSON.parse(localStorage.getItem('arg')) || argDef

console.log(arg)

const corners = [
    {bottom:0,right:0},
    {bottom:0,left:0},
    {top:0,right:0},
    {top:0,left:0},
]


let state = {
    click: false,
    clicked: 0,
    hover: false,
    hovered: 0,
    hiding: false,
    reacting: false,
    squished: false,
    size: 150,
    x: 0,
    y: 0,
    w: window.innerWidth,
    h: window.innerHeight,
    url: location.pathname,
    idle: 0
}


const faces = ['neutral','talk']



const updateProg = _ => localStorage.setItem('arg', JSON.stringify({...arg, last: Date.now()}))

const checkHover = e => {
    if(!redman) return
    const corner = corners[arg.corner] || corners[0]
    const vert = (corner.top===0&&e.clientY<state.size)||(corner.bottom===0&&e.clientY>window.innerHeight-state.size)
    const hori = (corner.left===0&&e.clientX<state.size)||(corner.right===0&&e.clientX>window.innerWidth-state.size)
    if(state.hover && !(vert && hori)) state.hovered++
    state.hover = vert && hori
}

document.body.onmousemove = checkHover
window.onresize = _ => {w = window.innerWidth; h = window.innerHeight}




const makeFace = (face='neutral') => faces.forEach(f => f===face ? redman.classList.add(f) : redman.classList.remove(f))

const react = (text = 'Hello', timer = 1000, face='talk',forced) => { 
    if(!forced && (state.reacting||state.hiding)) return
    state.reacting = true
    makeFace(face)
    textBox.classList.remove('hide')
    textBox.innerHTML = text
    setTimeout(unreact, timer)
}

const unreact = _ => {
    textBox.classList.add('hide')
    makeFace()
    state.reacting = false
}

const normal = _=> {
    if(state.hiding) return
    const corner = corners[arg.corner] || corners[0]
    const scale = [(corner.left===0?1:-1),(corner.top===0?-1:1)]
    redman.style.scale = scale.join(' ')
    redman.style.translate = `0 0`
    state.squished = false
}

const squish = _=> {
    if(state.hiding) return
    const corner = corners[arg.corner] || corners[0]
    const scale = [(corner.left===0?1:-1),(corner.top===0?-1:1)]
    redman.style.translate = `${0} ${(state.size/2)*scale[1]}px`
    redman.style.scale = scale.map((s,i)=>i?s*.2:s).join(' ')
    state.squished = true
    state.idle = 0
}

const slink = () => {
    if(state.hiding) return
    const corner = corners[arg.corner] || corners[0]
    const scale = [(corner.left===0?1:-1),(corner.top===0?-1:1)]
    state.hiding = true
    arg.active = false
    arg.stillActive = false
    arg.dismissed++
    redmanCont.style.transition = `1s ease-in`
    redmanCont.style.translate = `0 ${state.size*scale[1]}px`,
    redmanCont.style.opacity = 0
    state.idle = 0
}

const spawnRedman = () => {
    const corner = corners[arg.corner] || corners[0]
    const redmanContEl = document.createElement('div')
    const redmanEl = document.createElement('div')
    const textEl = document.createElement('div')
    redmanContEl.classList.add('redman-cont')
    textEl.classList.add('redman-text')
    textEl.classList.add('hide')
    redmanEl.classList.add('redman')
    redmanEl.classList.add(faces[0])

    redmanContEl.style.width = state.size + 'px'
    redmanContEl.style.height = state.size + 'px'
    redmanEl.style.width = state.size + 'px'
    redmanEl.style.height = state.size + 'px'

    Object.entries(corner).forEach(([k,v]) => redmanContEl.style[k] = v)
    
    textEl.style.maxWidth = state.size + 'px'
    textEl.style.translate = `0 ${state.size*(corner.top===0?1:-.5)}px`;
    
    redmanEl.onclick = e => {
        state.click = true
        state.clicked++
    }
    
    redmanContEl.append(redmanEl)
    redmanContEl.append(textEl)
    redman = redmanEl
    textBox = textEl
    redmanCont = redmanContEl
    document.body.append(redmanContEl)
    normal()
}




if(state.url.includes('redman')) {
    arg.stillActive = arg.active
    arg.active = true
}

if(arg.active) spawnRedman()

const checkEvents = (arg,state) => {
    state.idle += checkEventsEvery
    redmanEvents.forEach(ev=>{
        const met = ev.condition(arg,state) && chance(ev.chance||1)
        if(met && !ev.onced) {
            if(ev.once) ev.onced = true
            else if(ev.onceUntil) ev.onced = !ev.onceUntil(arg,state)

            if(ev.action) {
                ev.action(arg,state)
                updateProg()
            }
            if(!ev.text) return
            if(!ev.text.length) {
                ev.exhausted && ev.exhausted()
                return updateProg()
            }
            const ind = ev.isRandom ? rand(ev.text,true) : 0
            const choice = ev.exhaust ? ev.text.splice(ind,1)[0] : ev.text[ind]
            react(choice,(ev.time||2)*1000,ev.face,ev.forced)
        } 
        else if(ev.onceUntil) ev.onced = !ev.onceUntil(arg,state)
    })

    redmanQuests.forEach(q=>{
        const existing = arg.quests.find(qu=>q.name===qu.name)
        if(existing) {
            if(existing.completed) return
            const progressed = q.toProgress && q.toProgress(arg,state,redmanQuests,existing)
            if(progressed) {
                if(q.type==='collect') {
                    if(!existing.progress.includes(progressed)) {
                        existing.progress.push(progressed)
                        updateProg()    
                    }
                }
                else {
                    existing.progress++
                    updateProg()
                }
            }
            if((q.type==='collect'?existing.progress.length:existing.progress) >= q.needed) {
                existing.completed = true
                q.reward && q.reward(arg,state)
                updateProg()
            }
        }
        else if(q.toUnlock && q.toUnlock(arg,state)) {
            arg.quests.push({
                name: q.name,
                progress: q.type==='collect' ? [] : 0,
                completed: false,
            })
            react(q.prompt,5000)
            updateProg()
        }
    })
}

const eventTimer = !redman || setInterval(_=>checkEvents(arg,state), (checkEventsEvery||.1)*1000)

