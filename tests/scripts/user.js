let AllPages = [];
let pageIsLoading = false;
let loadedPageName = "";

function logout(){
	document.getElementById("form-logout").submit();
}

document.addEventListener("DOMContentLoaded", (e)=>{
	switchPage('my_tests');
});

function LoadPage(name){
	if(!pageIsLoading){
		pageIsLoading = true;
		loadedPageName = name;
		let myid = getCookie('user_id');
		let key = getCookie('access_key');
		obj = {"type":"get_user_page", "page_name":name, "count": 30, "last": 0};
		if(obj){
			requestToServer(obj, onLoadedPage);
		}
	}
}

let pagesNames = {
	"my_tests": "Тесты",
	"my_results": "Ответы",
	"my_subscriptions": "Подписки",
	"my_subscribers": "Подписчики",
	"my_answers": "Решения"
};

function onLoadedPage(obj){
	if(obj){
		let allPg = document.querySelectorAll("#user-pages > div");
		let upgs = document.querySelector(".ajax-pages");
		let mypg = false;
		for(let item of allPg){
			if(item.getAttribute("name") == obj.page_name){
				mypg = item;
				break;
			}
		}
		if(!mypg){
			let d = document.createElement("DIV");
			d.classList.toggle("content-wrapper", true);
			d.setAttribute("name", obj.page_name);
			upgs.appendChild(d, upgs.parentNode.nextSibling);
			mypg = d;
		}
		
		mypg.innerHTML+=obj.html;
		updateInfoPages();
		switchPage(loadedPageName);
	}
	pageIsLoading = false;
}

function switchPage(name){
	let namebox = document.querySelector('.tool-box ._name-page');
	namebox.innerHTML = pagesNames[name];
	let tools = document.querySelectorAll(".tool-box > div");
	for(let item of tools){
		if(item.getAttribute("name")!=name && item.classList.contains("_tools"))
			item.classList.toggle("__active", false);
		else
			item.classList.toggle("__active", true);
	}
	updateInfoPages();
	PageON = getPage(name);
	if(PageON){
		for(let item of AllPages){
			if(item.name == name)
				item.element.setAttribute("visible", true);
			else item.element.setAttribute("visible", false);
		}
	}else{
		LoadPage(name);
	}
}

function getPage(name){
	let ret = false;
	for(let item of AllPages){
		if(item.name == name){
			ret = item.element;
		}
	}
	return ret;
}

function updateInfoPages(){
	let upgs = document.querySelectorAll("#user-pages > div");
	AllPages = [];
	for(let item of upgs){
		let obj = {
		"name": item.getAttribute("name"),
		"element": item
		};
		AllPages.push(obj);
	}
}

function getSelectedSubscribers(){
	let d = document.querySelectorAll("#user-pages > div[name='my_subscribers'] > .__selected");
	let arr = [];
	for(let item of d){
		arr.push(item.getAttribute("userid"));
	}
	return arr;
}

let isCreateGroupMode = false;

function selectSubscriber(usr){
	if(isCreateGroupMode){
		if(!modeBtnGroup){
			usr.classList.toggle("__selected");
			let d = document.querySelector("#user-pages > div[name='my_subscribers'] > .__selected");
			let os = document.querySelector(".tool-box ._tools ._only-selected");
			if(d){
				os.classList.toggle("__active", true);
				if(curGroup != "all"){
					os.querySelector("._only-group").classList.toggle("__active", true);
				}else os.querySelector("._only-group").classList.toggle("__active", false);
			}else if(os) os.classList.toggle("__active", false);
		}else{
			modeBtnGroup = false;
			
		}
	}
}

function btn_selectMode(btn){
	isCreateGroupMode = !isCreateGroupMode;
	if(!isCreateGroupMode){
		btn.innerHTML="Выбрать";
		let d = document.querySelectorAll("#user-pages > div[name='my_subscribers'] > .__selected");
		for(let item of d){
			item.classList.toggle("__selected", false);
		}
		SubrsribersPage.SetSelectMode(false);
	}else{
		btn.innerHTML="Отмена";

	}
	
}

function btnOW_createGroup(btn){
	document.querySelector(".win-create-group").classList.toggle("__opened", true);
}

function CreateGroup(){
	let inputName = document.querySelector("#name-group");
	let nameGroup = inputName.value.trim();
	if(nameGroup.length > 0 && nameGroup.length < 70){
		listusrs = document.querySelectorAll("#user-pages > div[name='my_subscribers'] > .__selected");
		let listUsers = [];
		for(let item of listusrs){
			listUsers.push(item.getAttribute("userid"));
		}
		
		let obj = {
			"users": listUsers,
			"groupName": nameGroup
		};
		let myid = getCookie('user_id');
		let key = getCookie('access_key');
		params = "myid="+myid+"&key="+key+"&user_id="+curUserID+"&type=create_group&object="+JSON.stringify(obj);
		requestToServer(ajaxlink, params, onCreatedGroup);
	}
}

