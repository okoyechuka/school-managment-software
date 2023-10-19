<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
# ---------------------------------------------------------------------
#  Add all footer scripts here
#  You are not permitted to modify the first 12 lines. Please keep off
#----------------------------------------------------------------------
global $URL; global $userID; global $LANG; global $server; $school_id = @$_SESSION['school_id']; global $school_id;?>
    <!-- end -->
	<script type="text/javascript" src="assets/js/select2.js"></script>
    <script type="text/javascript" src="assets/js/select2.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.fancybox.js?v=2.1.5"></script>
    
    <script type='text/javascript' src='assets/js/jquery.validationEngine-en.js'></script>
    <script type='text/javascript' src='assets/js/jquery.validationEngine.js'></script>
    <script type='text/javascript' src='assets/js/jquery.ui.core.js'></script>
    <script type='text/javascript' src='assets/js/jquery.ui.widget.js'></script>
    <script type="text/javascript" src="assets/js/datepicker.js"></script>
    <!-- ceejay @for js notification control
    <script type="text/javascript" src="assets/js/notification.control.js"></script> -->
    <!-- Chat JS Files ------------------------------------------------------>
    <script type="text/javascript" src="assets/js/Chart.js"></script>
    <script type="text/javascript" src="assets/js/Chart.min.js"></script>
    
    <script src="assets/js/froala_editor.min.js"></script>
    <script type="text/javascript" src="assets/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="assets/table/jquery.tablesorter.js"></script>
    <script src="assets/plugins/featherlight/src/featherlight.js" type="text/javascript" charset="utf-8"></script>
        <?php if(getUser()>0) { ?>
        	<script src="assets/lchat/chat.js?<?=time()?>"></script>  
            <script>
			$(document).ready(function() {
			<?php if(userRole(getUser())<4) { ?>
				chat_setCookie('myAvaraID','0');	
			<?php } else { ?>	
				chat_setCookie('myAvaraID','<?=getUser()?>');	
			<?php } ?>
			<?php if(userRole(getUser())<5) { ?>
				<?php if(userRole($userID) < 3) { ?>
					chat_setCookie('CHANNEL_ID','0',1);	
					chat_setCookie('CHAT-ROOM',"0");	
				<?php } else { ?>	
					chat_setCookie('CHANNEL_ID','<?=getTeacherClass(userProfile(getUser()))?>',1);	
					chat_setCookie('CHAT-ROOM',"<?=getTeacherClass(userProfile(getUser()))?>");	
				<?php } ?>
				chat_setCookie('BASE-URL',"admin/API");
			<?php } else { ?>	
			    chat_setCookie('BASE-URL',"API");
				chat_setCookie('CHANNEL_ID','<?=getClass(userProfile(getUser()),getSetting('current_session'))?>',1);	
				chat_setCookie('CHAT-ROOM','<?=getClass(userProfile(getUser()),getSetting('current_session'))?>');	
			<?php } ?>
				chat_setCookie('LastChatSee2',0);
				});
			$(document).ready(function() {
					$("#addClass").click(function () {
					  $('#qnimate').addClass('popup-box-on');$('#nc').hide();$('#nc').text(0);
						if(chat_getCookie('CHANNEL_ID') != '0') {
							  var doStuffs = setInterval(function() {	$.ajax({url: chat_getCookie('BASE-URL')+"?LaunchChats="+chat_getCookie('CHANNEL_ID')+"&last="+chat_getCookie('LastChatSee2'), success: function(data){
									var newList = "";
									var chas = JSON.parse(data);
									var counts = 0;
									var mes = chat_getCookie('myAvaraID');
									for(var i = 0; i < chas.length; i++){
										var counts = counts+1;
										var member = chas[i]['member_id'];
										var message = chas[i]['message'];
										var name = chas[i]['member_name'];
										var agos = chas[i]['agos'];
										var lastTime = chas[i]['lasttime'];
										if(member===mes) {
											var name = 'Me';
											var newList = newList + '<div class="direct-chat-msg"><div class="indirect-chat-text"><span class="direct-chat-name">'+name+'</span><br>'+message+'<p class="direct-chat-timestamp">'+agos+'</p></div></div>  ';
										} else {
											var newList = newList + '<div class="direct-chat-msg"><div class="direct-chat-text"><span class="direct-chat-name">'+name+'</span><br>'+message+'<p class="direct-chat-timestamp">'+agos+'</p></div></div>  ';
										}
									}
									if(counts >0) {
										var currentHTML = $('#loadHistory').html();
										$('#loadHistory').html(currentHTML+newList);
										$('#cthk').scrollTop($('#cthk')[0].scrollHeight);
										if($('#qnimate').is(":visible")) {
											$('#nc').hide();$('#nc').text(0);
										} else {
											if(chat_getCookie('LastChatSee2')>0) {	chat_playSound();}
											clearInterval(doStuffs);
										}
										chat_setCookie('LastChatSee2',lastTime);
										 var seconds = new Date().getTime() / 1000; chat_setCookie('LastChatSee',seconds);
									}
								}});
							}, 2100);
						} else {
							$('#jiners').show();
						}
					});
					$("#removeClass").click(function () {
						  $('#qnimate').removeClass('popup-box-on');$('#nc').text(0);
						  var seconds = new Date().getTime() / 1000; chat_setCookie('LastChatSee',seconds);
					});
					setInterval(function() {	$.ajax({url: chat_getCookie('BASE-URL')+"?count_new_messchat="+chat_getCookie('LastChatSee')+"&church="+chat_getCookie('CHANNEL_ID'), success: function(data){
							var uid = parseInt(data);
							var nCv = parseInt($('#nc').text());
							if(Number.isInteger(uid)) { 
								if(uid<1) {
									$('#nc').text(0);$('#nc').hide();
								}
								if(uid > 0&&uid !== nCv) {
									if($('#qnimate').is(":visible")) {
										//no need
									} else {
										$('#nc').text(uid);$('#nc').show();
										chat_playSound();
									}
								} 
							}
						}});
					}, 4500);
				})

			</script> 
              
        <?php } ?>  

