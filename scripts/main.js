
const changeTheme = (classNames=[]) => {
    while (document.body.classList.length > 0) 
        document.body.classList.remove(classList[0])
    
    if(!Array.isArray(classNames)) classNames = [classNames]

    classNames.forEach(cn=>document.body.classList.add(cn))
}


const show = id => {
    const target = document.getElementById(id)
    target&&target.classList.remove('hide')
}
const hide = id => {
    const target = document.getElementById(id)
    target&&target.classList.add('hide')
}
