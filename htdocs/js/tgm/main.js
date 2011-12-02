function Submenu( $MenuObject, $SubmenuObject )
{
	this.$menuObject = $MenuObject;
	this.$object = $SubmenuObject;

	this.$object.css( 'left', this.$menuObject.offset( ).left + this.$menuObject.width( ) - this.$object.width( ) - 16 )
					.css( 'top', this.$menuObject.offset( ).top + this.$menuObject.height( ) );
	
	this.show = function( )
	{
		$( '.submenu' ).hide( );

		this.$object.show( );
	}
	
	this.hide = function( Event )
	{
		var event = Event || window.event;
		var relatedTarget = event.relatedTarget || event.fromElement;
		
		// Скрываем только если переходит за пределы менюшки		
		if( relatedTarget.tagName == 'A' || relatedTarget.className == 'submenu' )
		{
			return;
		}

		this.$object.hide( );
	}
}