// Gruppenräume

Prototype.Browser.IE6 = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 6;
Prototype.Browser.IE7 = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 7;
Prototype.Browser.IE8 = Prototype.Browser.IE && !Prototype.Browser.IE6 && !Prototype.Browser.IE7;

var xmouse;
var ymouse;
document.onmousemove=getMouseCoordinates;

var self="index.php";

function getMouseCoordinates(event)
{
	ev = event || window.event;
	
	xmouse=ev.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
	ymouse=ev.clientY + document.body.scrollTop + document.documentElement.scrollTop;

}


function getBookingForm(date, time, room)
{
	$("booking-form").style.display="block";
	
	$("error-alert").style.display="none";
	
	$("booking-form").style.top=(ymouse-4) + "px";
	$("booking-form").style.left=xmouse + "px";
	
	getData("get_room_title", room, "b-raum");
	getData("convert_time", time, "b-von");
	getData("convert_date", date, "b-datum");
	getData("get_end_times", date + "," + time + "," + room, "bis");
	
	$("datum").value=date;
	$("raum").value=room;
	$("von").value=time;
}

function getConfirmationForm(date, time, room, confirm)
{
	$("confirmation-form").style.display="block";
	$("c-print").style.display="none";
	$("c-error-alert").style.display="none";
	
	if(confirm) 
	{
		$("c-confirm").style.display="block";
		if(confirm==1)
		{
			new Ajax.Updater("c-headline", 'ajax_php/loc.php', {
				  parameters: { key_term: 'marking' }
				});
		}
		else if(confirm==2)
		{
			$("c-confirm").style.display="none";

			new Ajax.Updater("c-delete", 'ajax_php/loc.php', {
				  parameters: { key_term: 'delete_reservation' }
				});

			new Ajax.Updater("c-headline", 'ajax_php/loc.php', {
				  parameters: { key_term: 'reservation' }
				});
			$("c-print").style.display="block";
		}
	}
	else $("c-confirm").style.display="none";
	
	$("confirmation-form").style.top=(ymouse-4) + "px";
	$("confirmation-form").style.left=xmouse + "px";
	
	getData("get_room_title", room, "c-raum");
	getData("convert_time", time, "c-von");
	getData("convert_date", date, "c-datum");
	getData("get_end_times", date + "," + time + "," + room, "c-bis");
	
	$("cf-datum").value=date;
	$("cf-raum").value=room;
	$("cf-von").value=time;
}

function getLoginAlert()
{
	$("login-alert").style.display="block";
	$("login-alert").style.top=(ymouse-4) + "px";
	$("login-alert").style.left=xmouse + "px";
}

function getTodayAlert()
{
	$("today-alert").style.display="block";
	$("today-alert").style.top=(ymouse-4) + "px";
	$("today-alert").style.left=xmouse + "px";
}

function login()
{
	window.location.href=self + "?login";
}

function logout()
{
	window.location.href=self + "?logout";
}

function closeForm(form)
{
	$(form).style.display="none";
}

function bookIt()
{
	var datum=$F("datum");
	var von=$F("von");
	var bis=$F("bis");
	var raum=$F("raum");
	var login_id2=$F("login_id2");

	if(!login_id2) 
	{
		$("error-alert").style.display="block";
		new Ajax.Updater("error-alert", 'ajax_php/loc.php', {
			  parameters: { key_term: 'type_login_id2' }
			});
		return;
	}
	else $("error-alert").style.display="none";
	

	var data=datum + "," + von + "," + bis + "," + raum + "," + login_id2;
	var url="ajax_php/set_data.php";
	
	new Ajax.Request(url, 
	{
		method: 'get',
		parameters: 
		{
			action: "book_room", 
		    value: data
		},
		onSuccess: function(transport) 
		{
			$("error-alert").style.display="block";
			
			if(transport.responseText=="ok") 
			{
				new Ajax.Updater("error-alert", 'ajax_php/loc.php', {
					  parameters: { key_term: 'alert_marking_saved' }
					});
				
				new Ajax.Updater("rb", 'ajax_php/bookings.php');
				setTimeout("closeForm('booking-form')", 2000);
			}
			else 
			{
				$("error-alert").innerHTML=transport.responseText;
				setTimeout("closeForm('booking-form')", 2000);
			}
		}
	}
	);

	
}

function confirmIt()
{
	var datum=$F("cf-datum");
	var von=$F("cf-von");
	var raum=$F("cf-raum");

	var data=datum + "," + von + "," + raum;
	var url="ajax_php/set_data.php";
	
	new Ajax.Request(url, 
	{
		method: 'get',
		parameters: 
		{
			action: "confirm", 
		    value: data
		},
		onSuccess: function(transport) 
		{
			$("c-error-alert").style.display="block";
			
			if(transport.responseText=="ok") 
			{
				new Ajax.Updater("c-error-alert", 'ajax_php/loc.php', {
					  parameters: { key_term: 'alert_marking_confirmed' }
					});
				new Ajax.Updater("rb", 'ajax_php/bookings.php');
				
				setTimeout("closeForm('confirmation-form')", 2000);
			}
			else 
			{
				$("c-error-alert").innerHTML=transport.responseText;
				setTimeout("closeForm('confirmation-form')", 2000);
			}
		}
	}
	);

	
}