<script>
	$(document).ready(function(){$("#Firm").validationEngine();
	$( "#datePickercurr_date" ).datepicker({
		 dateFormat:"yy-mm-dd",
		 changeMonth: true,changeYear: true,
		// yearRange:"<?php echo date('Y'); ?>:+10",
		 minDate: 0} )}
	);

  $(document).ready(function() {
	  $("select").select2();
   });

	function isNumberKey(evt) {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31
            && (charCode < 48 || charCode > 57))
             return false;
          return true;
    }
	function togleShowMenu(menu) {
		jQuery('.resellerMenuLists').hide();
		jQuery('#'+menu).toggle('show');
	}

$(document).ready(function()   {
        $('table').tablesorter();
 });
$(function(){
	$('a').click(function(){
	   $('<div class="loadingDiv"><i class="fa fa-spinner fa-spin"></i><br>Please wait...</div>').prependTo(document.getElementById('main-body'));
	   setTimeout(function() { $(".loadingDiv").fadeOut(1500); }, 15000);
	});
});
$(function(){
	$('#close-message').click(function(){
	   setTimeout(function() { $(".loadingDiv").fadeOut(1500); }, 1500);
	});
});
$(function(){
	$('#closeBox').click(function(){
	   setTimeout(function() { $(".loadingDiv").fadeOut(1500); }, 1500);
	});
});
$(function(){
	$('.fancybox').click(function(){
	   setTimeout(function() { $(".loadingDiv").fadeOut(1500); }, 1500);
	});
});

	function printDiv(divID) {
		var divElements = document.getElementById(divID).innerHTML;
		var oldPage = document.body.innerHTML;
		document.body.innerHTML = '<html><head><title>Report</title></head><body style="background-image:none;">' + divElements + "</body></html>";
		window.print();
		document.body.innerHTML = oldPage;
	}
</script>

