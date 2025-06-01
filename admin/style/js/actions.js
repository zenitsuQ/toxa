//Функция вставки BB-кодов в поле ввода текста
function AddBB(bb)
{
	var elem = document.getElementById('text');
	elem.focus();

	if (document.selection) {
		var s = document.selection.createRange(); 
		if (s.text) {
			if (bb != "color")
			{
				s.text = "[" + bb + "]" + s.text + "[/" + bb + "]";
			}
			else
			{
				s.text = "[" + bb + "=#000000]" + s.text + "[/" + bb + "]";
			}
			s.select();
			return true;
		}
	}
	else
	{
		if (typeof(elem.selectionStart) == "number") {
				var start = elem.selectionStart;
				var end   = elem.selectionEnd;
				var bblen = bb.length;

				if (bb != "color")
				{
					var rs = "[" + bb + "]" + elem.value.substr(start, end-start) + "[/" + bb + "]";
					bblen += 2;
				}
				else
				{
					var rs = "[" + bb + "=#000000]" + elem.value.substr(start, end-start) + "[/" + bb + "]";
					bblen += 10;
				}
				elem.value = elem.value.substr(0, start) + rs + elem.value.substr(end);
				elem.setSelectionRange(start + bblen, end + bblen);
			return true;
		}
	}
	return false;
}

//Функция вставки символов в поле ввода текста
function AddSym(sym)
{
	var elem = document.getElementById('text');
	elem.focus();

	if (typeof(elem.selectionStart) == "number") {
		var start = elem.selectionStart;
		var end   = elem.selectionEnd;

		elem.value = elem.value.substr(0, start) + sym + elem.value.substr(end);
		if (start != end)
		{
			elem.setSelectionRange(start + sym.length, end);
		}
		else
		{
			elem.setSelectionRange(start + sym.length, end + sym.length);
		}
		elem.scrollTop = scrollTop;
		return true;
	}
	return false;
}

//Функция вставки смайликов
function AddSmile(smile)
{
	var sms = document.texthere.text;
	if (document.selection) {
		sms.focus();
		var sel = document.selection.createRange();
		sel.text = smile;
		sms.focus();
	}
	else
	if (sms.selectionStart || sms.selectionStart == '0') {
		var startPos = sms.selectionStart;
		var endPos = sms.selectionEnd;
		var scrollTop = sms.scrollTop;
		sms.value = sms.value.substring(0, startPos)+smile+sms.value.substring(endPos,sms.value.length);
		sms.focus();
		sms.selectionStart = startPos + smile.length;
		sms.selectionEnd = startPos + smile.length;
		sms.scrollTop = scrollTop;
	} else {
		sms.value += smile;
		sms.focus();
	}
}