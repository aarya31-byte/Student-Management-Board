/*==================================================
        STUDENT MANAGEMENT BOARD
              LOGIN JAVASCRIPT
==================================================*/


document.addEventListener(

    "DOMContentLoaded",

    function(){

        /*========================
            GET ELEMENTS
        ========================*/

        const loginForm =

            document.getElementById(

                "loginForm"

            );


        const passwordInput =

            document.getElementById(

                "password"

            );


        const togglePassword =

            document.getElementById(

                "togglePassword"

            );


        /*========================
          PASSWORD SHOW / HIDE
        ========================*/

        if(togglePassword){

            togglePassword.addEventListener(

                "click",

                function(){

                    if(

                        passwordInput.type ===

                        "password"

                    ){

                        passwordInput.type =

                            "text";


                        togglePassword.innerHTML =

                            '<i class="fa-solid fa-eye-slash"></i>';

                    }

                    else{

                        passwordInput.type =

                            "password";


                        togglePassword.innerHTML =

                            '<i class="fa-solid fa-eye"></i>';

                    }

                }

            );

        }


        /*========================
              LOGIN
        ========================*/

        if(loginForm){

            loginForm.addEventListener(

                "submit",

                function(event){

                    event.preventDefault();


                    const username =

                        document

                        .getElementById(

                            "username"

                        )

                        .value

                        .trim();


                    const password =

                        document

                        .getElementById(

                            "password"

                        )

                        .value

                        .trim();


                    /*

                    DEFAULT ADMIN LOGIN

                    Username: admin

                    Password: admin123

                    */


                    const adminUsername =

                        "admin";


                    const adminPassword =

                        "admin123";


                    if(

                        username ===

                        adminUsername

                        &&

                        password ===

                        adminPassword

                    ){

                        /*

                        Save Login Session

                        */


                        localStorage.setItem(

                            "isLoggedIn",

                            "true"

                        );


                        localStorage.setItem(

                            "adminName",

                            "Administrator"

                        );


                        /*

                        Go To Main Dashboard

                        */


                        window.location.href =

                            "04_dashboard.html";

                    }

                    else{

                        alert(

                            "Invalid Username or Password!"

                        );


                        passwordInput.value =

                            "";


                        passwordInput.focus();

                    }

                }

            );

        }

    }

);