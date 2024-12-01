var messageList = document.querySelector(".im-msgs");
if(messageList) { messageList.addEventListener("click", removeMessage); }
function removeMessage (e) {
	var el = e.target;
	var messageContainer;
	var list;
	console.log(el.parentNode.classList);
	if (el.parentNode.classList.contains("close")) {
		messageContainer = el.parentNode.parentNode;
		list = messageContainer.parentNode;
		list.removeChild(messageContainer);
	}
};
