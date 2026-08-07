/* =========================================================
   GANISHKA TECHNOLOGY + GANISHKA ACADEMY
   STUDENT MANAGEMENT BOARD
   COMMON JAVASCRIPT
========================================================= */


/* =========================================================
   1. COMMON STORAGE KEYS
========================================================= */

const STORAGE_KEYS = {

    isLoggedIn: "isLoggedIn",

    adminName: "adminName",

    gtStudents: "gtStudents",

    gaStudents: "gaStudents", 

    gtAssignments: "gtAssignments",

    gtProjects: "gtProjects",

    gaCoding: "gaCoding",

    gaExams: "gaExams"

};



/* =========================================================
   2. GENERATE UNIQUE ID
========================================================= */

function generateUniqueId(prefix = "ID") {

    return (

        prefix +

        Date.now().toString() +

        Math.random()
            .toString(36)
            .substring(2, 7)

    );

}



/* =========================================================
   3. GET LOCAL STORAGE DATA
========================================================= */

function getStorageData(
    key
) {

    try {

        const data =

            localStorage.getItem(
                key
            );


        if (!data) {

            return [];

        }


        return JSON.parse(
            data
        ) || [];


    } catch (error) {

        console.error(

            "Error reading localStorage:",

            error

        );


        return [];

    }

}



/* =========================================================
   4. SAVE LOCAL STORAGE DATA
========================================================= */

function saveStorageData(

    key,

    data

) {

    try {

        localStorage.setItem(

            key,

            JSON.stringify(
                data
            )

        );


        return true;


    } catch (error) {

        console.error(

            "Error saving localStorage:",

            error

        );


        return false;

    }

}



/* =========================================================
   5. GET GT STUDENTS
========================================================= */

function getGTStudents() {

    return getStorageData(

        STORAGE_KEYS.gtStudents

    );

}



/* =========================================================
   6. GET GA STUDENTS
========================================================= */

function getGAStudents() {

    return getStorageData(

        STORAGE_KEYS.gaStudents

    );

}



/* =========================================================
   7. SAVE GT STUDENTS
========================================================= */

function saveGTStudents(

    students

) {

    return saveStorageData(

        STORAGE_KEYS.gtStudents,

        students

    );

}



/* =========================================================
   8. SAVE GA STUDENTS
========================================================= */

function saveGAStudents(

    students

) {

    return saveStorageData(

        STORAGE_KEYS.gaStudents,

        students

    );

}



/* =========================================================
   9. GENERATE GT STUDENT ID
========================================================= */

function generateGTStudentId() {

    const students =
        getGTStudents();


    let number =

        students.length + 1;


    let id =

        "GT" +

        String(number)
            .padStart(3, "0");


    let exists =

        students.some(

            function(student) {

                return (

                    student.studentId ===
                    id

                );

            }

        );


    while (exists) {

        number++;


        id =

            "GT" +

            String(number)
                .padStart(3, "0");


        exists =

            students.some(

                function(student) {

                    return (

                        student.studentId ===
                        id

                    );

                }

            );

    }


    return id;

}



/* =========================================================
   10. GENERATE GA STUDENT ID
========================================================= */

function generateGAStudentId() {

    const students =
        getGAStudents();


    let number =

        students.length + 1;


    let id =

        "GA" +

        String(number)
            .padStart(3, "0");


    let exists =

        students.some(

            function(student) {

                return (

                    student.studentId ===
                    id

                );

            }

        );


    while (exists) {

        number++;


        id =

            "GA" +

            String(number)
                .padStart(3, "0");


        exists =

            students.some(

                function(student) {

                    return (

                        student.studentId ===
                        id

                    );

                }

            );

    }


    return id;

}



/* =========================================================
   11. GET STUDENT BY ID
========================================================= */

function getStudentByInternalId(

    students,

    internalId

) {

    return students.find(

        function(student) {

            return (

                student._id ===
                internalId

            );

        }

    );

}



/* =========================================================
   12. GET STUDENT BY STUDENT ID
========================================================= */

function getStudentByStudentId(

    students,

    studentId

) {

    return students.find(

        function(student) {

            return (

                student.studentId ===
                studentId

            );

        }

    );

}



/* =========================================================
   13. SEARCH STUDENTS
========================================================= */

function searchStudents(

    students,

    searchTerm

) {

    const search =

        String(
            searchTerm || ""
        )
        .toLowerCase()
        .trim();


    if (!search) {

        return students;

    }


    return students.filter(

        function(student) {


            const searchableText =

                (

                    student.studentId || ""

                )

                +

                " " +

                (

                    student.name || ""

                )

                +

                " " +

                (

                    student.batch || ""

                )

                +

                " " +

                (

                    student.course || ""

                )

                +

                " " +

                (

                    student.duration || ""

                );


            return (

                searchableText
                    .toLowerCase()
                    .includes(search)

            );

        }

    );

}



/* =========================================================
   14. DELETE STUDENT RELATED DATA
========================================================= */