function deleteIt()
{
	var datum=$F("cf-datum");
	var von=$F("cf-von");
	var raum=$F("cf-raum");

	var data=datum + "," + von + "," + raum;
	var url="ajax_php/set_data.php";
	
	new Ajax.Request(url, 
	{
		method: 'get',
		parameters: 
		{
			action: "delete", 
		    value: data
		},
		onSuccess: function(transport) 
		{
			$("c-error-alert").style.display="block";
			
			if(transport.responseText) 
			{
				$("c-error-alert").innerHTML=transport.responseText;
				new Ajax.Updater("rb", 'ajax_php/bookings.php');
				setTimeout("closeForm('confirmation-form')", 2000);
			}
			else 
			{
				new Ajax.Updater("c-error-alert", 'ajax_php/loc.php', {
					  parameters: { key_term: 'alert_deletion_not_possible' }
					});
				setTimeout("closeForm('confirmation-form')", 2000);
			}
		}
	}
	);

	
}

function reload()
{
	window.location.reload();
}



function getData(action, value, element_id)
{
	var url="ajax_php/get_data.php";
	
	new Ajax.Request(url, 
	{
		method: 'get',
		parameters: 
		{
			action: action, 
		    value: value
		},
		onSuccess: function(transport) 
		{
			if(element_id=="bis")
			{
				bisOptions=$w(transport.responseText);
				selected=0;
				$("bis").options.length = 0;
				
				for(i=0; i < bisOptions.length; i++) 
				{
					bo=bisOptions[i].split(",");
					if(bo[0]=="selected") selected=bo[1];
					else $("bis").options[i] = new Option(bo[1], bo[0]);
				}
				$("bis").selectedIndex=selected;

			}	
			else $(element_id).innerHTML=transport.responseText;
		}
	}
	);
}


function setData(action, value)
{
	var url="ajax_php/set_data.php";
	
	new Ajax.Request(url, 
	{
		method: 'get',
		parameters: 
		{
			action: action, 
		    value: value
		},
		onSuccess: function(transport) 
		{
			return 1;
		}
	}
	);
}


function showRoomAndTime(date, currentTime, room)
{
	$("room-" + date + "-" + room).style.backgroundColor="#555555";
	$("time-" + currentTime).style.backgroundColor="#555555";
}

function hideRoomAndTime(date, currentTime, room)
{
	$("room-" + date + "-" + room).style.backgroundColor="";
	$("time-" + currentTime).style.backgroundColor="";
}


function printReceipt()
{
	var datum=$F("cf-datum");
	var von=$F("cf-von");
	var raum=$F("cf-raum");
	
	window.open("print.php?datum=" + datum + "&von=" + von + "&raum=" + raum, "", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=600,height=400");
	setTimeout("closeForm('confirmation-form')", 2000);
}

function removeInfoBox(close)
{
	document.body.removeChild(close.getOffsetParent());
}

function getInfoBox(page, title)
{   
	url="ajax_php/info_content.php";
	new Ajax.Request(url, 
	{
		method: 'get',
		parameters: {page: page},
		onSuccess: function(transport) 
		{	
			if(transport.responseText && $(page)==null)
			{	
				var infoBox = document.createElement("div");
			    infoBox.className = "info-popup";
			    infoBox.id = page;

			    if(!Prototype.Browser.IE7 && !Prototype.Browser.IE6)
			    {
				    var close = document.createElement("a");
				    close.className="info-popup-close";
				    close.href="javascript:void(0)";
				    Element.writeAttribute(close, "onclick", "removeInfoBox(this)");
				  
				    close.appendChild(document.createTextNode("X"));
				    Element.insert(infoBox, {top: close});
			    }
			    
			    var handle=document.createElement("div");
			    handle.className="info-popup-handle";
			    if(title)
			    {
			    	if(title.length > 57) title=title.substr(0, 57) + " ...";
			    	handle.appendChild(document.createTextNode(title));
			    }
			    Element.insert(infoBox, {top: handle});
			    
			    var infoBoxContent=document.createElement("div");
			    infoBoxContent.className="info-popup-content";
			    infoBoxContent.innerHTML=transport.responseText;
			    Element.insert(infoBox, {bottom: infoBoxContent});

			    infoBox.style.position = "absolute";
			    
			    if(Prototype.Browser.IE7 || Prototype.Browser.IE6)
			    {
				    infoBox.style.top="750px"
				    if(page=="regeln") 
				    {
				    	infoBox.style.top="834px"
				    	infoBox.style.left="590px";
				    }
				    else 
				    {
				    	infoBox.style.top="750px"
				    	infoBox.style.left="20px";
				    }
			    }
			    else
			    {
				    infoBox.style.top="150px"
					infoBox.style.left="250px";
				    new Draggable(infoBox, { zindex: 10000, handle: handle, starteffect: false, endeffect: false });
			    }
			    document.body.appendChild(infoBox);

			}
		}
	}
	);
}

/*
Event.observe(window, 'load', function() {
	
});
*/