<script>
<?php if($URL=='useronlinetest') { ?>
function startTest(cbtid) {
	if(confirm("Are you sure you want to start this online textx now?")) {
		$.ajax({url: "API?start_test="+1+'&cbt_id='+cbtid+'&user_id='+<?=getUser()?>, success: function(returndata){
			var tiings = parseInt(returndata.trim());
			if(Number.isInteger(tiings)) {
				$('#starter').hide('slow');
				var countDownDate = tiings+<?=TIMER?>;
				var availba = <?=TIMER?>;
				var x = setInterval(function() {
					var distance = (1000 * availba); // countDownDate - now;
					var days = Math.floor(distance / (1000 * 60 * 60 * 24));
					var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
					var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
					var seconds = Math.floor((distance % (1000 * 60)) / 1000);
					document.getElementById("cbttime").innerHTML = hours + ":" + minutes + ":" + seconds + "";				
					availba--;
					if (distance < 0) {
						clearInterval(x);
						endTest(cbtid);
					}
				}, 1000);
			} else {
				swal("Oops", tiings, "error");
			}
	   },error: function(){
		   swal("Aw!", 'Unable to complete action. Please check your connection and try again', "warning");
		}});
	}
}
<?php if(defined('STARTED')){?>
	$(document).ready(function(){
	  var countDownDate = <?=time()+TIMER?>;
  	  var availba = <?=TIMER?>;
	  var x = setInterval(function() {
		var distance = (1000 * availba); // countDownDate - now;
	  	var days = Math.floor(distance / (1000 * 60 * 60 * 24));
	  	var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	  	var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	  	var seconds = Math.floor((distance % (1000 * 60)) / 1000);
	  	document.getElementById("cbttime").innerHTML = hours + ":" + minutes + ":" + seconds + "";				
		availba--;
	  	if (distance < 0) {
			clearInterval(x);
			endTest(<?=STARTED?>);
	  	} 
	  }, 1000);
	})
<?php } ?>
function finishTest(cbtid) {
	if(confirm("Are you sure you want to finish this online textx now?\nYou may not be able to return to this test once finished")) {
		$.ajax({url: "API?finish_test="+1+'&cbt_id='+cbtid+'&user_id='+<?=getUser()?>, success: function(returndata){
			if(returndata.trim()==="OK") {
				swal({   
					title: "Congratulations",   
					text: "Your online test has been completed. You can view your test result at anytime from the Online Test page",   
					type: "success",   
					confirmButtonColor: "#086",   
					confirmButtonText: "Got it!", 
					closeOnConfirm: false 
					}, function(){  
						window.location = 'usercbt'; 
					});
			} else {
				swal("Oops", returndata, "error");
			}
	   },error: function(){
		   swal("Aw!", 'Unable to complete action. Please check your connection and try again', "warning");
		}});
	}
}
function endTest(cbtid) {
	$.ajax({url: "API?end_test="+1+'&cbt_id='+cbtid+'&user_id='+<?=getUser()?>, success: function(returndata){
		swal({   
			title: "Attention",   
			text: "You have exhausted the allocated time for this test. The test will now close",   
			type: "warning",   
			confirmButtonColor: "#086",   
			confirmButtonText: "Got it!", 
			closeOnConfirm: false 
			}, function(){  
				window.location = 'usercbt'; 
			});
	  },error: function(){
	   swal({   
			title: "Attention",   
			text: "You have exhausted the allocated time for this test. The test will now close",   
			type: "warning",   
			confirmButtonColor: "#086",   
			confirmButtonText: "Got it!", 
			closeOnConfirm: false 
			}, function(){  
				window.location = 'usercbt'; 
			});
	}});
}

function submitAnswer(cbtid,question,answer) {
	$.ajax({url: "API?submit_answer="+1+'&question_id='+question+'&answer_id='+answer+'&cbt_id='+cbtid+'&user_id='+<?=getUser()?>, success: function(returndata){
		if(returndata.trim()==="OK") {
			//all good
		} else {
			swal("Oops", returndata, "error");
		}
   },error: function(){
	   swal("Aw!", 'Unable to complete action. Please check your connection try again', "warning");
	}});
}
<?php } ?>
<?php if(userRole($userID) == 5 || userRole($userID) == 6) { ?>
$(document).ready(function(){
		var session_id = $('#e1').val();
			$.ajax({
				type:'get',
				url:'index.php?url=API',
				data:{id:session_id,usrex:1},
				cache:false,
				success: function(returndata){
					$('#e2').html(returndata);
				}
			});

	$('#e1').change(function(){
		var session_id = $('#e1').val();
			$.ajax({
				type:'get',
				url:'index.php?url=API',
				data:{id:session_id,usrex:1},
				cache:false,
				success: function(returndata){
					$('#e2').html(returndata);
				}
			});
	})

})
<?php } else { ?>
$(document).ready(function(){
if($("#report").length != 0) {	
	var report = $('#report').val();
	if(report != 3)
	{
		document.getElementById('stuspan').style.display='none';
		document.getElementById('subspan').style.display='inline';
	} else {
		document.getElementById('stuspan').style.display='inline';
		document.getElementById('subspan').style.display='none';
	}
}

	$('#report').change(function(){
		$('<div class="loadingDiv"><i class="fa fa-spinner fa-spin"></i><br>Please wait...</div>').prependTo(document.getElementById('main-body')); setTimeout(function() { $(".loadingDiv").fadeOut(1500); }, 1500);
		var report = $('#report').val();
		if(report != 3)
		{
		document.getElementById('stuspan').style.display='none';
		document.getElementById('subspan').style.display='inline';
		} else {
		document.getElementById('stuspan').style.display='inline';
		document.getElementById('subspan').style.display='none';
		}
	})

	$('#session_se').change(function(){
		$('<div class="loadingDiv"><i class="fa fa-spinner fa-spin"></i><br>Please wait...</div>').prependTo(document.getElementById('main-body')); setTimeout(function() { $(".loadingDiv").fadeOut(1500); }, 3000);
		var session_id = $('#session_se').val(); 
			$.ajax({
				type:'get',
				url:'admin/API',
				data:{id:session_id,sese:1},
				cache:false,
				success: function(returndata){
					$('#exam_sel').html(returndata);
				}
			});
	})

	$('#class_sel').change(function(){
		$('<div class="loadingDiv"><i class="fa fa-spinner fa-spin"></i><br>Please wait...</div>').prependTo(document.getElementById('main-body')); setTimeout(function() { $(".loadingDiv").fadeOut(1500); }, 3000);
		var class_id = $('#class_sel').val();
		var report = $('#report').val();
		if(report != 3) {
			$.ajax({
				type:'post',
				url:'admin/API',
				data:{id:class_id,clasus:1},
				cache:false,
				success: function(returndata){
					$('#sel_sub').html(returndata);
				}
			});
		} else {
			$.ajax({
				type:'post',
				url:'admin/API',
				data:{id:class_id,classtu:1},
				cache:false,
				success: function(returndata){
					$('#sel_stu').html(returndata);
				}
			});
		}
	})
})
<?php } ?>

