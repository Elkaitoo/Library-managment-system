function vald(cpr){


let c = new XMLHttpRequest();
c.onload = function(){

        document.getElementById("error").innerHTML =this.responseText;
        if (this.responseText.includes("The CPR is already taken") ){
                document.getElementById("error").style.color = "red";
                document.getElementById("cpr").style.borderBottom = "2px solid red";

              }else if (this.responseText.includes("Valid")) {
                document.getElementById("error").style.color = "green";

      }

}

c.open("get", 'checkVal.php' + "?q="+cpr);

c.send();
}