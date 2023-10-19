/* Fixes HTML5 in IE */
(function(){
    var html5elements = "address|article|aside|audio|canvas|command|datalist|details|dialog|figure|figcaption|footer|header|hgroup|keygen|mark|meter|menu|nav|progress|ruby|section|time|video".split('|');
         for(var i = 0; i < html5elements.length; i++){
  			document.createElement(html5elements[i]);
  		}
  	}
 )();