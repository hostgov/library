const emailReg = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/
const nameReg = /^[\w'\-,.][^0-9_!¡?÷?¿/\\+=@#$%ˆ&*(){}|~<>;:[\]]{0,19}$/
const passwordReg = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,15}/



const checkField = (e, reg, len) => {
    const ele = e.target
    const v = ele.value.trim()
    if (v.length <= len && reg.test(v)) {
        changeInputStatus(ele, "is-valid")
        return true
    } else {
        changeInputStatus(ele, "is-invalid")
        return false
    }
}
function changeInputStatus(ele, statu) {
    switch (statu) {
        case "is-valid":
            if (!ele.classList.contains("is-valid")) {
                if (ele.classList.contains("is-invalid")) {
                    ele.classList.remove("is-invalid")
                }
                ele.classList.add("is-valid")
            }
            break;
        case "is-invalid":
            if (!ele.classList.contains("is-invalid")) {
                if (ele.classList.contains("is-valid")) {
                    ele.classList.remove("is-valid")
                }
                ele.classList.add("is-invalid")
            }
            break;
        default:
            break;
    }
}

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
                clearCookie('libraryLoginUser')
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
const checkLoginBack2 = () => {
    return axios({
        url: '/library/php/authentication.php',
        method: 'POST'
    }).then(
        response => {
            if (response.data.code === "2" || response.data.code === "3") {
                return Promise.resolve(response.data) // login
            } else {
                return Promise.reject(response.data) //no login
            }
        },
        error => {
            return Promise.reject(error) // failed with back-end checking
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

function getCookie(cname)
{
    let name = cname + "="
    let ca = document.cookie.split(';')
    for(let i=0; i<ca.length; i++)
    {
        let c = ca[i].trim()
        if (c.indexOf(name)===0) return decodeURIComponent(c.substring(name.length,c.length))
    }
    return ""
}
function  clearCookie(cname) {
    setCookie(cname, "", -1)
}
function setCookie (cname, cvalue, exdays) {
    let d = new Date()
    d.setTime(d.getTime() + (exdays*24*50*60*1000))
    let expires = "expires"+d.toUTCString()
    document.cookie = cname + "=" + cvalue + ";" + expires
}
const checkLoginFront = () => {
    return new Promise((resolve, reject) => {
        const cookie = getCookie("libraryLoginUser")
        if (cookie !== "") {
            resolve(JSON.parse(cookie)) //login
        } else {
            reject("failed to get cookie") //no login
        }
    })
}