$("#e2feetpay").change(function(){
	var val = $('option:selected', this).attr('data-amount');
    $('#total').val(val);
});
var val = $("#num_assignment").val();
if(val==1) {
	$('.ass_2').hide();		$('.ass_3').hide();	$('.ass_4').hide();	$('.ass_5').hide();	
} else {
	$('.ass_2').show();		$('.ass_3').show();		$('.ass_4').show();	$('.ass_5').show();	
	if(val==2) {
		$('.ass_3').hide();			$('.ass_4').hide();		$('.ass_5').hide();	
	} else {
		$('.ass_3').show();			$('.ass_4').show();		$('.ass_5').show();	
		if(val==3) {
			$('.ass_4').hide();			$('.ass_5').hide();	
	} else {
			$('.ass_4').show();			$('.ass_5').show();	
			if(val==4) {
				$('.ass_5').hide();	
			} else {
				$('.ass_5').show();	
			}
		}
	}
}
$("#num_assignment").change(function(){
	var val = $("#num_assignment").val();
	if(val==1) {
		$('.ass_2').hide();		$('.ass_3').hide();	$('.ass_4').hide();	$('.ass_5').hide();	
	} else {
		$('.ass_2').show();		$('.ass_3').show();		$('.ass_4').show();	$('.ass_5').show();	
		if(val==2) {
			$('.ass_3').hide();			$('.ass_4').hide();		$('.ass_5').hide();	
		} else {
			$('.ass_3').show();			$('.ass_4').show();		$('.ass_5').show();	
			if(val==3) {
				$('.ass_4').hide();			$('.ass_5').hide();	
			} else {
				$('.ass_4').show();			$('.ass_5').show();	
				if(val==4) {
					$('.ass_5').hide();	
				} else {
					$('.ass_5').show();	
				}
			}
		}
	}
});

