<div id="booking-form" class="popups"><a id="close-form"
	href="javascript: closeForm('booking-form')">x</a>
<h3>%~mark_group_work_room%</h3>
<p>%~room% <span id="b-raum"></span> %~date_on% <span id="b-datum"></span><br />
%~between% <span id="b-von"></span> %~and% <select id="bis" name="bis"></select>
%~time_uhr%. <br />
%~login_id_of_user% 2: <input type="text" id="login_id2"
	name="login_id2"></p>
<p id="error-alert" class="alert"></p>
<button type="button" onclick="bookIt()">%~mark%</button>
<input type="hidden" id="datum" name="datum"> <input type="hidden"
	id="raum" name="raum"> <input type="hidden" id="von" name="von">
</div>

<div id="confirmation-form" class="popups"><a id="close-form"
	href="javascript: closeForm('confirmation-form')">x</a>
<h3 id="c-headline">%~marking%</h3>
<p>%~room% <span id="c-raum"></span>&nbsp;%~date_on% <span
	id="c-datum"></span><br />
%~time_from% <span id="c-von"></span>&nbsp;%~time_uhr%.</p>
<button id="c-confirm" type="button" onclick="confirmIt()" style="float: left; margin-right: 3px">%~confirm_marking%</button>
<button id="c-print" type="button" onclick="printReceipt()" style="float: left; margin-right: 3px">%~print_receipt%</button>
<button id="c-delete" type="button" onclick="deleteIt()">%~delete_marking%</button>
<input type="hidden" id="cf-datum" name="datum"> <input type="hidden"
	id="cf-raum" name="raum"> <input type="hidden" id="cf-von" name="von">
<p id="c-error-alert" class="alert"></p>
</div>

<div id="login-alert" class="popups"><a id="close-form"
	href="javascript: closeForm('login-alert')">x</a>
<h3>%~login_required%</h3>
<p>%~please_login%</p>
<button type="button" name="login-button" id="login-button"
	onclick="login()">Login</button>
</div>

<div id="today-alert" class="popups"><a id="close-form"
	href="javascript: closeForm('today-alert')">x</a>
<h3>%~no_reservation%</h3>
<p>%~no_reservation_for_current_day%</p>
</div>