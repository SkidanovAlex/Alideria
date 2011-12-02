var smile_call_back_div = 0;

function smiles( q )
{
	smile_call_back_div = q;
	window.open('smiles.php','_blank','toolbar=no,status=no,menubar=no,scrollbars=yes,width=480,height=280,resizeble=no');
}

function smile_call_back( s )
{
	if( smile_call_back_div ) f1( eval( 'document.' + smile_call_back_div ), s, '' );
}

function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}

function f1(moo,op,cl)
{
	var txtarea = moo;

	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? op + caretPos.text + cl + ' ' : op + caretPos.text + cl;
		txtarea.focus();
		storeCaret( txtarea );
		txtarea.focus();
	}
	else if(typeof(txtarea.selectionStart)=="number")
    {
      var start = txtarea.selectionStart;
      var end = txtarea.selectionEnd;

      var rs = txtarea.value.substr(start,end-start);
      txtarea.value = txtarea.value.substr(0,start)+ op + rs + cl +txtarea.value.substr(end);
      txtarea.setSelectionRange(start,end+op.length+cl.length);

      txtarea.focus();
      storeCaret( txtarea );
      txtarea.focus();
    }
	else
	{
		txtarea.value  += op + cl;
		txtarea.focus();
	}
}
function f2(txt,moo)
{
	var txtarea = txt;
	
	if (txtarea.createTextRange && txtarea.caretPos) {
		if( txtarea.value.length > 0 ) if( txtarea.value.charAt( txtarea.value.length - 1 ) != '\n' ) txtarea.value += '\n';
		txtarea.value = txtarea.value + '(æ)' + moo + '(!æ)' + '\n';
		txtarea.focus();
	} else {
		txtarea.value = txtarea.value + '(æ)' + moo + '(!æ)' + '\n';
		txtarea.focus();
	}
}

function insbtn(nm,wd,tx,op,cl)
{
	var st;
	
	op = "'" + op + "'";
	cl = "'" + cl + "'";
	st = '<td style="cursor: pointer" width=' + wd + ' onClick="f1(document.' + nm + ',' + op + ',' + cl + ');" align=center valign=center>' + tx + '</td>';
	document.write( st );
}

function inssmilebtn(moo,i)
{
	var st;
	var la = ( i < 10 ) ? "0"+i : i;
	
	st = "<a style=\"cursor: pointer\" onClick=\"f1(document."+moo+",'[SMILE"+i+"]','')\"><img border=0 src=smiles/"+la+".gif></a>";
	document.write( st );
}