<?php if(userRole($userID)<3) {?>
//load gateway settings
		$('#gtype').change(function(){
			$('#param1').val('');
			$('#param2').val('');
			$('#param3').val('');
			$('#show_p1').hide();
			$('#show_p2').hide();
			$('#show_p3').hide();
			var aka = $('#gtype').val();
			if(aka.length > 1) { 
				$.ajax({url: "admin/API?load_pay_gateway_temp="+aka, success: function(result){
					var param1 = result.split(':')[0];					
					var param2 = result.split(':')[1]
					var param3 = result.split(':')[2]
					if(param1.length > 1) {
						$('#label_p1').text(param1);
						$('#show_p1').show();
					} else {
						$('#param1').val('');
						$('#show_p1').hide();
					}
					if(param2.length > 1) {
						$('#label_p2').text(param2);
						$('#show_p2').show();
					} else {
						$('#param2').val('');
						$('#show_p2').hide();
					}
					if(param3.length > 1) {
						$('#label_p3').text(param3);
						$('#show_p3').show();
					} else {
						$('#param3').val('');
						$('#show_p3').hide();
					}
					if(aka=='custom') {
						$('#show_p1').hide();
						$('#show_p2').hide();
						$('#show_p3').hide();
					}
				}});
			}
		});
<?php } ?>		
</script>

<?php if(defined('calender')): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="assets/js/fullcalendar.min.js"></script>
<!-- Page specific script -->
<script>
  $(function () {
    /* initialize the external events
     -----------------------------------------------------------------*/
    function ini_events(ele) {
      ele.each(function () {
        // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
        // it doesn't need to have a start or end
        var eventObject = {
          title: $.trim($(this).text()) // use the element's text as the event title
        };

        // store the Event Object in the DOM element so we can get to it later
        $(this).data('eventObject', eventObject);

        // make the event draggable using jQuery UI
        $(this).draggable({
          zIndex: 1070,
          revert: true, // will cause the event to go back to its
          revertDuration: 0  //  original position after the drag
        });

      });
    }

    ini_events($('#external-events div.external-event'));

    /* initialize the calendar
     -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date();
    var d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear();
    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      buttonText: {
        today: 'today',
        month: 'month',
        week: 'week',
        day: 'day'
      },
      //Random default events
      events: [
        {
          title: 'General',
          start: new Date(1970, 1, 1),
          backgroundColor: "#f56954", //red
          borderColor: "#f56954" //red
        },
	//pu; events from datanase
<?php
	$date = date('Y-m-d');
	$today20 = increaseDate($date, '20');
	$today10 = reduceDate($date, '10');
	 $query = "SELECT * FROM schedules WHERE school_id = '$school_id' ORDER BY date DESC";
	$result = mysqli_query($server,$query) or die(mysqli_error($server));
	while ($row = mysqli_fetch_assoc($result)) {
		$day = date('j', strtotime($row['date']));
		$month = date('n', strtotime($row['date']))-1;
		$year = date('Y', strtotime($row['date']));
		$event = str_replace("'",' ',$row['schedule']);
		if(strtotime($row['date']) < strtotime(date('Y-m-d'))) $color = '#999999';
		if(strtotime($row['date']) == strtotime(date('Y-m-d'))) $color = '#66FF66';
		if(strtotime($row['date']) > strtotime(date('Y-m-d'))) $color = '#99CCFF';
?>
        {
          title: '<?=$event;?>',
          start: new Date(<?=$year;?>, <?=$month;?>, <?=$day;?>),
          allDay: false,
          backgroundColor: "<?=$color;?>", //Success (green)
          borderColor: "<?=$color;?>" //Success (green)
        },
<?php } ?>
        {
          title: 'General 2',
          start: new Date(1970, 12, 2),
          backgroundColor: "#3c8dbc", //Primary (light-blue)
          borderColor: "#3c8dbc" //Primary (light-blue)
        }
      ],
      editable: true,
      droppable: true, // this allows things to be dropped onto the calendar !!!
      drop: function (date, allDay) { // this function is called when something is dropped

        // retrieve the dropped element's stored Event Object
        var originalEventObject = $(this).data('eventObject');

        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject);

        // assign it the date that was reported
        copiedEventObject.start = date;
        copiedEventObject.allDay = allDay;
        copiedEventObject.backgroundColor = $(this).css("background-color");
        copiedEventObject.borderColor = $(this).css("border-color");

        // render the event on the calendar
        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

        // is the "remove after drop" checkbox checked?
        if ($('#drop-remove').is(':checked')) {
          // if so, remove the element from the "Draggable Events" list
          $(this).remove();
        }

      }
    });

    /* ADDING EVENTS */
    var currColor = "#3c8dbc"; //Red by default
    //Color chooser button
    var colorChooser = $("#color-chooser-btn");
    $("#color-chooser > li > a").click(function (e) {
      e.preventDefault();
      //Save color
      currColor = $(this).css("color");
      //Add color effect to button
      $('#add-new-event').css({"background-color": currColor, "border-color": currColor});
    });
    $("#add-new-event").click(function (e) {
      e.preventDefault();
      //Get value and make sure it is not null
      var val = $("#new-event").val();
      if (val.length == 0) {
        return;
      }

      //Create events
      var event = $("<div />");
      event.css({"background-color": currColor, "border-color": currColor, "color": "#fff"}).addClass("external-event");
      event.html(val);
      $('#external-events').prepend(event);

      //Add draggable funtionality
      ini_events(event);

      //Remove event from text input
      $("#new-event").val("");
    });
  });

