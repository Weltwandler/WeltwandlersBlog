function enable(id) {
    document.getElementById(id).removeAttribute("disabled");
    document.getElementById("submit-main-form-profile").style.display = "block";
}

function toggle(id) {
    if (window.getComputedStyle(document.getElementById(id)).display == "block") {
        document.getElementById(id).style.display = "none";
    } else {
        document.getElementById(id).style.display = "block";
    }
}

function aboutShow(id) {
    let articles = document.getElementsByClassName('about-article');

    for (var a in articles) {
        if (articles[a].id == id) {
            articles[a].style.display = "block";
        } else {
            articles[a].style.display = "none";
        }
    }
}

// Make header appear/disappear when scrolling down/up

/*
window.onscroll = function() {makeSticky()};

let header = document.getElementById('header');

let sticky = header.offsetTop;

function makeSticky() {
    if (window.scrollY > sticky) {
        header.classList.add('sticky');
    } else {
        header.classList.remove('sticky');
    }
}
*/

/*
let prevPos = window.scrollY;
let header = document.getElementById('header');

window.addEventListener('scroll', function() {
    const currentPos = window.scrollY;

    if (prevPos < currentPos) {
        // this.document.getElementById('header').style.visibility = 'hidden';
        // this.document.getElementById('header').style.position = 'static';
        this.document.getElementById('header').style.zIndex = "0";
    } else {
        // this.document.getElementById('header').style.position = 'sticky';
        this.document.getElementById('header').style.zIndex = "2";
    }

    prevPos = currentPos;
});
*/