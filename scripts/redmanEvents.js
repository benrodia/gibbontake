

// EVENTS
// condition: function that returns true/false. if true, fire action/text. Checked every update
// once: bool. Event happens only once (per page instance)
// onceUntil: function that operates as once flag, but will un-onced the event if it returns true. Use this OR once
// onced: private bool (do not set). flags event with once/onceUntil as being used (resets on page load)
// action: function called if condition met. Can include react(), or setting various arg values, or really anything
// text: array -> string. simple alternative to action. same as writing "action: ()=> react('some text')" but with other features listed below
// isRandom: bool. used with text (not action), picks random item from text choices for reaction
// exhaust: bool. used with text (not action), will not repeat used responses
// exhausted: function called when all responses have been exhausted. (only use with text + exhaust, obviously)
// chance: number 0-1. chance for action/text to fire if condition met. Won't use up "once" until it does. same as including chance() in condition check


// QUESTS
// name: string. unique name of quest. used as ID to check what quests you have
// toUnlock: function that returns true/false. once true, will add initiate quest and add it to arg cache
// onUnlock: function.
// prompt: string. text for reaction that fires when quest is unlocked.
// type: string. type of quest. right now only supports "collect" or null. This changes the behavior of some other params
// progress: private number | array (stored/cached in arg.quests[#], not here). current progression through quest. Default (null) type quests record progress as number from 0-X. Collect quests record progress as an array of UNIQUE string values 
// needed: number. Amount of progress needed to complete quest. Update checks if progress number or length of array matches this number. Completes quest when it does.
// toProgress: function that evaluates your logic for progression, and returns a value. Falsey values do not progress. Truthy values will increment progress for default type quests, or push return value for collect type quests. 
// onProgress: function.
// reward: function that calls when quest is completed. Use to set arg values, give reaction, etc. Quest can only be completed once.
// toFail: function that returns bool. If true, quest is marked "failed".
// onFail: function. Use for failstate reaction + setting state/arg/etc.
// canRetry: bool. If true, quest is removed from list on fail and can be reunlocked normally. Otherwise quest is permanently failed

// NOTES to reader
// ALL bools are false by default
// ALL functions have access to, in this order (arg,state,self,events,quests)
    // arg: current values of arg object. any changes set will be cached. Note you can use this to access quest progress/what quests you have
    // state: current values of state object. any values can be set (but are reset on refresh, as usual)
    // self: the specific event/quest that this function belongs to (otherwise inaccessable from inside its declaration)
        // includes temp values like "onced" for events
        // includes progress for quests
    // events: the list of all events. otherwise inaccessable from inside its declaration.
    // quests: the list of all quests. otherwise inaccessable from inside its declaration.

const interact = [
    {
        condition: (arg,state) => state.click,
        action: (arg) => {
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
        action: () => {
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
        action: () => unsquish(),
    },
]

const dismiss = [
    {
        condition: (arg) => !arg.stillActive && arg.corner===1,
        once: true,
        text: ["hey, how's this corner?"]
    },
    {
        condition: (arg) => !arg.stillActive && arg.corner===2,
        once: true,
        text: ["sorry if i keep getting in the way"]
    },
    {
        condition: (arg) => !arg.stillActive && arg.corner===3,
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
        toUnlock: (arg)=>arg.active,
        prompt: "ooh, i love this guy. let's read some more.",
        needed: 20,
        type: 'collect',
        toProgress: (arg,state)=> {
            if(arg.active && state.idle > 7 && state.url.includes('/redman/')) return state.url
            else return false
        },
        reward: (arg) => {
            arg.friendship++
            react("you really like my comic THIS MUCH? cool.")
        }
    },
    {
        name: 'around tha world',
        toUnlock: (arg)=> arg.dismissed>=4,
        needed: 1,
        toProgress: (arg)=> arg.active && arg.dismissed>=4,
        reward: (arg) => {
            arg.friendship++
            react("got it. ill stay right here from now on")
        }
    },
    {
        name: 'adventurousness',
        toUnlock: (arg)=>arg.active,
        prompt: "wow, i've never been here before.",
        needed: 7,
        type: 'collect',
        toProgress: (arg,state)=> {
            if(arg.active && !state.url.includes('/redman/')) return state.url
            else return false
        },
        reward: (arg) => {
            arg.friendship++
            react("the world is filled with weird stuff.")
        }
    },
]