function createCookies(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookies(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookies(name) {
    createCookie(name,"",-1);
}
function hideUpgrade(value) {
	createCookies('hideUpgrade',value,90);
}
</script>

<?php endif; ?>
<script>
$(document).ready(function() {
	$("a.fancybox").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	false
	});
});

<?php
if(!DEMO_MODE && $URL=='admindashboard' && userRole($userID)<2) {
if(getUser()>0&&userRole($userID) < 3) {isOutdated();autoUpdate(); }
}
?>
<?php if($URL=='cbt') { ?>
$(document).ready(function() {
	<?php if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])) { ?>
	var linkto = jQuery('#linkto').val();
	if(linkto=="exam") {
		jQuery('.show_exam').show();
		jQuery('.required_on_exam').prop('required',true);
		jQuery('.show_course').hide();
	} else {
		jQuery('.show_exam').hide();
		jQuery('.required_on_exam').prop('required',false);
		jQuery('.show_course').show();
	}
	
	var exORass = jQuery('#exORass').val();
	if(exORass=="exam") {
		jQuery('.show_assesment').show();
		jQuery('.required_on_asses').prop('required',true);
	} else {
		jQuery('.show_assesment').hide();
		jQuery('.required_on_asses').prop('required',false);
	}
	
	Query('#exORass').change(function () {
		var exORass = jQuery('#exORass').val();
		if(exORass=="exam") {
			jQuery('.show_assesment').show();
			jQuery('.required_on_asses').prop('required',true);
		} else {
			jQuery('.show_assesment').hide();
			jQuery('.required_on_asses').prop('required',false);
		}
	});
	
	jQuery('#linkto').change(function () {
		var linkto = jQuery('#linkto').val();
		if(linkto=="exam") {
			jQuery('.show_exam').show();
			jQuery('.required_on_exam').prop('required',true);
			jQuery('.show_course').hide();
		} else {
			jQuery('.show_exam').hide();
			jQuery('.required_on_exam').prop('required',false);
			jQuery('.show_course').show();
		}
	});
	<?php } ?>
});

