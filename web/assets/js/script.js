function validateLoginData() {
    console.log("validating data");
    debugger;
    let txtInputEmail = document.getElementById("txtEmail");
    let txtInputPasswd = document.getElementById("txtPasswd");
    if (txtInputEmail && txtInputPasswd) {
        let email = txtInputEmail.value;
        let passwd = txtInputPasswd.value;
        if (passwd.length < 8) {
            txtInputPasswd.classList.add("is-danger");
            debugger;
            return false;
        }
        if (document.frmLogin) {
            document.frmLogin.submit();
        }
    }
    return false;
}

function validateSingupData() {
    console.log("validating singup data");
    debugger;
    let txtGivenName = document.getElementById("txtName");
    let txtSurname = document.getElementById("txtSuraname");
    let txtPasswd = document.getElementById("txtPasswd");
    let txtConfirmPasswd = document.getElementById("txtConfirmPasswd");
    if (txtGivenName && txtSurname && txtPasswd && txtConfirmPasswd) {
       if (txtPasswd.value != txtConfirmPasswd.value) {
           //TODO: agregar colores de advertencia a los campos de texto
       }
    }
    return true;
}

function resetInputColor(elmnt, newClass) {
    /*
    if (elmnt.classList.contains("is-danger")) {

    } else if () {

    } else if () {

    } else if () {

    } else if () {

    } else if () {

    }
    elmnt.classList()
    */
}