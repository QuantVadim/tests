let QuestBlocks = [];
let qid_counter = 0;

document.addEventListener("DOMContentLoaded", ()=>{
	let allquests = document.querySelectorAll(".quest-block");
	let el_choice = document.querySelectorAll(".test_wrapper .el-choice");
	let el_choice_li = document.getElementsByClassName("el-choice-li_btn");
	for(let item of allquests){
		let questStruct = getObjectFromQuest(item);
		QuestBlocks[qid_counter] = { "qid": qid_counter, "question": questStruct, "element": item};
		item.setAttribute("qid", qid_counter++);
	}
	for(let element of el_choice_li){
		element.addEventListener("click", ()=>{
			el_choice_check(element);
		});
	}
});

function el_choice_check(selectItem){
	let list = selectItem.parentNode.parentNode;
	let lis = list.querySelectorAll(".el-choice-li");
	let list_li = selectItem.parentNode;
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

function updateInfoAllQuestBlocks(){
	for(let i = 0; i < QuestBlocks.length; i++){
		if(QuestBlocks[i] != undefined){
			let questStruct = getObjectFromQuest(QuestBlocks[i]["element"]);
		QuestBlocks[i]["question"] = questStruct;
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
			"answer": []
			}
			el_ans = qb.querySelectorAll(".el-choice-li_btn");
			let i = 0, j = 0;
			for(let item of el_ans){
				if(item.parentNode.getAttribute("check") == "true"){
					struct["answer"][i++]=item.textContent.trim();
				}
			}
			break;
	}
	struct["uid"] = qb.getAttribute("uid");
	return struct;
}


function sendTest(){
	updateInfoAllQuestBlocks();
	let ques = [];
	let i = 0;
	for(let item of QuestBlocks){
		if(item != undefined && !item["element"].classList.contains("_creator")){
			ques[i++] = item["question"];
		}
	}
	document.querySelector("#test-send #data-test-id").value = test_id;
	document.querySelector("#test-send #data-answers").value =  JSON.stringify(ques);
	document.querySelector("#test-send").submit();
}