function deleteStudentRelatedData(

    studentInternalId,

    type

) {


    if (

        !studentInternalId

    ) {

        return;

    }



    /* =========================================
       GA RELATED DATA
    ========================================= */


    if (

        type === "GA"

    ) {


        const codingRecords =

            getStorageData(

                STORAGE_KEYS.gaCoding

            );


        const filteredCoding =

            codingRecords.filter(

                function(record) {

                    return (

                        record.studentId !==
                        studentInternalId

                    );

                }

            );


        saveStorageData(

            STORAGE_KEYS.gaCoding,

            filteredCoding

        );



        const examRecords =

            getStorageData(

                STORAGE_KEYS.gaExams

            );


        const filteredExams =

            examRecords.filter(

                function(record) {

                    return (

                        record.studentId !==
                        studentInternalId

                    );

                }

            );


        saveStorageData(

            STORAGE_KEYS.gaExams,

            filteredExams

        );

    }



    /* =========================================
       GT RELATED DATA
    ========================================= */


    if (

        type === "GT"

    ) {


        const assignments =

            getStorageData(

                STORAGE_KEYS.gtAssignments

            );


        const filteredAssignments =

            assignments.filter(

                function(record) {

                    return (

                        record.studentId !==
                        studentInternalId

                    );

                }

            );


        saveStorageData(

            STORAGE_KEYS.gtAssignments,

            filteredAssignments

        );



        const projects =

            getStorageData(

                STORAGE_KEYS.gtProjects

            );


        const filteredProjects =

            projects.filter(

                function(record) {

                    return (

                        record.studentId !==
                        studentInternalId

                    );

                }

            );


        saveStorageData(

            STORAGE_KEYS.gtProjects,

            filteredProjects

        );

    }

}



/* =========================================================
   15. GET GT DASHBOARD COUNTS
========================================================= */

function getGTDashboardCounts() {


    const students =
        getGTStudents();


    const assignments =

        getStorageData(

            STORAGE_KEYS.gtAssignments

        );


    const projects =

        getStorageData(

            STORAGE_KEYS.gtProjects

        );


    return {


        totalStudents:

            students.length,


        assignments:

            assignments.length,


        projects:

            projects.length

    };

}



/* =========================================================
   16. GET GA DASHBOARD COUNTS
========================================================= */

function getGADashboardCounts() {


    const students =
        getGAStudents();


    const coding =

        getStorageData(

            STORAGE_KEYS.gaCoding

        );


    const exams =

        getStorageData(

            STORAGE_KEYS.gaExams

        );


    return {


        totalStudents:

            students.length,


        codingPractice:

            coding.length,


        finalExams:

            exams.length

    };

}



/* =========================================================
   17. CALCULATE PERCENTAGE
========================================================= */

function calculatePercentage(

    obtained,

    total

) {


    obtained =
        Number(obtained);


    total =
        Number(total);


    if (

        !total ||

        total <= 0

    ) {

        return 0;

    }


    return (

        obtained /

        total

    ) * 100;

}



/* =========================================================
   18. GET GRADE
========================================================= */

function getGrade(

    percentage

) {


    percentage =
        Number(percentage);


    if (

        percentage >= 90

    ) {

        return "A+";

    }


    if (

        percentage >= 80

    ) {

        return "A";

    }


    if (

        percentage >= 70

    ) {

        return "B+";

    }


    if (

        percentage >= 60

    ) {

        return "B";

    }


    if (

        percentage >= 50

    ) {

        return "C";

    }


    if (

        percentage >= 40

    ) {

        return "D";

    }


    return "F";

}



/* =========================================================
   19. GET RESULT STATUS
========================================================= */

function getResultStatus(

    percentage

) {


    percentage =
        Number(percentage);


    if (

        percentage >= 40

    ) {

        return "Passed";

    }


    return "Needs Improvement";

}



/* =========================================================
   20. FORMAT PERCENTAGE
========================================================= */

function formatPercentage(

    value

) {


    const number =

        Number(value);


    if (

        isNaN(number)

    ) {

        return "0.00%";

    }


    return (

        number.toFixed(2)

        +

        "%"

    );

}



/* =========================================================
   21. ESCAPE HTML
========================================================= */

function escapeHTML(

    value

) {


    const div =

        document.createElement(
            "div"
        );


    div.textContent =

        value === null ||

        value === undefined

            ?

            ""

            :

            String(value);


    return div.innerHTML;

}



/* =========================================================
   22. LOGOUT
========================================================= */

function commonLogout() {


    localStorage.removeItem(

        STORAGE_KEYS.isLoggedIn

    );


    localStorage.removeItem(

        STORAGE_KEYS.adminName

    );


    window.location.href =

        "01_index.html";

}



/* =========================================================
   23. CHECK LOGIN
========================================================= */

function checkLogin() {


    const loggedIn =

        localStorage.getItem(

            STORAGE_KEYS.isLoggedIn

        );


    if (

        loggedIn !== "true"

    ) {


        window.location.href =

            "01_index.html";


        return false;

    }


    return true;

}



/* =========================================================
   24. GET ADMIN NAME
========================================================= */

function getAdminName() {


    return (

        localStorage.getItem(

            STORAGE_KEYS.adminName

        )

        ||

        "Administrator"

    );

}



/* =========================================================
   25. DISPLAY ADMIN NAME
========================================================= */

function displayAdminName() {


    const adminElements =

        document.querySelectorAll(

            "#gaAdminName, #gtAdminName, #adminName"

        );


    const name =

        getAdminName();


    adminElements.forEach(

        function(element) {

            element.textContent =
                name;

        }

    );

}



/* =========================================================
   26. NAVIGATION HELPER
========================================================= */

function goToPage(

    page

) {


    if (!page) {

        return;

    }


    window.location.href =
        page;

}



/* =========================================================
   27. INITIALIZE COMMON PANEL
========================================================= */

document.addEventListener(

    "DOMContentLoaded",

    function() {


        /* LOGIN CHECK */


        const currentPage =

            window.location.pathname
                .split("/")
                .pop();



        const publicPages = [

            "",

            "01_index.html",

            "02_login.html"

        ];



        if (

            !publicPages.includes(
                currentPage
            )

        ) {


            checkLogin();

        }



        /* ADMIN NAME */


        displayAdminName();



    }

);