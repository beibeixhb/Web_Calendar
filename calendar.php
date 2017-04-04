<!DOCTYPE html>

<head>
    <title>Calendar</title>

    <style type="text/css">
        body {
            font: normal 12px/20px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
            color: black;
            background-color: lightgrey;
        }
        
        table {
            width: 100%;
        }
        
        th {
            background-color: dimgray;
        }
        
        tr:nth-child(even) {
            border: 1px solid black;
            padding: 15px;
            background-color: lightseagreen;
        }
        
        tr:nth-child(odd) {
            border: 1px solid black;
            padding: 15px;
            background-color: lightseagreen;
        }
        
        td {
            border-right: 1px solid #A2ADBC;
            border-bottom: 1px solid #A2ADBC;
            width: 20px;
            height: 80px;
            text-align: center;
        }
    </style>
</head>

<body>

    <!--login and register part of the page-->
    <div class="loginAndRegister"></div>
    <div class="basic_information"></div>
    <div class="functionality"></div>
    <div class="editbox"></div>
    <div class="supplement"></div>
    <div class="calendar"></div>


    <?php
    //Session cookie is HTTP-Only
    ini_set("session.cookie_httponly", 1);
    //start a session
    session_start();
    ?>

        <script type="text/javascript">
            //define a function to display something related to login event
            function login(event) {
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createTextNode("Login and Registration information: \n"));
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createElement("br"));

                //create a input tag in which contains text, password and button element
                var username = document.createElement("input");
                var password = document.createElement("input");
                var submitBtn = document.createElement("input");
                var registerBtn = document.createElement("input");

                //set some attributes of them
                username.setAttribute("id", "username");
                username.setAttribute("name", "username");
                username.setAttribute("type", "text");

                password.setAttribute("id", "password");
                password.setAttribute("name", "password");
                password.setAttribute("type", "password");

                submitBtn.setAttribute("id", "submit");
                submitBtn.setAttribute("type", "button");
                submitBtn.setAttribute("value", "submit");

                registerBtn.setAttribute("id", "register");
                registerBtn.setAttribute("type", "button");
                registerBtn.setAttribute("value", "register");

                //specification
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createTextNode("if you want to login, provide the information and click submit button, if you want to register, provide the information and click register button."));
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createElement("br"));

                //append the username
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createTextNode("username:"));
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createElement("br"));
                document.getElementsByClassName("loginAndRegister")[0].appendChild(username);
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createElement("br"));

                //append the password
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createTextNode("password:"));
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createElement("br"));
                document.getElementsByClassName("loginAndRegister")[0].appendChild(password);
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createElement("br"));

                //append the login button
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createTextNode("login? click here: "));
                document.getElementsByClassName("loginAndRegister")[0].appendChild(submitBtn);
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createElement("br"));

                //append the register button
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createTextNode("register? click here: "));
                document.getElementsByClassName("loginAndRegister")[0].appendChild(registerBtn);
                document.getElementsByClassName("loginAndRegister")[0].appendChild(document.createElement("br"));

                //add events for login and register
                document.getElementById("submit").addEventListener("click", loginCheck, false);
                document.getElementById("register").addEventListener("click", registerCheck, false);
            }

            //bind the AJAX call to page load
            document.addEventListener("DOMContentLoaded", login, false);

            //loginCheck function
            function loginCheck(event) {
                // Initialize the XMLHttpRequest instance
                var xmlHttp = new XMLHttpRequest();

                //get the data
                var username = document.getElementById("username").value;
                var password = document.getElementById("password").value;

                // Make a URL-encoded string for passing POST data
                var dataString = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);

                xmlHttp.open("POST", "ajax_loginCheck.php", true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                //bind the callback function to load event
                xmlHttp.addEventListener("load", function (event) {
                    // parse the JSON into a JavaScript object
                    var jsonData = JSON.parse(event.target.responseText);
                    // in PHP, this was the "success" key in the associative array; in JavaScript, it's the .success property of jsonData
                    if ((jsonData.success)) {
                        alert("You've been Logged In!");
                        currentUser = jsonData.username;
                        // store the token passed from the server-side
                        token = jsonData.token;
                        //document.write(currentUser);
                        // if a user has logged in, the username and password input box will disappear
                        document.getElementsByClassName("loginAndRegister")[0].style.display = "none";
                        displayBasic();
                        displayFunction();
                        display_ajaxResult();
                        dynamicalDel();
                    } else {
                        alert("You were not logged in.  " + jsonData.message);
                    }
                }, false);
                //send the data
                xmlHttp.send(dataString);
            }

            //registerCheck function
            function registerCheck(event) {
                // Initialize the XMLHttpRequest instance
                var xmlHttp = new XMLHttpRequest();

                //get the data
                var username = document.getElementById("username").value;
                var password = document.getElementById("password").value;

                // Make a URL-encoded string for passing POST data:
                var dataString = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);

                xmlHttp.open("POST", "ajax_registerCheck.php", true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function (event) {
                    // parse the JSON into a JavaScript object
                    var jsonData = JSON.parse(event.target.responseText);
                    // in PHP, this was the "success" key in the associative array; in JavaScript, it's the .success property of jsonData
                    if (jsonData.success) {
                        alert("Congratulation! Registration has been completed!");
                    } else {
                        alert("Registration unsuccessful: " + jsonData.message);
                    }
                }, false);

                //send he data
                xmlHttp.send(dataString);
            }

            //initialize some variables useful for our calendar
            var currentDate = new Date();

            //An integer between 1 and 31 that represents the day-of-the-month. 
            var date = currentDate.getDate();
            //An integer between 0 and 6 representing the day of the week (Sunday is 0, Saturday is 6). 
            var day = currentDate.getDay();
            //because 0 represents January, we add 1 to every month, this obey our common sense
            var month = currentDate.getMonth() + 1;
            //represent the year as a four-digit number
            var year = currentDate.getFullYear();

            var displayedYear = year;
            var displayedMonth = month;

            var tag = true;

            // a variable used to store the current username
            var currentUser = null;
            
            // a variable used to store the token passed from the server side
            var token = null;

            //a function to specify the length of a month
            function lengthOfMonth(month, year) {
                var length_of_month = 0;
                switch (month) {
                case 1:
                    length_of_month = 31;
                    break;
                case 2:
                    length_of_month = num_Of_Days_in_February(year);
                    break;
                case 3:
                    length_of_month = 31;
                    break;
                case 4:
                    length_of_month = 30;
                    break;
                case 5:
                    length_of_month = 31;
                    break;
                case 6:
                    length_of_month = 30;
                    break;
                case 7:
                    length_of_month = 31;
                    break;
                case 8:
                    length_of_month = 31;
                    break;
                case 9:
                    length_of_month = 30;
                    break;
                case 10:
                    length_of_month = 31;
                    break;
                case 11:
                    length_of_month = 30;
                    break;
                case 12:
                    length_of_month = 31;
                    break;
                default:
                    alert("ERROR, not a valid month");
                    break;
                }
                return length_of_month;
            }

            //define a function to return the number of days in february in different years
            function num_Of_Days_in_February(year) {
                if (((year % 4 === 0) && (year % 100 !== 0)) || ((year % 4 === 0) && (year % 100 === 0) && (year % 400 === 0))) {
                    return 29;
                } else {
                    return 28;
                }
            }

            //a function used to initialize the current month
            function calendar_Init(year, month, date, day) {
                //first, we get the first day (day-of-week) of current month
                var first_Day_Of_Week = day - (date % 7) + 1;
                //because we want to use 0-6 to represent sunday to saturday
                if (first_Day_Of_Week == 7) {
                    first_Day_Of_Week = 0;
                }
                if (first_Day_Of_Week < 0) {
                    first_Day_Of_Week += 7;
                }
                //set the day (sunday to saturday) of each date in the month
                var length = lengthOfMonth(month, year);
                // var monthArray = new Array();
                var monthArray = [];
                for (var i = 0; i < length; i++) {
                    //every element of the month array contains a new array, the first member of the new array is the date,
                    //and the second member of the new array is the day of this date
                    monthArray[i] = new Array((i + 1), first_Day_Of_Week);
                    first_Day_Of_Week++;
                    if (first_Day_Of_Week == 7) {
                        first_Day_Of_Week = 0;
                    }
                }

                return monthArray;
            }

            //initialize a variable represents the current month
            var currentMonth = calendar_Init(year, month, date, day);

            //a function used to display the main index of the page
            function index(events, numberOfEvents) {
                // some variables used in this function
                var temp = null;
                var prompt = null;
                var color = null;
                var tableBegin = "<table>";
                var tableEnd = "</table>";
                var day = "<tr><td>Sunday</td><td>Monday</td><td>Tuesday</td><td>Wednesday</td><td>Thursday</td><td>Friday</td><td>Saturday</td></tr>";
                //first line of our calendar, without </table> end tage
                var table = tableBegin + day;
                var counter = 0;
                //a calendar at most has six row
                for (var i = 0; i < 6; i++) {
                    //start a new row
                    table = table + "<tr>";
                    //every row has six column, sunday to satyrday
                    for (var j = 0; j < 7; j++) {
                        if ((i === 0) && (j < currentMonth[0][1])) {
                            table = table + "<td>          </td>";
                        } else {
                            if (counter + 1 < 10 && displayedMonth < 10) {
                                temp = displayedYear + "-0" + displayedMonth + "-0" + (counter + 1);
                            } else if (counter + 1 < 10) {
                                temp = displayedYear + "-" + displayedMonth + "-0" + (counter + 1);
                            } else if (displayedMonth < 10) {
                                temp = displayedYear + "-0" + displayedMonth + "-" + (counter + 1);
                            } else {
                                temp = displayedYear + "-" + displayedMonth + "-" + (counter + 1);
                            }

                            table = table + "<td>" + currentMonth[counter][0];
                            table = table + "<br>";
                            var m = 0;
                            //add the events to a specifuc day
                            while (m < numberOfEvents) {
                                if (events[m].date_of_event == temp) {
                                    if (tag === true) {
                                        table = table + "tag: " + events[m].category;
                                    }
                                    // now
                                    var second = new Date();
                                    // date of the event
                                    var first = new Date((events[m].date_of_event).replace(/-/g, "/"));
                                    var gap = computeDifference(first, second);
                                    gap = gap - 1;
                                    if (first < second) {
                                        prompt = " days before now!";
                                    } else if (first > second) {
                                        prompt = " days after now!";
                                    } else {
                                        prompt = " days TODAY!";
                                    }
                                    if (gap <= 5) {
                                        color = "red";
                                    } else if ((gap > 5) && (gap <= 15)) {
                                        color = "yellow";
                                    } else {
                                        color = "blue";
                                    }
                                    table = table + "<br>" + "time slot: " + events[m].beginTime + " - " + events[m].endTime;
                                    table = table + "<br>" + " content: " + "<stong>" + events[m].content + "</stong>";
                                    table = table + "<br>" + "<font color=" + color + ">" + gap + prompt + "</font>" + "<br>";
                                }
                                m++;
                            }
                            counter++;
                            table = table + "</td>";
                        }
                        if (counter == currentMonth.length) {
                            break;
                        }
                    }
                    table = table + "</tr>";
                    if (counter == currentMonth.length) {
                        break;
                    }
                }
                //</table> added
                table = table + tableEnd;

                //var current = "Today: "+month + "/"+date+"/"+year;
                var current = "Today: " + month + "/" + date + "/" + year + "<br>";
                var displayed_month = "Calendar for: " + displayedYear + "/" + displayedMonth;
                document.getElementsByClassName("calendar")[0].innerHTML = current + displayed_month + table;

            }
            document.addEventListener("DOMContentLoaded", index, false);

            //a function used to display events in the 

            //a function to add some supplements to the calendar
            function addSupplement(event) {
                //a previous button
                var previous = document.createElement("input");
                //a next button
                var next = document.createElement("input");
                // a tag button
                var tag = document.createElement("input");

                //set some attributes
                previous.setAttribute("id", "previous");
                previous.setAttribute("name", "previous");
                previous.setAttribute("type", "button");
                previous.setAttribute("value", "previous");

                next.setAttribute("id", "next");
                next.setAttribute("id", "next");
                next.setAttribute("type", "button");
                next.setAttribute("value", "next");

                tag.setAttribute("id", "tag");
                tag.setAttribute("name", "tag");
                tag.setAttribute("type", "button");
                tag.setAttribute("value", "tag");

                //add to the page
                document.getElementsByClassName("supplement")[0].appendChild(document.createTextNode("  Click to see previous month:  "));
                document.getElementsByClassName("supplement")[0].appendChild(previous);
                document.getElementsByClassName("supplement")[0].appendChild(document.createTextNode("  Click to see next month:  "));
                document.getElementsByClassName("supplement")[0].appendChild(next);
                document.getElementsByClassName("supplement")[0].appendChild(document.createTextNode("  Click to enable/disable the tag"));
                document.getElementsByClassName("supplement")[0].appendChild(tag);

                //add event listener to the correspondent button
                document.getElementById("next").addEventListener("click", nextMonth, false);
                document.getElementById("previous").addEventListener("click", previousMonth, false);
                document.getElementById("tag").addEventListener("click", switchTag, false);


            }

            document.addEventListener("DOMContentLoaded", addSupplement, false);

            // a function to determine next month
            function nextMonth() {
                // first, get the length
                var length = lengthOfMonth(displayedMonth, displayedYear);
                var first_of_next_month = null;
                // the last day's date and day are stored in the currentMonth[length - 1]
                // if the day is sunday, next month's first day is monday
                if (currentMonth[length - 1][1] == 6) {
                    first_of_next_month = 0;
                } else {
                    //if the last day's date is not sunday, next month's first day is the number add 1
                    first_of_next_month = currentMonth[length - 1][1] + 1;
                }

                // set the nuber of next month
                if (displayedMonth == 12) {
                    // into the next year
                    displayedMonth = 1;
                    displayedYear = displayedYear + 1;
                } else {
                    displayedMonth = displayedMonth + 1;
                }

                // get the length of the new month
                var new_length = lengthOfMonth(displayedMonth, displayedYear);
                // currentMonth = new Array();
                currentMonth = [];
                for (var i = 0; i < new_length; i++) {
                    currentMonth[i] = new Array((i + 1), first_of_next_month);
                    first_of_next_month++;
                    if (first_of_next_month == 7) {
                        first_of_next_month = 0;
                    }
                }
                //display
                //display_ajaxResult();
                if (currentUser !== null) {
                    display_ajaxResult();
                } else {
                    index();
                }
            }

            // a function to determine previous month
            function previousMonth() {
                var lastDay = 0;
                var firstDay = currentMonth[0][1];
                // first day of the current month is sunday, the last day of last month is saturday
                if (firstDay === 0) {
                    lastDay = 6;
                } else {
                    lastDay = firstDay - 1;
                }

                // set the previous month
                if (displayedMonth == 1) {
                    displayedMonth = 12;
                    displayedYear = displayedYear - 1;
                } else {
                    displayedMonth = displayedMonth - 1;
                }
                // get the length of the new month
                var length = lengthOfMonth(displayedMonth, displayedYear);
                // currentMonth = new Array();
                currentMonth = [];
                // set date and day of the previous month
                for (var i = length; i >= 1; i--) {
                    currentMonth[i - 1] = new Array(i, lastDay);
                    lastDay--;
                    if (lastDay == -1) {
                        lastDay = 6;
                    }
                }
                //display
                //display_ajaxResult();
                if (currentUser !== null) {
                    display_ajaxResult();
                } else {
                    index();
                }
            }

            // a function to display the result of AJAX query to our main page
            function display_ajaxResult() {
                var dataString = "currentmonth=" + encodeURIComponent(displayedMonth) + "&year=" + encodeURIComponent(displayedYear) + "&token=" + encodeURIComponent(token);
                //initialize a XMLHttpRequest instance
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST", "display_event.php", true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function (event) {
                    var jsonData = JSON.parse(event.target.responseText);
                    if (jsonData.success) {
                        var events = jsonData.data;
                        //var numberOfEvents = jsonData.data.length;
                        if ((events === null)) {
                            index();
                        } else {
                            var numberOfEvents = jsonData.data.length;
                            index(events, numberOfEvents);
                        }
                    } else {
                        alert("ERROR: " + jsonData.message);
                    }
                }, false);
                xmlHttp.send(dataString);
            }

            // a function to display some basic information when a user logged in
            function displayBasic() {
                // create a logout button
                var logoutBtn = document.createElement("input");
                var username = document.createElement("input");

                username.setAttribute("id", "currentUser");
                username.setAttribute("name", "currentUser");
                username.setAttribute("type", "button");
                username.setAttribute("value", currentUser);

                logoutBtn.setAttribute("id", "logoutBtn");
                logoutBtn.setAttribute("name", "logoutBtn");
                logoutBtn.setAttribute("type", "button");
                logoutBtn.setAttribute("value", "logout");

                var prompt = document.createTextNode("You are currently logged in as: ");

                // append them in the basic_information container
                document.getElementsByClassName("basic_information")[0].appendChild(prompt);
                document.getElementsByClassName("basic_information")[0].appendChild(document.createTextNode(currentUser));
                document.getElementsByClassName("basic_information")[0].appendChild(logoutBtn);

                // add an event listener on the logout button
                //document.getElementById("logoutBtn").addEventListener("click", ajax_logout, false);
            }

            // a function to display some elements related to functionality
            function displayFunction() {
                // some html element used to complete the functinality of the calendar
                var date_of_event = document.createElement("input");
                var beginTime = document.createElement("input");
                var endTime = document.createElement("input");
                var content = document.createElement("input");
                var addBtn = document.createElement("input");
                var category = document.createElement("select");
                // a select box to represent the events than can be deleted
                var eventSelector = document.createElement("select");
                var delBtn = document.createElement("input");

                // buttons used to display specific type of events
                var view = document.createElement("input");
                var category2 = document.createElement("select");

                // edit button
                var editBtn = document.createElement("input");

                editBtn.setAttribute("id", "editBtn");
                editBtn.setAttribute("name", "editBtn");
                editBtn.setAttribute("type", "button");
                editBtn.setAttribute("value", "edit");

                date_of_event.setAttribute("id", "date_of_event");
                date_of_event.setAttribute("name", "date_of_event");
                date_of_event.setAttribute("type", "date");

                beginTime.setAttribute("id", "beginTime");
                beginTime.setAttribute("name", "endTime");
                beginTime.setAttribute("type", "time");

                endTime.setAttribute("id", "endTime");
                endTime.setAttribute("name", "endTime");
                endTime.setAttribute("type", "time");

                content.setAttribute("id", "content");
                content.setAttribute("name", "content");
                content.setAttribute("type", "text");

                addBtn.setAttribute("id", "addBtn");
                addBtn.setAttribute("name", "addBtn");
                addBtn.setAttribute("type", "button");
                addBtn.setAttribute("value", "add");

                category.setAttribute("id", "category");
                category.setAttribute("name", "category");

                eventSelector.setAttribute("id", "eventSelector");
                eventSelector.setAttribute("name", "eventSelector");

                delBtn.setAttribute("id", "delBtn");
                delBtn.setAttribute("name", "delBtn");
                delBtn.setAttribute("type", "button");
                delBtn.setAttribute("value", "delete");

                view.setAttribute("id", "view");
                view.setAttribute("name", "view");
                view.setAttribute("type", "button");
                view.setAttribute("value", "view");

                category2.setAttribute("id", "category2");
                category2.setAttribute("name", "category2");

                // add some options to the select
                var opt1 = document.createElement("option");
                opt1.setAttribute("value", "study");
                opt1.appendChild(document.createTextNode("study"));
                category.appendChild(opt1);

                var opt2 = document.createElement("option");
                opt2.setAttribute("value", "play");
                opt2.appendChild(document.createTextNode("play"));
                category.appendChild(opt2);

                var opt3 = document.createElement("option");
                opt3.setAttribute("value", "shopping");
                opt3.appendChild(document.createTextNode("shopping"));
                category.appendChild(opt3);

                var opt4 = document.createElement("option");
                opt4.setAttribute("value", "personal");
                opt4.appendChild(document.createTextNode("personal"));
                category.appendChild(opt4);

                // add category 2
                var opt12 = document.createElement("option");
                opt12.setAttribute("value", "study");
                opt12.appendChild(document.createTextNode("study"));
                category2.appendChild(opt12);

                var opt22 = document.createElement("option");
                opt22.setAttribute("value", "play");
                opt22.appendChild(document.createTextNode("play"));
                category2.appendChild(opt22);

                var opt32 = document.createElement("option");
                opt32.setAttribute("value", "shopping");
                opt32.appendChild(document.createTextNode("shopping"));
                category2.appendChild(opt32);

                var opt42 = document.createElement("option");
                opt42.setAttribute("value", "personal");
                opt42.appendChild(document.createTextNode("personal"));
                category2.appendChild(opt42);

                var opt52 = document.createElement("option");
                opt52.setAttribute("value", "all");
                opt52.appendChild(document.createTextNode("all"));
                category2.appendChild(opt52);



                document.getElementsByClassName("functionality")[0].appendChild(document.createTextNode("input the date of the event: "));
                document.getElementsByClassName("functionality")[0].appendChild(date_of_event);
                document.getElementsByClassName("functionality")[0].appendChild(document.createTextNode("begin time: "));
                document.getElementsByClassName("functionality")[0].appendChild(beginTime);
                document.getElementsByClassName("functionality")[0].appendChild(document.createTextNode("end time: "));
                document.getElementsByClassName("functionality")[0].appendChild(endTime);
                document.getElementsByClassName("functionality")[0].appendChild(document.createTextNode("content of the event: "));
                document.getElementsByClassName("functionality")[0].appendChild(content);
                document.getElementsByClassName("functionality")[0].appendChild(document.createTextNode("select the category: "));
                document.getElementsByClassName("functionality")[0].appendChild(category);
                document.getElementsByClassName("functionality")[0].appendChild(document.createTextNode("click to add: "));
                document.getElementsByClassName("functionality")[0].appendChild(addBtn);
                document.getElementsByClassName("functionality")[0].appendChild(document.createElement("br"));
                document.getElementsByClassName("functionality")[0].appendChild(document.createTextNode("delete events: "));
                document.getElementsByClassName("functionality")[0].appendChild(eventSelector);
                document.getElementsByClassName("functionality")[0].appendChild(delBtn);
                document.getElementsByClassName("functionality")[0].appendChild(document.createTextNode("  edit event: "));
                document.getElementsByClassName("functionality")[0].appendChild(editBtn);
                document.getElementsByClassName("functionality")[0].appendChild(document.createElement("br"));
                document.getElementsByClassName("functionality")[0].appendChild(document.createTextNode("Which type of events do you want to view? "));
                document.getElementsByClassName("functionality")[0].appendChild(category2);
                document.getElementsByClassName("functionality")[0].appendChild(view);


                // add an event listener on add button
                document.getElementById("addBtn").addEventListener("click", addEvent, false);
                // add an event listener on delete button
                document.getElementById("delBtn").addEventListener("click", delEvent, false);
                // add an event listener on edit button
                document.getElementById("editBtn").addEventListener("click", editEventDisplay, false);
                // add event listeners on view button
                document.getElementById("view").addEventListener("click", specialView, false);

            }


            // a function used to add event
            function addEvent() {
                // first, get the value of related DOM element
                var date_of_event = document.getElementById("date_of_event").value;
                var beginTime = document.getElementById("beginTime").value;
                var endTime = document.getElementById("endTime").value;
                var content = document.getElementById("content").value;
                var category = document.getElementById("category").value;
                // Make a URL-encoded string for passing POST data
                var dataString = "date_of_event=" + encodeURIComponent(date_of_event) + "&beginTime=" + encodeURIComponent(beginTime) + "&endTime=" + encodeURIComponent(endTime) + "&content=" + encodeURIComponent(content) + "&category=" + encodeURIComponent(category) + "&token=" + encodeURIComponent(token);

                //initialize a XMLHttpRequest instance
                var xmlHttp = new XMLHttpRequest();
                //open a connection
                xmlHttp.open("POST", "addEvent.php", true);
                // required when using POST method
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                // add event listener
                xmlHttp.addEventListener("load", function (event) {
                    var jsonData = JSON.parse(event.target.responseText);
                    if (jsonData.success) {
                        dynamicalDel();
                        display_ajaxResult();
                    } else {
                        alert("ERROR :" + jsonData.message);
                    }
                });
                // send the data
                xmlHttp.send(dataString);
            }

            // a function used to delete events
            function delEvent() {
                // we use the id of each event as the identifier of the event that we want to delete
                var id = document.getElementById("eventSelector").value;
                // Make a URL-encoded string for passing POST data
                var dataString = "eventId=" + encodeURIComponent(id) + "&token=" + encodeURIComponent(token);

                // initialize a XMLHTTPRequest instance and set some attributes
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST", "deleteEvent.php", true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function (event) {
                    var jsonDara = JSON.parse(event.target.responseText);
                    if (jsonDara.success) {
                        // if delete successfully, update the page without refreshing the page
                        display_ajaxResult();
                        dynamicalDel();
                    } else {
                        alert("ERROR: " + jsonDara.message);
                    }
                }, false);
                xmlHttp.send(dataString);
            }

            // a function used to dynamically display the events that can be deleted in the dropdown box
            function dynamicalDel() {
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST", "dinamically_Display_Events.php", true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function (event) {
                    var jsonData = JSON.parse(event.target.responseText);
                    if (jsonData.success) {
                        // accept the value of json response and display them in the delete dropdown box
                        var events = jsonData.data;
                        var numberOfEvents = jsonData.data.length;
                        // iterate the results from the server, and add them into the dropdown box
                        // before doing this, we need to clear the event selector
                        document.getElementById("eventSelector").options.length = 0;
                        for (var i = 0; i < numberOfEvents; i++) {
                            var currentEvent = document.createElement("option");
                            // set the value of each option to the id of the event
                            currentEvent.setAttribute("value", events[i].id);
                            currentEvent.appendChild(document.createTextNode("date: " + events[i].date_of_event + "  content: " + events[i].content));
                            // append each event into the eventSelector dropdown box
                            document.getElementById("eventSelector").appendChild(currentEvent);
                        }
                    } else {
                        alert("ERROR: " + jsonData.message);
                    }
                }, false);
                // to make this method valid, we need send a data through xmlHttp instance, but in the php page
                // which handle this connection, we don't use this dataString
                var dataString = "token=" + encodeURIComponent(token);
                xmlHttp.send(dataString);
            }

            // a function used to enable/disable the tag
            function switchTag() {
                if (tag) {
                    tag = false;
                } else {
                    tag = true;
                }
                // after changing the tag, call the display function
                display_ajaxResult();
            }

            // a function used to compute differences between two days
            function computeDifference(firstDate, secondDate) {
                // hours*minutes*seconds*milliseconds
                var oneDay = 24 * 60 * 60 * 1000;
                // use getTime() function, it returns number of milliseconds since midnight of January 1 1970
                var timeDiff = Math.abs(firstDate.getTime() - secondDate.getTime());
                var diffDays = Math.ceil(timeDiff / oneDay);
                // return diffDays, round to integer
                return diffDays;
            }

            // a function to display specific type of events
            function specialView() {
                var type_of_event = document.getElementById("category2").value;
                // Make a URL-encoded string for passing POST data
                var dataString = "type=" + encodeURIComponent(type_of_event) + "&token=" + encodeURIComponent(token);
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST", "specialView.php", true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function (event) {
                    var jsonData = JSON.parse(event.target.responseText);
                    if (jsonData.success) {
                        var events = jsonData.data;
                        var numberOfEvents = jsonData.data.length;
                        if ((events === null) || (numberOfEvents === null)) {
                            index();
                        } else {
                            index(events, numberOfEvents);
                        }
                    } else {
                        alert("ERROR  " + jsonData.message);
                    }
                }, false);
                xmlHttp.send(dataString);
            }

            // a function used to display elements for editting events
            function editEventDisplay() {
                // get the event ID to be edited
                // var eventId = document.getElementById("eventSelector").value;
                // var dataString = "eventId=" + encodeURIComponent(eventId);

                // display the box for edtting an event
                var editDate = document.createElement("input");
                var editBeginTime = document.createElement("input");
                var editEndTime = document.createElement("input");
                var editContent = document.createElement("input");
                var editType = document.createElement("select");
                var ok = document.createElement("input");

                ok.setAttribute("id", "editEventBtn");
                ok.setAttribute("name", "editEventBtn");
                ok.setAttribute("type", "button");
                ok.setAttribute("value", "OK");

                editDate.setAttribute("id", "editDate");
                editDate.setAttribute("name", "editDate");
                editDate.setAttribute("type", "date");

                editBeginTime.setAttribute("id", "editBeginTime");
                editBeginTime.setAttribute("name", "editBeginTime");
                editBeginTime.setAttribute("type", "time");

                editEndTime.setAttribute("id", "editEndTime");
                editEndTime.setAttribute("name", "editEndTime");
                editEndTime.setAttribute("type", "time");

                editContent.setAttribute("id", "editContent");
                editContent.setAttribute("name", "editContent");
                editContent.setAttribute("type", "text");

                editType.setAttribute("id", "editType");
                editType.setAttribute("name", "editType");

                // add some options to the select
                var editOpt1 = document.createElement("option");
                editOpt1.setAttribute("value", "study");
                editOpt1.appendChild(document.createTextNode("study"));
                editType.appendChild(editOpt1);

                var editOpt2 = document.createElement("option");
                editOpt2.setAttribute("value", "play");
                editOpt2.appendChild(document.createTextNode("play"));
                editType.appendChild(editOpt2);

                var editOpt3 = document.createElement("option");
                editOpt3.setAttribute("value", "shopping");
                editOpt3.appendChild(document.createTextNode("shopping"));
                editType.appendChild(editOpt3);

                var editOpt4 = document.createElement("option");
                editOpt4.setAttribute("value", "personal");
                editOpt4.appendChild(document.createTextNode("personal"));
                editType.appendChild(editOpt4);

                // append all the above elements to the editbox container
                document.getElementsByClassName("editbox")[0].appendChild(document.createTextNode("new date: "));
                document.getElementsByClassName("editbox")[0].appendChild(editDate);
                document.getElementsByClassName("editbox")[0].appendChild(document.createTextNode(" new begin time: "));
                document.getElementsByClassName("editbox")[0].appendChild(editBeginTime);
                document.getElementsByClassName("editbox")[0].appendChild(document.createTextNode(" new end time: "));
                document.getElementsByClassName("editbox")[0].appendChild(editEndTime);
                document.getElementsByClassName("editbox")[0].appendChild(document.createTextNode(" new content: "));
                document.getElementsByClassName("editbox")[0].appendChild(editContent);
                document.getElementsByClassName("editbox")[0].appendChild(document.createTextNode(" new type: "));
                document.getElementsByClassName("editbox")[0].appendChild(editType);
                document.getElementsByClassName("editbox")[0].appendChild(ok);

                // add an event on the edit button
                document.getElementById("editEventBtn").addEventListener("click", editEventFun, false);
            }

            // a function used to edit event
            function editEventFun() {
                // get the new value
                var id = document.getElementById("eventSelector").value;
                var newDate = document.getElementById("editDate").value;
                var newBeginTime = document.getElementById("editBeginTime").value;
                var newEndTime = document.getElementById("editEndTime").value;
                var newContent = document.getElementById("editContent").value;
                var newType = document.getElementById("editType").value;
                // Make a URL-encoded string for passing POST data
                var dataString = "eventId=" + encodeURIComponent(id) + "&newDate=" + encodeURIComponent(newDate) + "&newBeginTime=" + encodeURIComponent(newBeginTime) + "&newEndTime=" + encodeURIComponent(newEndTime) + "&newContent=" + encodeURIComponent(newContent) + "&newType=" + encodeURIComponent(newType) + "&token=" + encodeURIComponent(token);
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST", "editEvent.php", true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function (event) {
                    var jsonData = JSON.parse(event.target.responseText);
                    if (jsonData.success) {
                        dynamicalDel();
                        display_ajaxResult();
                        document.getElementsByClassName("editbox")[0].style.display = "none";
                    } else {
                        alert("ERROR: " + jsonData.message);
                    }
                }, false);
                xmlHttp.send(dataString);
            }

        </script>


</body>

</html>