


const interact = [
    {
        condition: (arg,state) => state.click,
        action: (arg,state) => {
            react("ok ill go",2000,'neutral',true)
            slink()
            if(arg.dismissed<4) arg.corner++
            else arg.corner = 0
        },
        once: true,
    },
    {
        condition: (arg,state) => state.hover,
        onceUntil: (arg,state) => !state.hover,
        action: (arg,state) => {
            squish()
            react(rand([
                "scuse me",
                "hey, im lurkin here",
                "oop",
                "eek, a mouse",
                "that's my personal space!"
            ]),2000,'neutral',true)
        },
    },
    {
        condition: (arg,state) => !state.hover,
        action: () => normal(),
    },
]

const dismiss = [
    {
        condition: (arg,state) => !arg.stillActive && arg.corner===1,
        once: true,
        text: ["hey, how's this corner?"]
    },
    {
        condition: (arg,state) => !arg.stillActive && arg.corner===2,
        once: true,
        text: ["sorry if i keep getting in the way"]
    },
    {
        condition: (arg,state) => !arg.stillActive && arg.corner===3,
        once: true,
        text: ["all the blood's rushing to my head"]
    },

]

const lowfsidles = [
    "i really like the art on this one",
    "so insightful",
    "ehh, ive done better",
    "this one's quite good i think",
    "i clapped. i clapped when i read it.",
    "wow, i look so young in this one.",
    "to think, this is just as relevant today as when it came out.",
    "heh. i geddit.",
    "whooooa, i cannot BELIEVE i said THAT",
    "i need my own tv show. i tell you what.",
    "uhhhhhh... next.",
    "*COUGH*",
]

const idle = [
    {
        condition: (arg,state) => state.idle > 7 && state.url.includes('/redman/') && arg.friendship < 1,
        once: true,
        action: _=> react(rand(lowfsidles),4000)
    },
    {
        condition: (arg,state) => state.idle > 7 && state.url.includes('/redman/') && arg.friendship >= 1,
        once: true,
        action: _=> react(rand(
            chance(.6) ? lowfsidles : [
                "this really speaks to me",
                "damn... that's deep",
                "hmmmmmm...",
                "i like red. hbu?",
                "oh hoh, i've been waiting to see this one.",
                "makes ya think, dunnit?",
                "what am i EVEN talking about?",
        ]),4000)
    },
]


const checkEventsEvery = .1 // time in seconds

const redmanEvents = [
    ...interact,
    ...dismiss,
    ...idle
]


const redmanQuests = [
    {
        name: 'Read me',
        toUnlock: (arg,state)=>arg.active,
        prompt: "ooh, i love this guy. let's read some more.",
        needed: 20,
        type: 'collect',
        toProgress: (arg,state)=> {
            if(arg.active && state.idle > 7 && state.url.includes('/redman/')) return state.url
            else return false
        },
        reward: (arg,state) => {
            arg.friendship++
            react("you really like my comic THIS MUCH? cool.")
        }
    },
    {
        name: 'around tha world',
        toUnlock: (arg,state)=> arg.dismissed>=4,
        needed: 1,
        toProgress: (arg,state)=> arg.active && arg.dismissed>=4,
        reward: (arg,state) => {
            arg.friendship++
            react("got it. ill stay right here from now on")
        }
    },
    {
        name: 'adventurousness',
        toUnlock: (arg,state)=>arg.active,
        prompt: "wow, i've never been here before.",
        needed: 7,
        type: 'collect',
        toProgress: (arg,state)=> {
            if(arg.active && !state.url.includes('/redman/')) return state.url
            else return false
        },
        reward: (arg,state) => {
            arg.friendship++
            react("the world is filled with weird stuff.")
        }
    },
]