<?php } ?>
<?php if($URL=='coursecontent') { ?>
$(document).ready(function() {
	<?php if(isset($_REQUEST['edit']) || isset($_REQUEST['new'])) { ?>
	var content_type = jQuery('#content_type').val();
	if(content_type=="text") {
		jQuery('#show_file').hide();
		jQuery('#show_text').show();
		jQuery('#show_text2').show();
		jQuery('#show_youtube').hide();
	} else if(content_type=="youtube") {
		jQuery('#show_youtube').show();
		jQuery('#show_file').hide();
		jQuery('#show_text').hide();
		jQuery('#show_text2').hide();
	} else {
		jQuery('#show_youtube').hide();
		jQuery('#show_file').show();
		jQuery('#show_text').hide();
		jQuery('#show_text2').hide();
		if(content_type=="audio") { jQuery('#file-mes').text("MPS or WAV Audio File");$('#uploads').attr("accept", "audio/*")}
		if(content_type=="video") { jQuery('#file-mes').text("MP4 Video File");$('#uploads').attr("accept", "video/*")}
		if(content_type=="file") { jQuery('#file-mes').text("PDF Document");$('#uploads').attr("accept", ".pdf")}
	}
	jQuery('#content_type').change(function () {
		var content_type = jQuery('#content_type').val();
		if(content_type=="text") {
			jQuery('#show_file').hide();
			jQuery('#show_text').show();
			jQuery('#show_text2').show();
			jQuery('#show_youtube').hide();
		} else if(content_type=="youtube") {
			jQuery('#show_youtube').show();
			jQuery('#show_file').hide();
			jQuery('#show_text').hide();
			jQuery('#show_text2').hide();
		} else {
			jQuery('#show_file').show();
			jQuery('#show_text').hide();
			jQuery('#show_text2').hide();
			jQuery('#show_youtube').hide();
			if(content_type=="audio") { jQuery('#file-mes').text("MPS or WAV Audio File");$('#uploads').attr("accept", "audio/*")}
		if(content_type=="video") { jQuery('#file-mes').text("MP4 Video File");$('#uploads').attr("accept", "video/*")}
		if(content_type=="file") { jQuery('#file-mes').text("PDF Document");$('#uploads').attr("accept", ".pdf")}
		}	
	});
	<?php } ?>
});

<?php } ?>
<?php if($URL=='generalsetting') { ?>
$(document).ready(function() {
	var Privileges = jQuery('#en_re');
	if ($(this).val() == '1') {
			$('.resources').show();
	} else $('.resources').hide(); // hide div if value is not "custom"
	Privileges.change(function () {
		if ($(this).val() == '1') {
			$('.resources').show();
		} else $('.resources').hide(); // hide div if value is not "custom"
	});
});
<?php } ?>
$('.hidders').change(function() {
	var value = $('.hidders').val();
	if(value > 0) {
		$('.hidden_fields').hide();
	} else {
		$('.hidden_fields').show();
	}
});	

//sub_class
$('.subjclass').change(function(){
	$('<div class="loadingDiv"><i class="fa fa-spinner fa-spin"></i><br>Please wait...</div>').prependTo(document.getElementById('main-body')); setTimeout(function() { $(".loadingDiv").fadeOut(1500); }, 3000);
	var class_id = $('.subjclass').val();
	var c_subject = $('#c_subject').val();
	if(class_id > 0) {
		$.ajax({
			type:'post',
			url:'admin/API',
			data:{id:class_id,sid:c_subject,sub_classtu:1},
			cache:false,
			success: function(returndata){
				$('.subjstud').html(returndata);
			}
		});
	}
})
//exam_subject_owners
$('.eam_subsj').change(function(){

	$('<div class="loadingDiv"><i class="fa fa-spinner fa-spin"></i><br>Please wait...</div>').prependTo(document.getElementById('main-body')); setTimeout(function() { $(".loadingDiv").fadeOut(1500); }, 3000);
	var subject_id = $('.subjclass').val();
	var exam_class_id = $('#exam_class_id').val();
	var exam_session_id = $('#exam_session_id').val();
	if(class_id > 0) {
		$.ajax({
			type:'post',
			url:'admin/API',
			data:{id:subject_id,sid:exam_class_id,esid:exam_session_id,sub_stud:1},
			cache:false,
			success: function(returndata){
				$('#eampartis').html(returndata);
			}
		});
	}
})

//grade class changes
$('.grad_subjclass').change(function(){
	$('#grad_mulclass').hide();
	var grad_subjclass = $('.grad_subjclass').val();
	if(grad_subjclass === 0) {
		$('#grad_mulclass').hide();
	} else {
		$('#grad_mulclass').show();
	}
})

</script>
<?php global $hooks;$hooks->do_action('CustomJavaScripts'); ?>   
</html>