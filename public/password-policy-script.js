window.onload = function() {
    var newUser = document.getElementById("createuser");
    var editCurrentUser = document.getElementById("your-profile");

    if (newUser || editCurrentUser) {
        
        //Remove checkbox weak password
        var weakPassword = document.getElementsByClassName('pw-weak');
        weakPassword[0].remove();
    }
}
