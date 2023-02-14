

const body = document.getElementById('entry')
const cursor = document.getElementById('cursor')

body.onmousemove = e => {
    cursor.style.left = e.clientX + 'px'
    cursor.style.top = e.clientY + 'px'
}

// body.onclick = _ => cursor.classList.add('active')