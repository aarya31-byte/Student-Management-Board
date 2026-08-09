/*==================================================
        STUDENT MANAGEMENT BOARD
              LOGIN JAVASCRIPT
==================================================*/


/*==================================================
        API BASE URL

        Hardcoded for now — update this (and the
        copy in 17_script.js) once the backend has
        a real deployed address. See
        backend_details.md section 7.
==================================================*/

const API_BASE_URL =

    "http://localhost:8000";


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

                async function(event){

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

                    LOGIN AGAINST REAL BACKEND

                    POST {API_BASE_URL}/api/auth/login

                    Body: {username, password}

                    Success: 200 {token, adminName}

                    Failure: non-2xx {detail}

                    */


                    try{

                        const response =

                            await fetch(

                                API_BASE_URL +

                                "/api/auth/login",

                                {

                                    method:
                                        "POST",

                                    headers: {

                                        "Content-Type":
                                            "application/json"

                                    },

                                    body:
                                        JSON.stringify({

                                            username:
                                                username,

                                            password:
                                                password

                                        })

                                }

                            );


                        const data =

                            await response

                            .json()

                            .catch(function(){

                                return {};

                            });


                        if(!response.ok){

                            alert(

                                data.detail ||

                                "Invalid username or password!"

                            );


                            passwordInput.value =

                                "";


                            passwordInput.focus();


                            return;

                        }


                        /*

                        Save Login Session

                        */


                        localStorage.setItem(

                            "authToken",

                            data.token

                        );


                        localStorage.setItem(

                            "adminName",

                            data.adminName

                        );


                        /*

                        Go To Main Dashboard

                        */


                        window.location.href =

                            "04_dashboard.html";

                    }

                    catch(error){

                        console.error(

                            "Login request failed:",

                            error

                        );


                        alert(

                            "Unable to reach the server. Please try again."

                        );

                    }

                }

            );

        }

    }

);