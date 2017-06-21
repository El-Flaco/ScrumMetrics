// Create xmlHTTP Request
var xmlHttp = createXmlHttpRequestObject();

function createXmlHttpRequestObject()
{
    var xmlHttp;
    
    if (window.ActiveXObject) {
        try {
            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {
            xmlHttp = false;
        }
    } else {
        try {
            xmlHttp = new XMLHttpRequest();
        } catch (e) {
            xmlHttp = false;
        }
    }
    
    if (!xmlHttp) {
        alert("Error creating XMLHttpRequest object.");
    } else {
        return xmlHttp;
    }
}

function checkUser()
{
    if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
        xmlHttp.onreadystatechange = handleCheckUserResponse;
        xmlHttp.open("GET", "php/loggedUser.php", true);
        xmlHttp.send();
    }
}

function getProjectsList()
{
    if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
        xmlHttp.onreadystatechange = getProjectsHandler;
        xmlHttp.open("GET", "php/getProjectsList.php", true);
        xmlHttp.send();
    }
}

function handleCheckUserResponse()
{
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
        if (xmlHttp.responseText != "FALSE") {
            document.getElementById("aGreeting").innerHTML += " - " + xmlHttp.responseText;
            getProjectsList();
        } else {
            window.location = "login.html";
        }
    } else {
        console.log(xmlHttp.statusText);
    }
}

function getProjectsHandler()
{
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
        projectListUl = document.getElementById("projectListUl");
        var responseJson = JSON.parse(xmlHttp.responseText);
        for (index in responseJson) {
            projectListUl.innerHTML += '<li>Project: <button class="buttonProject" onclick="selectProject(' + responseJson[index].id +')">' + responseJson[index].id + " - " + responseJson[index].label + "</button></li>";
        }
        
    } else {
        console.log(xmlHttp.statusText);
    }
}

function selectProject(projectID)
{
    if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
        xmlHttp.onreadystatechange = goProjectHandler;
        xmlHttp.open("GET", "php/goProject.php?projectId="+projectID, true);
        xmlHttp.send();
    }
    
}

function goProjectHandler()
{
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
        if (xmlHttp.responseText != "FALSE") {
            window.location = "project.html";
        }
    } else {
        console.log(xmlHttp.statusText);
    }
}
