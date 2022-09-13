(function () {


    /*======
    * Sticky
    * */
    window.onscroll = function () {
        const header_navbar = document.getElementById("header_navbar")
        const logo = document.querySelector("img#logo")
        let sticky = header_navbar.offsetTop
        if (window.pageYOffset > sticky) {
            header_navbar.classList.add("sticky")
            logo.setAttribute("src", "images/logo-red-trans-svg.svg")
        } else {
            header_navbar.classList.remove("sticky")
            logo.setAttribute("src", "images/logo-white-trans-svg.svg")
        }
        /*======
        * show or hide back-to-top button
        * */
        const backToTop = document.querySelector(".back-to-top")
        if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
            backToTop.style.display = "block"
        } else {
            backToTop.style.display = "none"
        }
    }

    // for menu scroll
    const pageLink = document.querySelectorAll('.page-scroll')

    pageLink.forEach(elem => {
        elem.addEventListener('click', e => {
            e.preventDefault();
            document.querySelector(elem.getAttribute('href')).scrollIntoView({
                behavior: 'smooth',
                offsetTop: 1 - 60,
            });
        });
    });

    // section menu active
    function onScroll(event) {
        const sections = document.querySelectorAll('.page-scroll')
        const scrollPos = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop

        for (let i = 0; i < sections.length; i++) {
            const currLink = sections[i]
            const val = currLink.getAttribute('href')
            const refElement = document.querySelector(val)
            let scrollTopMinus = scrollPos + 73
            if (refElement.offsetTop <= scrollTopMinus && (refElement.offsetTop + refElement.offsetHeight > scrollTopMinus)) {
                document.querySelector('.page-scroll').classList.remove('active')
                currLink.classList.add('active')
            } else {
                currLink.classList.remove('active')
            }
        }
    }

    window.document.addEventListener('scroll', onScroll)



    //===== close navbar-collapse when a  clicked
    const navbarToggler = document.querySelector(".navbar-toggler")
    // const navbarToggler = document.getElementById('navbar-toggler')
    const navbarCollapse = document.querySelector(".navbar-collapse")

    navbarToggler.addEventListener('click', () => {
        navbarToggler.classList.toggle('active')
    })

    document.querySelectorAll(".page-scroll").forEach(e =>
        e.addEventListener("click", () => {
            navbarToggler.classList.remove("active")
            navbarCollapse.classList.remove('show')
        })
    );


    //WOW Scroll Spy
    let wow = new WOW({
        //disabled for mobile
        mobile: false
    });
    wow.init();


    // ====== scroll top js
    function scrollTo(element, to = 0, duration= 1000) {

        const start = element.scrollTop
        const change = to - start
        const increment = 20
        let currentTime = 0

        const animateScroll = (() => {

            currentTime += increment

            element.scrollTop = Math.easeInOutQuad(currentTime, start, change, duration)

            if (currentTime < duration) {
                setTimeout(animateScroll, increment)
            }
        })

        animateScroll()
    }

    Math.easeInOutQuad = function (t, b, c, d) {

        t /= d/2;
        if (t < 1) return c/2*t*t + b
        t--;
        return -c/2 * (t*(t-2) - 1) + b
    };

    document.querySelector('.back-to-top').onclick = function () {
        scrollTo(document.documentElement)
    }

})();
