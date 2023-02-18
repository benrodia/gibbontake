const toggleBtn = document.getElementById('page-select-btn')
const cont = document.getElementById('page-select-cont')
const exit = document.getElementById('exit')
const contOutside = document.getElementById('page-select-bg')

toggleBtn.addEventListener('click', _ => {
    cont.classList.toggle('hide')
    console.log('eyyy')
})
exit.addEventListener('click', _ => {
    cont.classList.add('hide')
    console.log('eyyy')
})
contOutside.addEventListener('click', _ => {
    cont.classList.add('hide')
    console.log('eyyy')
})