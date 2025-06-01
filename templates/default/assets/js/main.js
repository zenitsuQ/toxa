// Функция добавления смайликов в текст
function AddSmile(smile)
{
	var
		sms = document.texthere.text;

	if (document.selection)
	{
		sms.focus();

		var
			sel = document.selection.createRange();

		sel.text = smile;
		sms.focus();
	}
	else
		if (sms.selectionStart || sms.selectionStart == "0")
		{
			var
				startPos  = sms.selectionStart,
				endPos    = sms.selectionEnd,
				scrollTop = sms.scrollTop;

			sms.value = sms.value.substring(0, startPos) + smile + sms.value.substring(endPos, sms.value.length);
			sms.focus();

			sms.selectionStart = startPos + smile.length;
			sms.selectionEnd   = startPos + smile.length;
			sms.scrollTop      = scrollTop;
		} else
		{
			sms.value += smile;
			sms.focus();
		}
}

// Просмотр картинки в полном размере
function ShowFull(image)
{
	var
		div = $("#shop-image-show-full");

	div.css('background-image', 'url(' + image + ')');

	div.fadeIn(300);
}

// Закрыть окно просмотра
function CloseFull()
{
	$("#shop-image-show-full").fadeOut(300);
}

// Смена сортировки
function SortBy(sort)
{
	if (sort > 0 && sort < 5 && !isNaN(sort))
	{
		/*
			Параметры сортировки:

			1 - По имени
			2 - По имени DESC
			3 - По ID
			4 - По ID DESC
		*/
		var
			link = "/api/js/?order_by=" + sort;

		$.ajax({
			url         : link,
			type        : 'GET',
			beforeSend: function(xhr)
			{
				xhr.setRequestHeader('JS-API-Client', '1.0');
			},
			success     : function(data)
			{
				if (data == "OK")
				{
					location.reload();
				}
				else
					alert("Ошибка смены сортировки!");
			}
		});
	}
	else
		alert("Некорректный параметр сортировки!");
}

// Оценка
$(document).ready(function()
{
	if (!$("div").is(".mark"))
		return;

	var
		rate       = 0,
		step       = 40;
		offsetstar = $(".mark").offset().left,

	$(".mark").mousemove(function(event)
	{ 
		$(".mark_action").width(Math.ceil((event.pageX-offsetstar) / step) * step);
	});
	$(".mark").mouseleave(function()
	{
		$(".mark_action").width(rate * step);
	});
	$(".mark").click(function(event)
	{
		rate = Math.ceil((event.pageX-offsetstar) / step);
		$("[name = mark]").val(rate);
	});
});

// Количество товара
$(document).ready(function()
{
	if (!$("div").is(".shop-item-minus") || !$("div").is(".shop-item-plus"))
		return;

	$('.shop-item-minus').click(function ()
	{
		var
			item   = parseInt($(this).attr('item')),
			$input = $(this).parent().find('input'),
			count  = parseInt($input.val()) - 1;

		count = count < 1 ? 1 : count;
		$input.val(count);
		$input.change();
		$('#shop-cart-add-link').attr("onclick", "location.href='/shop/cart/add/" + item + "/" + count + "'");

		return false;
	});

	$('.shop-item-plus').click(function ()
	{
		var item = parseInt($(this).attr('item'));
		var max = parseInt($(this).attr('max'));
		var $input = $(this).parent().find('input');
		var count = parseInt($input.val()) + 1;
		if (count <= max)
		{
			$input.val(count);
			$input.change();
			$('#shop-cart-add-link').attr("onclick", "location.href='/shop/cart/add/" + item + "/" + count + "'");
		}

		return false;
	});
});

// Скроллер вверх
$(document).ready(function()
{
	$(window).scroll(function()
	{
		if ($(this).scrollTop() > 0)
		{
			$('#scroller').fadeIn();
		}
		else
		{
			$('#scroller').fadeOut();
		}
	});

	$('#scroller').click(function()
	{
		$('body, html').animate({
			scrollTop: 0
		}, 400);

		return false;
	});
});