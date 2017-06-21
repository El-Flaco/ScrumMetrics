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

function setProjectName ()
{
    if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
        xmlHttp.onreadystatechange = getProjectNameHandler;
        xmlHttp.open("GET", "php/getProjectName.php", true);
        xmlHttp.send();
    }
}

function getVelocity()
{
    if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
        xmlHttp.onreadystatechange = setGraphHandler;
        xmlHttp.open("GET", "php/getGraphVelocity.php" , true);
        xmlHttp.send();
    }
}

function getArtifactsByType()
{
    if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
        xmlHttp.onreadystatechange = setGraphHandler;
        xmlHttp.open("GET", "php/getGraphArtifacts.php" , true);
        xmlHttp.send();
    }
}

function getSprintsLeadTime()
{
    if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
        xmlHttp.onreadystatechange = setGraphHandler;
        xmlHttp.open("GET", "php/getGraphSprintsLT.php" , true);
        xmlHttp.send();
    }
}

function getArtifactsLeadTime()
{
    if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
        xmlHttp.onreadystatechange = setGraphHandler;
        xmlHttp.open("GET", "php/getGraphArtifactsLT.php" , true);
        xmlHttp.send();
    }
}

function setGraphHandler()
{
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
        var graphDiv = document.getElementById("graphDiv");
        var response = xmlHttp.responseText;
        if (response != "FALSE") {
            graphDiv.innerHTML = response;
        }
    } else {
        console.log(xmlHttp.statusText);
    }
}

function getProjectNameHandler()
{
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
        var projectName = document.getElementById("aGreeting");
        var response = xmlHttp.responseText;
        if (response != "FALSE") {
            projectName.innerHTML += " " + response;
        } else {
            
        }
    } else {
        console.log(xmlHttp.statusText);
    }
}
