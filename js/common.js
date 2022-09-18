const changeBtnLoginLogout = (type) => {
    const loginBtn = document.getElementById("loginBtn")
    const logoutBtn = document.getElementById("logoutBtn")
    if (type === "login") {
        logoutBtn.removeAttribute("hidden")
        loginBtn.setAttribute("hidden", "true")
    } else {
        loginBtn.removeAttribute("hidden")
        logoutBtn.setAttribute("hidden", "true")
    }

}

const logout = () => {
    if (window.confirm("Please confirm you want to log out")) {
        axios.get("../php/logout.php").then(
            response => {
                localStorage.removeItem("login_member")
                changeBtnLoginLogout("logout")
                alert("logout successfully", "success")
            },
            error => {
                alert("logout failed", "danger")
            }
        )

    }

}



const checkLoginBack = () => {
    axios({
        url: './php/authentication.php',
        method: 'POST'
    }).then(
        response => {
            if (response.data.code === "2" || response.data.code === "3") {
                changeBtnLoginLogout("login")
            } else {
                changeBtnLoginLogout("logout")
            }
        },
        error => {
            alert(error.message, 'danger')
        }
    )
}
const checkLoginFront = () => {
    if (localStorage.getItem("login_member")) {
        if (window.confirm("You have already login, do you want to log out ?") ) {
            logout()
        } else {
            location.href = "/library/index.html"
        }
    }
}
async function  checkAdmin ()  {
    await axios({
        url: '../php/authentication.php',
        method: 'POST'
    }).then(
        response => {
            if (response.data.code === "3") {
                changeBtnLoginLogout("login")
                return true
            } else {
                alert('You are not login or login without admin role, redirect to index page', 'danger')
                // setInterval(() => {
                //     location.href= "/library/index.html"
                // }, 2000)
                return false
                // location.href= "/library/index.html"
            }

        },
        error => {
            alert(error.message, 'danger')
            // setInterval(() => {
            //     location.href= "/library/index.html"
            // }, 2000)
            return false
            // location.href= "/library/index.html"
        }
    )
}

const logoutBtn = document.getElementById("logoutBtn")
logoutBtn.addEventListener("click", e => {
    e.preventDefault()
    e.stopPropagation()
    logout()
    location.href = '/library/index.html'
})
