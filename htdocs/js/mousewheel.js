// Основная Функция mousewheel
function wheel(event){
        var delta = 0;
        if (!event) event = window.event; // Событие IE.
        // Установим кроссбраузерную delta
        if (event.wheelDelta) { 
	   			// IE, Opera, safari, chrome - кратность дельта равна 120
                delta = event.wheelDelta/120;
        } else if (event.detail) { 
	   			// Mozilla, кратность дельта равна 3
                delta = -event.detail/3;
        }
		// Вспомогательня функция обработки mousewheel
        if (delta && typeof handle == 'function') {
                handle(delta);
				// Отменяет текущее событие - событие поумолчанию (скролинг окна).
                if (event.preventDefault)
                        event.preventDefault();
                event.returnValue = false; // для IE
        }
}

// Инициализация события mousewheel
if (window.addEventListener) // mozilla, safari, chrome
	window.addEventListener('DOMMouseScroll', wheel, false);
// IE, Opera.
window.onmousewheel = document.onmousewheel = wheel;