function onCreatedGroup(info){
	document.querySelector(".win-create-group").classList.toggle("__opened", false);
	let groupBox = document.querySelector(".tool-box ._tools[name='my_subscribers'] ._subboard");
	let obj = JSON.parse(info);
	let newBtn = document.createElement("BUTTON");
	newBtn.innerHTML = obj.name;
	newBtn.setAttribute("gid", obj.id);
	newBtn.setAttribute("onclick", "switchGroup(this)");
	groupBox.appendChild(newBtn, groupBox.nextSibling);
	switchGroup(newBtn);
}

let curGroup = "all";

let groupIsLoading = false;
let modeBtnGroup = false;

function LoadGroup(id){
	if(!groupIsLoading){
		let myid = getCookie('user_id');
		let key = getCookie('access_key');
		let obj = false;
		let params;
		obj = {"type":"group", "group_id":id, "count": 30, "last": 0};
		params = "myid="+myid+"&key="+key+"&user_id="+curUserID+"&type=get_html&object="+JSON.stringify(obj);
		requestToServer(ajaxlink, params, onLoadedGroup);
	}
}

function onLoadedGroup(info){
	let obj = JSON.parse(info);
	let tb = document.querySelector("#user-pages .content-wrapper[name='my_subscribers']");
	tb.innerHTML = obj.html;
	groupIsLoading = false;
}

//Нажатие кнопки ГРУППЫ
function switchGroup(btn){
	let btnid =  btn.getAttribute("gid");
	switchGroupById(btnid);
}

function switchGroupById(group_id){
	let btnid = group_id;
	if(!modeBtnGroup){
	 	let btns = document.querySelectorAll(".tool-box ._tools[name='my_subscribers'] ._subboard button");
	 	for(let item of btns){
	 		if(item.getAttribute("gid") == btnid)
	 			item.classList.toggle("__disable", false);
	 		else item.classList.toggle("__disable", true);
	 	}
		if(curGroup != btnid){
			curGroup = btnid;
			LoadGroup(btnid);
		}
		SubrsribersPage.UpdateButtons();
	}else if(modeBtnGroup == "add"){
		//Добавление пользователей в группу
		let myid = getCookie('user_id');
		let key = getCookie('access_key');
		let users = getSelectedSubscribers();
		let obj = {"type":"addUsers", "group_id":btnid, "users": users};
		let params = "myid="+myid+"&key="+key+"&user_id="+curUserID+"&type=addUsersInGroup&object="+JSON.stringify(obj);
		requestToServer(ajaxlink, params, onChangedUsersFromGroups);
	}
	SubrsribersPage.SetSelectMode(false);
}



function btnDeleteUsersFromGroup(groupID){
	let arr = getSelectedSubscribers();
	let myid = getCookie('user_id');
	let key = getCookie('access_key');
	let obj = {"group_id":groupID, "users": arr};
	params = "myid="+myid+"&key="+key+"&user_id="+curUserID+"&type=deleteUsersFromGroup&object="+JSON.stringify(obj);
	requestToServer(ajaxlink, params, onChangedUsersFromGroups);
	SubrsribersPage.SetSelectMode(false);
}


function onChangedUsersFromGroups(info){
	let obj = JSON.parse(info);
	obj ? console.log(obj):false;
	if(obj){
		switch (obj.type) {
			case "deleteUsersFromGroup":
				if(obj.status == "del"){
					let btng = document.querySelector(".tool-box ._tools[name='my_subscribers'] ._subboard button[gid='"+obj.group_id+"']");
					btng.remove();
					modeBtnGroup = false;
					switchGroupById("all");
				}
				else LoadGroup(obj.group_id);
				break;
			case "addUsersInGroup":
				LoadGroup(obj.group_id);
				break;
		}
	}
	SubrsribersPage.SetAddUsersMode(false);
	modeBtnGroup = false;
}

function btnGroupMode(mode){
	switch(mode){
		case "add":
			modeBtnGroup = "add";
			SubrsribersPage.SetAddUsersMode(true);
			break;
		case "del":
			if(curGroup != "all"){
				modeBtnGroup = 'del';
				btnDeleteUsersFromGroup(curGroup);
			}
			break;
	}
}

let SubrsribersPage = {
	SetSelectMode: function(isEnable){
		document.querySelector(".tool-box ._tools ._only-selected").classList.toggle("__active", isEnable);
		if(!isEnable) this.SetAddUsersMode(false);
	},
	SetAddUsersMode: function(isEnable){
		let btng = document.querySelector(".tool-box ._tools[name='my_subscribers'] ._subboard");
			btng.classList.toggle("_rolling", isEnable);
	},
	UpdateButtons: function(){
		let btns = document.querySelectorAll(".tool-box ._tools[name='my_subscribers'] ._subboard button");
	 	for(let item of btns){
	 		if(item.getAttribute("gid") == btnid)
	 			item.classList.toggle("__disable", false);
	 		else item.classList.toggle("__disable", true);
	 	}
	}

}
