<script type="text/javascript" src="../js/jquery.js"></script>

<script type="text/javascript" src="../js/jQueryRotate.js"></script>

<img src='safe_roll.png' id=ima>


<script>

var isrot = false;
var ang = 0;
var xm = 0;

$('#ima').rotate({
	bind:
		{
			mouseover:function(){
				$(this).rotate({animateTo:360})},
			mouseout:function(){
				$(this).rotate({animateTo:0})}
		}
});
</script>