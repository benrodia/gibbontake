const lb = document.getElementById('lightbox-cont')
const lbImg = document.getElementById('lightbox-img')
const lbBg = document.getElementById('lightbox-bg')
let images = [...document.querySelectorAll('.gallery-img')].map(img=>img.src)
let cur = 0

if(lb&&lbBg&&lbImg) {


const setImg = ind => {
	if(ind<0) ind = images.length-1
	if(ind>=images.length) ind = 0
	cur = ind
	lbImg.src = images[cur]
}

document.body.onclick = e => {
	const ev = window.event ? e.srcElement: e.target;
	if(ev.classList.contains('gallery-img')) {
		const got = images.indexOf(ev.src)
		if(got>=0) {
			setImg(got)
			lb.classList.remove('hide')
		}
	}
}


lbBg.addEventListener('click', _ => {
    lb.classList.add('hide')
})

window.onkeyup = e => {
	if(!lb.classList.contains('hide')) {
		if(e.keyCode===37) setImg(cur-1)
		if(e.keyCode===39) setImg(cur+1)	
	}
}

}


// window.onmousemove = e => {
// 	if(!lb.classList.contains('hide')) {
		
// 	}
// }