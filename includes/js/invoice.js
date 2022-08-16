/* global wp, jQuery */
/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	
		$('.ajax').on('click', function(e){
// 			alert('yes');
			var oid = $(this).attr('my-oid');

			$.post('https://yuezers.com/cedcoss/wp-admin/admin-ajax.php', {
				action:'yue_invoice',
				oid: oid,
				type: 'POST',
			}, function(response) {
// 			console.log(response);
// 			console.log($('#hellon').html(response));
				var specialElementHandlers = {
					'#editor': function (element,renderer) {
						return true;
					}
				};
	
				var doc = new jsPDF();
				doc.fromHTML(response, 15, 15, {
					'width': 170,'elementHandlers': specialElementHandlers
				});
				doc.save('sample-file.pdf');	
			});
			
		});
	
}( jQuery ) );
