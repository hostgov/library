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
        axios.get("/library/php/logout.php").then(
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
        url: '/library/php/authentication.php',
        method: 'POST'
    }).then(
        response => {
            if (response.data.code === "2" || response.data.code === "3") {
                changeBtnLoginLogout("login")
            } else {
                changeBtnLoginLogout("logout")
                localStorage.clear()
            }
        },
        error => {
            alert(error.message, 'danger')
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
