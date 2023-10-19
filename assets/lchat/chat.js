// JavaScript Document
function chat_setCookie(name,value,days) {
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days*24*60*60*1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function chat_getCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
}

function chat_playSound() {
    var sound = document.getElementById("audio2"); sound.play();
}
function sendChat() {
	var text = $('#status_message').val();
	var myAvaraID = chat_getCookie('myAvaraID');
	var myRoom = chat_getCookie('CHAT-ROOM');
	$.get(chat_getCookie('BASE-URL')+'?send_chat='+myAvaraID+'&room='+myRoom+'&text='+text, function(data3){  
		if(data3 !== "empty") {
			$('#status_message').val("");
			var seconds = new Date().getTime() / 1000; chat_setCookie('LastChatSee',seconds);
		} else {
			alert('Unable to send chat. Check your connection and try again');
		}
	});
	return false;
}