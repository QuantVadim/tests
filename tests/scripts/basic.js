

document.addEventListener("paste", (e)=>{
	if(e.target.hasAttribute("contenteditable") && e.clipBoardData){
		e.preventDefault();
		let text = e.clipBoardData.getData("text/plain").replace(/<\/?[^>]+>/g, "");
		text = getNormalText(text);
		document.execCommand("insertHTML", false, "");
	}
});

document.addEventListener("drop", (e)=>{
	if(e.target.hasAttribute("contenteditable")){
		e.preventDefault();
		document.execCommand("insertHTML", false, "");
	}
});

function getNormalText(text){
	let ret = text;
	while(ret.indexOf("<") >= 0){
		ret = ret.replace("<", "&lt;");		
	}
	while (ret.indexOf(">") >= 0) {
		ret = ret.replace(">", "&gt;");
	}
	ret = ret.split(/\n/g).join("<br>");
	return ret;
}

function getParent(child, parentClass){
	let upObj = child;
	while(!upObj.classList.contains(parentClass) && upObj.tagName != "BODY"){
		upObj = upObj.parentElement;
	}
	if(upObj.tagName == "BODY") upObj = false;
	return upObj;
}

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
			console.log(r.responseText);
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

function checkbox(btn){
	if(btn.getAttribute("ischeck") == "true")
		btn.setAttribute("ischeck", "false");
	else btn.setAttribute("ischeck", "true");
}

function HeaderEnable(isEnable){
	//let hd = document.querySelector("header");
	//hd.classList.toggle("_hidden-for-mouse", isEnable);
}

function btn_follow(btn){
	if(btn.getAttribute("waiting") != "true"){
		let myid = getCookie("user_id");
		let key = getCookie("access_key");
		let flwid = btn.getAttribute("userid");
		btn.innerHTML = "Загрузка...";
		btn.setAttribute("waiting", "true");
		requestToServer({'type':'action', 'name': 'tofollow'}, onReciveFollowFromServer);
	}
}

function onReciveFollowFromServer(obj){
	if(obj){
		let b = document.querySelector(".btn-follow[userid='"+obj.user_id+"']");
		let isFollow = obj.value == "follow"? true: false;
		b.innerHTML = $isFollow? "Не следить": "Следить";
		b.classList.toggle('__follow', isFollow);
		b.setAttribute("waiting", "false");
	}
}

