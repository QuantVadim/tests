function getCookie(name) {
  let matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

const ajaxLink = document.location.origin+"/ajax.php";
function requestToServer(sendObj, funcDoAfter, isJSON = true, link = ajaxLink){
	let myID = getCookie('user_id');
	let key = getCookie('access_key');
	let params = "myid="+myID+"&key="+key+"&user_id="+curUserID+"&object="+JSON.stringify(sendObj);
	const r = new XMLHttpRequest();
	r.open("POST", link, true);
	console.log(params);
	r.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	r.addEventListener("readystatechange", ()=>{
		if(r.readyState === 4 && r.status === 200){
			let rspServer = isJSON ? JSON.parse(r.responseText): r.responseText;
			if(rspServer){
				funcDoAfter(rspServer);	
			}else{
				console.log(rspServer);
			}
		}
	});
	r.send(params);
}