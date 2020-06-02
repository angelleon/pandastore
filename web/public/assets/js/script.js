function validateLoginData() {
    console.log("validating data");
    let txtInputEmail = document.getElementById("txtEmail");
    let txtInputPasswd = document.getElementById("txtPasswd");
    if (txtInputEmail && txtInputPasswd) {
        let email = txtInputEmail.value;
        let passwd = txtInputPasswd.value;
        if (passwd.length < 8) {
            txtInputPasswd.classList.add("is-danger");
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
    let txtGivenName = document.getElementById("txtName");
    let txtSurname = document.getElementById("txtSuraname");
    let txtPasswd = document.getElementById("txtPasswd");
    let txtConfirmPasswd = document.getElementById("txtConfirmPasswd");
    if (txtGivenName && txtSurname && txtPasswd && txtConfirmPasswd) {
       if (txtPasswd.value != txtConfirmPasswd.value) {
           //TODO: agregar colores de advertencia a los campos de texto
           return false;
       }
    }
    return true;
}

function showPasswd(id) {
    let txtElmnt = document.getElementById(id);
    txtElmnt.type = "text";
}

function hidePasswd(id) {
    let txtElmnt = document.getElementById(id);
    txtElmnt.type = "password";
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