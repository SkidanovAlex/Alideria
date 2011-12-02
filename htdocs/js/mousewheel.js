// �������� ������� mousewheel
function wheel(event){
        var delta = 0;
        if (!event) event = window.event; // ������� IE.
        // ��������� ��������������� delta
        if (event.wheelDelta) { 
	   			// IE, Opera, safari, chrome - ��������� ������ ����� 120
                delta = event.wheelDelta/120;
        } else if (event.detail) { 
	   			// Mozilla, ��������� ������ ����� 3
                delta = -event.detail/3;
        }
		// �������������� ������� ��������� mousewheel
        if (delta && typeof handle == 'function') {
                handle(delta);
				// �������� ������� ������� - ������� ����������� (�������� ����).
                if (event.preventDefault)
                        event.preventDefault();
                event.returnValue = false; // ��� IE
        }
}

// ������������� ������� mousewheel
if (window.addEventListener) // mozilla, safari, chrome
	window.addEventListener('DOMMouseScroll', wheel, false);
// IE, Opera.
window.onmousewheel = document.onmousewheel = wheel;