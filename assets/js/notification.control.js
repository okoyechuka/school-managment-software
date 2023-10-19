function getElementPositionByClass(el){
    var xPos = 0;
    var yPos = 0;

    while(el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ){


        if(el.tagName.toUpperCase() == "BODY"){
            var xScroll = el.scrollLeft || document.documentElement.scrollLeft;
            var yScroll = el.scrollTop || document.documentElement.scrollTop;

            xPos += (el.offsetLeft - xScroll + el.clientLeft);
            yPos += (el.offsetTop - yScroll + el.clientTop);
        }
        else{

            xPos += (el.offsetLeft - el.scrollLeft + el.clientLeft);
            yPos += (el.offsetTop - el.scrollTop + el.clientTop);

        }

        el = el.offsetParent;
    }

    return {
        x: xPos,
        y: yPos
    };
}

window.addEventListener("scroll", updateElementPostionById('userMail'), false);
window.addEventListener("resize", updateElementPostionById('userMail'), false);
/*THE STRUCRUE OF THE EMVELOP
-<div class="mailNotice"><i.fa fa-envelope><counter>
*/
(function animateNotification(el, noticeClass){

    if(initialX == undefined){
        var initialX = getElementPositionByClass(el).x;
    }

    var boxHolder = (function(noticeClass){
        var box = document.getElementsByClassName(noticeClass)[0];


        return function(response){

           var Messages = JSON.parse(response);
           var Details = Messages.notDetail;

           var animation;

           var elMovement = function(){
              boxParent = box.offsetParent;

              if( box.className.search("xRight") != -1 &&
              (box.getBoundingClientRect().width / boxParent.getBoundingClientRect().width * 100) >= 70){
                  box.className = box.className.replace("xRight", "xLeft");

              }
              else{
                  if((box.getBoundingClientRect().width / boxParent.getBoundingClientRect().width * 100) <= 4){
                    var NextArray = Details.shift();
                    box.innerHTML = `<strong>${NextArray.title}:</strong> ${NextArray.text}`;
                    if(box.className.search("xLeft") != -1) {
                        box.className = box.className.replace("xLeft", "xRight");
                    }
                    else{
                        box.className += " xRight";
                    }
                    Details.push(NextArray);
                  }
              }

           }
           animation = setInterval(elMovement, 5000);

        }
    })(noticeClass);

    var url = window.location.href || window.URL;

    url = url.substr(0, url.toUpperCase().indexOf("SOA") + 4);

    var el = document.getElementsByClassName(el)[0];

    var elParaent = el.offsetParent;

    //var posParent = getElementPositionByClass(elParaent);

    if(el.nextElementSibling.tagName == 'COUNTER'){
        var notCount = el.nextElementSibling.innerHTML;
        if(notCount > 0){

            var data = {notice: 1,
                        userId: el.attributes.userid.value,
                        userRole: el.attributes.userRole.value,
                        class:el.attributes.notClass.value,
                        school_id: el.attributes.schId.value
                      };

            Ajax(
                'get',
                url + "assets/api/notice.php",
                data,
                boxHolder);
        }
    }

})('fa fa-envelope', 'mailNotice');

var notificationAnimation = setInterval(function(){},)

function updateElementPostionById(el){

    //getElementPositionById(el);
}

function Ajax(method, url, dataObj, callback){
    var data = "";
    var count = 0;
    for(var key in dataObj){
        if(count == 0){
            data += key + '=' + dataObj[key];
        }
        else{
            data += '&' + key + '=' + dataObj[key];
        }
        count++;
    }
    var xmlhttp = new XMLHttpRequest();
    var result = null;
    xmlhttp.onreadystatechange = function(){

        if(this.readyState == 4 && this.status == 200){
            callback(this.responseText);
        }
    }
    if(method.toUpperCase === 'POST'){
        xmlhttp.open(method.toUpperCase(), url, true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(data);
    }
    else{
        xmlhttp.open(method.toUpperCase(), `${url}?${data}`, true);
        xmlhttp.send();
    }

    return result;
}
