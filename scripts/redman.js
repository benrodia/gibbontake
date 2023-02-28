// HELPER FUNCTIONS

const rand = (arr,getInd) => {
    const ind = Math.floor(Math.random()*arr.length)
    return getInd ? ind : arr[ind]
}
const chance = (num = .5) => Math.random() < num


// placeholder for HTML elements
let redmanCont, redman, textBox

// define corners of screen, 0px away from said axes
const corners = [
    {bottom:0,right:0},
    {bottom:0,left:0},
    {top:0,right:0},
    {top:0,left:0},
]

// ARG default values
const argDef = { 
    active: false, // true after visiting redman comic page, if true he spawns
    stillActive: false, // true if user changes pages with active still true
    corner: 0, // index of corner for redman to be in (see const corners)
    dismissed: 0, // # of times redman is dismissed (clicked on)
    friendship: 0, // points gained for doing things, use for progression system in redmanEvents.js
    started: Date.now(), // timestamp of first time visiting site
    last: Date.now(), // most recent time progress was updated
    quests: [], // list of active/completed quests (see redmanEvents.js)
}

// vvv uncomment to wipe cache
// localStorage.removeItem('arg')

// initialize arg object with either cached info or default values
let arg = JSON.parse(localStorage.getItem('arg')) || argDef

console.log(arg)


// temporary data NOT saved to cache
let state = {
    click: false, // has been clicked
    clicked: 0, // # of times clicked
    hover: false, // is being hovered
    hovered: 0, // # of times hovered
    hiding: false, // true if clicked by default (if dismissed)
    reacting: false, // is currently doing speech bubble
    squished: false, // is being hovered and will try to squish
    size: 150, // width/height of redman image
    x: 0, // current mouse x in px
    y: 0, // current mouse y in px
    w: window.innerWidth, // browser window width
    h: window.innerHeight, // browser window height
    url: location.pathname, // url value after domain name or localhost
    idle: 0 // time in seconds spent on page (resets if redman hovered/clicked)
}

// reference of possible reaction faces (faces set css classes. update those to add new faces)
const faces = ['neutral','talk']


// save cache with all current values
const updateProg = _ => localStorage.setItem('arg', JSON.stringify({...arg, last: Date.now()}))


const checkHover = e => {
    if(!redman) return
    x = e.clientX, y = e.clientY
    const corner = corners[arg.corner] || corners[0]
    const vert = (corner.top===0&&y<state.size)||(corner.bottom===0&&y>state.h-state.size)
    const hori = (corner.left===0&&x<state.size)||(corner.right===0&&x>state.w-state.size)
    if(!state.hover && vert && hori) state.hovered++
    state.hover = vert && hori
}

document.body.onmousemove = checkHover
window.onresize = _ => {w = window.innerWidth; h = window.innerHeight}


// PUBLIC: set face, will stay that way if called on its own
const makeFace = (face='neutral') => faces.forEach(f => f===face ? redman.classList.add(f) : redman.classList.remove(f))

// PUBLIC: input text for textbox, time in ms on screen, face to use, and bool to override last reaction with this one
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
    makeFace('neutral')
    state.reacting = false
}
// called on unhover event
const unsquish = _=> {
    if(state.hiding) return
    const corner = corners[arg.corner] || corners[0]
    const scale = [(corner.left===0?1:-1),(corner.top===0?-1:1)]
    redman.style.scale = scale.join(' ')
    redman.style.translate = `0 0`
    state.squished = false
}
// called on hover event
const squish = _=> {
    if(state.hiding) return
    const corner = corners[arg.corner] || corners[0]
    const scale = [(corner.left===0?1:-1),(corner.top===0?-1:1)]
    redman.style.translate = `${0} ${(state.size/2)*scale[1]}px`
    redman.style.scale = scale.map((s,i)=>i?s*.2:s).join(' ')
    state.squished = true
    state.idle = 0
}

// called on click event
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
    // redmanCont.style.opacity = 0
    state.idle = 0
}

// called on page load if arg.active, makes actual HTML el
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
    unsquish()
}



if(state.url.includes('redman')) {
    arg.stillActive = arg.active
    arg.active = true
}

if(arg.active) spawnRedman()

// update function that checks if event conditions are met and executes them
const checkEvents = (arg,state) => {
    state.idle += checkEventsEvery
    redmanEvents.forEach(ev=>{
        const params = [arg,state,ev,redmanEvents,redmanQuests]
        const met = ev.condition(...params) && chance(ev.chance||1)
        if(met && !ev.onced) {
            if(ev.once) ev.onced = true
            else if(ev.onceUntil) ev.onced = !ev.onceUntil(...params)

            if(ev.action) {
                ev.action(...params)
                updateProg()
            }
            if(!ev.text) return
            if(!ev.text.length) {
                ev.exhausted && ev.exhausted(...params)
                return updateProg()
            }
            const ind = ev.isRandom ? rand(ev.text,true) : 0
            const choice = ev.exhaust ? ev.text.splice(ind,1)[0] : ev.text[ind]
            react(choice,(ev.time||2)*1000,ev.face,ev.forced)
        } 
        else if(ev.onceUntil) ev.onced = !ev.onceUntil(...params)
    })

    redmanQuests.forEach(q=>{
        const existing = arg.quests.find(qu=>q.name===qu.name)
        const params = [arg,state,{...q,...(existing||{})},redmanEvents,redmanQuests]
        if(existing) {
            if(existing.completed || existing.failed) return
            const failed = q.toProgress && q.toProgress(...params)
            if(failed) {
                if(q.canRetry) arg.quests = arg.quests.filter(qu=>qu.name !== q.name)
                else existing.failed = true
                if(q.onFail) q.onFail(...params)
                return updateProg()
            }
            const progressed = q.toProgress && q.toProgress(...params)
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
                if(q.onProgress) q.onProgress(...params)
            }
            if((q.type==='collect'?existing.progress.length:existing.progress) >= q.needed) {
                existing.completed = true
                q.reward && q.reward(...params)
                updateProg()
            }
        }
        else if(q.toUnlock && q.toUnlock(...params)) {
            arg.quests.push({
                name: q.name,
                progress: q.type==='collect' ? [] : 0,
                completed: false,
            })
            if(q.onUnlock) q.onUnlock(...params)
            updateProg()
        }
    })
}

// initialize updater
const eventTimer = !redman || setInterval(_=>checkEvents(arg,state), (checkEventsEvery||.1)*1000)

