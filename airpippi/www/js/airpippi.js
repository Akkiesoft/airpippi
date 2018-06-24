function showMessage(itemId, className, msg, timeout) {
	document.getElementById(itemId).innerHTML = msg;
	document.getElementById(itemId).className = className;
	setTimeout("hiddenItem('"+itemId+"')", timeout);
	return false;
}
function hiddenItem(item) {
	document.getElementById(item).className = "hidden";
	return false;
}
function sendAPI(command) {
	var result = fetch('/api/api.php', {
		method: 'post', 
		headers: {
			"Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
		},		
		body: 'command='+command
	});
	var body = result.then(function(response) {
		/* console.log(response.status); */
		return response.text()
	}).then(function(text) {
		return text;
	})
	if (body) {
		return body;
	}
	return false;
}
function power() {
	var result = sendAPI("power");
	result.then(function(body) {
		showMessage("ret_power", "alert alert-success", "電源操作に成功しました", 3000);
	});
	return false;
}
function timer() {
	time = document.getElementById("time").value;
	document.getElementById("time").value = "";
	if (! isFinite(time) || time < 1) {
		showMessage("ret_timer", "alert alert-danger", "1以上の数字を指定してください", 5000);
		return false;
	}
	var result = sendAPI("timer&time="+time);
	result.then(function(body) {
		showMessage("ret_timer", "alert alert-success", time + '分後に電源を操作します<br><small>(' + body + ')</small>', 5000);
	});
	return false;
}
function airpippi_temp() {
	var result = sendAPI("temp");
	result.then(function(temp) {
		var tempicon = "ok";
		if (temp < 18) { tempicon = "remove"; }
		if (17 < temp) { tempicon = "exclamation";  }
		if (21 < temp) { tempicon = "ok"; }
		if (28 < temp) { tempicon = "exclamation";  }
		if (30 < temp) { tempicon = "remove"; }
		document.getElementById("temp").innerHTML = temp + '℃';
		document.getElementById("temp_icon").className = "glyphicon glyphicon-" + tempicon + "-sign";
		setTimeout("airpippi_temp()", 10000);
	});
}