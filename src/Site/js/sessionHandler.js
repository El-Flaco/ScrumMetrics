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

function logOut()
{
    if (confirm("Do you want to log out?")) {
        xmlHttp.onreadystatechange = handleLogOutResponse;
        xmlHttp.open("GET", "php/logout.php", true);
        xmlHttp.send();
    }
}

function handleLogOutResponse()
{
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
        window.location = "login.html";
    } else {
        console.log(xmlHttp.statusText);
    }
}
