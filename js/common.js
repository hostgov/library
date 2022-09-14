const logout = () => {
    axios.get("./php/logout.php").then(
        response => {
            localStorage.removeItem("login_member")
            const loginBtn = document.getElementById("loginBtn")
            const logoutBtn = document.getElementById("logoutBtn")
            loginBtn.removeAttribute("hidden")
            logoutBtn.setAttribute("hidden", "true")
            alert("logout successfully", "success")
        },
        error => {
            alert("logout failed", "danger")
        }
    )
}

const login = () => {
    const email = document.getElementById('email').value.trim()
    const password = document.getElementById('password').value.trim()
    //todo validation
    const params = new FormData()
    params.append('email', email)
    params.append('password', password)

    axios({
        url: './php/login.php',
        method: 'POST',
        data: params
    }).then(
        response => {
            console.log(response)
            if (response.data.code === "0") {
                alert('Login successfully. Welcome back ' + response.data.data.firstName, 'success')
                localStorage.setItem("login_member", JSON.stringify(response.data.data))
                const loginBtn = document.getElementById("loginBtn")
                const logoutBtn = document.getElementById("logoutBtn")
                logoutBtn.removeAttribute("hidden")
                loginBtn.setAttribute("hidden", "true");
                location.href = './bookshelf.html'
            } else {
                alert('Login failed ' + response.data.message)
            }

        },
        error => {
            alert(error.message, 'danger')
        }
    )
}
