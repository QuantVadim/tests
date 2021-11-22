let qid_counter;
let FormsCreator;
let MainTest = {
	"name": "New Test",
	"questBlocks": []
};
let QuestBlocks = [];
document.addEventListener("DOMContentLoaded", ()=>{
	let allquests = document.querySelectorAll(".quest-block");
	FormsCreator = document.querySelectorAll(".creators-quest .quest-block");
	let el_choice = document.querySelectorAll(".test-wrapper .el-choice");
	qid_counter = 0;
	for(let item of allquests){
		update_quest_listener_editor(item);
		let questStruct = getObjectFromQuest(item);
		QuestBlocks[qid_counter] = { "qid": qid_counter, "question": questStruct, "element": item};
		item.setAttribute("qid", qid_counter++);
	}
	for(let item of FormsCreator){
		item.classList.toggle("_creator", true);
	}
});

function updateInfoAllQuestBlocks(){
	for(let i = 0; i < QuestBlocks.length; i++){
		if(QuestBlocks[i] != undefined){
			let questStruct = getObjectFromQuest(QuestBlocks[i]["element"]);
		QuestBlocks[i]["question"] = questStruct;
		}
	}
}

function updateInfoQuestion(qid){
	for(let i = 0; i < QuestBlocks.length; i++){
		if(QuestBlocks[i]["qid"] == qid){
			let questStruct = getObjectFromQuest(QuestBlocks[i]["element"]);
			QuestBlocks[i]["question"] = questStruct;
			break;
		}
	}
}

function getObjectFromQuest(qb){
	let struct; 
	switch (qb.getAttribute("qtype")) {
		case "simple":
			struct = {
			"type": "simple",
			"uid": undefined,
			"name": undefined,
			"text": undefined,
			"answer": undefined
			};
		struct["name"] = qb.querySelector(".quest-text").textContent;
		struct["text"] = qb.querySelector(".quest-text").textContent;
		struct["answer"] = qb.querySelector("._answer").value.trim();
			break;
		case "choice":
			struct = {
			"type": "choice",
			"uid": undefined,
			"name": undefined,
			"text": undefined,
			"multiselect": undefined,
			"isRow": undefined,
			"answer": [],
			"choice": []
			}
			struct["name"] = qb.querySelector(".quest-text").textContent;
			struct["text"] = qb.querySelector(".quest-text").textContent;
			struct["multiselect"] = qb.querySelector(".el-choice").getAttribute("multiselect");
			struct["isRow"] = qb.querySelector("._param-isrow").getAttribute("ischeck");
			el_ans = qb.querySelectorAll(".el-choice-li_btn");
			let i = 0, j = 0;
			for(let item of el_ans){
				if(item.parentNode.getAttribute("check") == "true"){
					struct["answer"][i++]=item.textContent.trim();
				}else{
					struct["choice"][j++]=item.textContent.trim();
				}
			}
			break;
	}
	struct["uid"] = qb.getAttribute("uid");
	return struct;
}

function update_quest_listener_editor(questBlock){
	switch (questBlock.getAttribute("qtype")) {
		case "choice":
			update_quest_listener_choice_editor(questBlock);
			break;
		default:
			break;
	}
	let qheader = questBlock.querySelector(".quest-header");
	let startDrag = function(e){
		questBlock.setAttribute("draggable", "true");
		HeaderEnable(false);
	};
	qheader.removeEventListener("mousedown", startDrag);
	let endDrag = function(e){
		questBlock.setAttribute("draggable", "false");
		HeaderEnable(true);
		let x = e.clientX, y = e.clientY;
		if(x == 0 && y == 0) x = e.screenX; y = e.screenY;
		let elm = document.elementFromPoint(x, y);
		elm = getParent(elm, "quest-block");
		if(questBlock != elm && elm != undefined && elm.parentNode){
			elm.parentNode.insertBefore(questBlock, elm.nextSibling);
			questBlock.classList.toggle("_new", true);
		}
	};
	let overDrag = function(e){e.preventDefault()};
	questBlock.removeEventListener("dragend", endDrag);
	questBlock.removeEventListener("dragover", endDrag);
	questBlock.addEventListener("dragover", overDrag);
	qheader.addEventListener("mousedown", startDrag);
	questBlock.addEventListener("dragend", endDrag);
}

function btn_multiselect(btn){
	let qb = getParent(btn, "quest-block");
	let bl = btn.getAttribute("ischeck") == "true"? true: false;
	qb.querySelector(".el-choice").setAttribute("multiselect", bl);
	if(!bl){
		let lis = qb.querySelectorAll(".el-choice-li");
		let b = true;
		for(let item of lis){
			if(item.getAttribute("check") == "true" && b){
				b = false;
			}else{
				item.setAttribute("check", "false");
			}
		}
	}
	console.log(qb);
}

function update_quest_listener_choice_editor(questBlock){
	let el_choice_li = questBlock.getElementsByClassName("el-choice-li_btn");
	for(let choice of el_choice_li){
		choice.addEventListener("click", ()=>{
			el_choice_check(choice);
		});
		getParent(choice, "el-choice-li").querySelector("._drop").addEventListener("click", ()=>{
			getParent(choice, "el-choice-li").remove();
		});
	}
	let textBox = questBlock.querySelector(".el-choice_add-text-choice");
	let list = questBlock.querySelector(".el-choice");
	let allvalues = list.querySelectorAll(".el-choice-li_btn");
	let btn_add = questBlock.querySelector("._btn-add");
	let param_isrow = questBlock.querySelector("._param-isrow");
	let param_ismultiselect = questBlock.querySelector("._param-multiselect");
	let Change_isRow = function(e){
		let bl = param_isrow.getAttribute("ischeck") == "true"? true: false;
		list.classList.toggle("__isrow", bl);
		param_isrow.removeEventListener("clcik", Change_isRow);
	};
	param_isrow.addEventListener("click", Change_isRow);

	let isContainsWord = function(word){
		let ret = false;
		for(let val of allvalues){
			if(val.textContent.trim() == word.trim()) ret = true;
		}
		return ret;
	}
	let enterWord = function(event){
		let word = textBox.value.trim();
		if(word.length > 0 && !isContainsWord(word)){
		list.innerHTML+="<div class='el-choice-li'><button class='el-choice-li_btn'>"+textBox.value.trim()+"</button><button class='_drop'> X </buton></div>"
		update_quest_listener_editor(questBlock);
		textBox.removeEventListener("keyup", textEnter, false );
		textBox.value = "";
		}
	}
	let textEnter = function(event){
		if(event.keyCode == 13){
			enterWord(event);
		};
	}
	btn_add.onclick = enterWord;
	textBox.addEventListener("keyup", textEnter, false );

}

function el_choice_check(selectItem){
	let list = getParent(selectItem, "el-choice");
	let lis = list.querySelectorAll(".el-choice-li");
	let list_li = getParent(selectItem, "el-choice-li") ;
	if(list.getAttribute("multiselect") == "true"){
		if(list_li.getAttribute("check") == "true"){
			list_li.setAttribute("check", "false");
		}else{
			list_li.setAttribute("check", "true");
		}
	}else{
		for(let element of lis){
			element.setAttribute( "check" , "false");
		}
		list_li.setAttribute("check", "true");
	}
}

function CreateQuestForm(typeQuest){
	let form;
	let myid = qid_counter++;
	for(let item of FormsCreator){
		if(item.getAttribute("qtype") == typeQuest){
			form = item;
		}
	}
	let newQB = document.createElement("div");
	newQB.classList.toggle("quest-block");
	newQB.setAttribute("qid", myid);
	newQB.setAttribute("uid", Math.random().toString().split('.')[1]);
	newQB.setAttribute("qtype", typeQuest);
	newQB.classList.toggle("_new", true);
	newQB.innerHTML = form.innerHTML;
	document.querySelector(".test-wrapper").appendChild(newQB);
	QuestBlocks[myid] = { "qid": qid_counter, "question": getObjectFromQuest(newQB), "element": newQB};
	update_quest_listener_editor(newQB);
}

function btn_deleteQuestBlock(btn){
	let qb = getParent(btn, "quest-block");
	QuestBlocks[qb.getAttribute("qid")] = undefined;
	qb.classList.toggle("__anim-delet-quest")
	setTimeout(()=>{
		qb.remove();
	}, 200);
}

function saveTest(){
	updateInfoAllQuestBlocks();
	let ques = [];
	let i = 0;
	for(let item of QuestBlocks){
		if(item != undefined && !item["element"].classList.contains("_creator")){
			ques[i++] = item["question"];
		}
	}
	let testName = document.querySelector("input._edit-test-name").value;
	document.querySelector("#form-save #data-test-id").value = test_id;
	document.querySelector("#form-save #data-test-name").value = testName;
	document.querySelector("#form-save #data-update").value =  JSON.stringify(ques);
	document.querySelector("#form-save").submit();
}

